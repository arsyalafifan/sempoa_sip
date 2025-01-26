<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\master\Rombel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RombelController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Rombel';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-10');

        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);  

            try {
                $rombel = DB::table('tbmrombel')
                    ->select('tbmrombel.*')
                    ->where('tbmrombel.dlt', '=', DB::raw("'0'"))
                    ->where(function($query) use ($search)
                    {
                        if (!is_null($search) && $search!='') {
                            $query->where(DB::raw('tbmrombel.norombel || tbmrombel.namarombel'), 'ilike', '%'.$search.'%');
                        }
                    })->orderBy('tbmrombel.rombelid');

                $count = $rombel->count();
                $data = $rombel->skip($page)->take($perpage)->get();
                
            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'rombel retrieved successfully.');
        }

        return view(
            'master.rombel.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('rombel.create'), 
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $this->authorize('add-10');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
        return view('master.rombel.create', [
            'page' => $this->page, 
            'child' => 'Tambah Data', 
            'masterurl' => route('rombel.index'), 
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->authorize('add-10');

        try
        {
            $user = auth('sanctum')->user();
            $model = new Rombel();

            DB::beginTransaction();

            $model->norombel = $request->norombel;
            $model->namarombel = $request->namarombel;
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('rombel.index')
            ->with('success', 'Data rombel berhasil ditambah.', ['page' => $this->page]);
        }catch(\Throwable $th)
        {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $this->authorize('view-10');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataRombel = Rombel::where('rombelid', $id)->firstOrFail();

        return view(
            'master.rombel.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('rombel.index'), 
                'rombel' => $dataRombel, 
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $this->authorize('edit-10');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $dataRombel = Rombel::where('rombelid', $id)->firstOrFail();

        return view(
            'master.rombel.edit', 
            [
                'page' => $this->page, 
                'createbutton' => false, 
                'createurl' => route('rombel.create'), 
                'child' => 'Ubah Data', 
                'masterurl' => route('rombel.index'), 
                'rombel' => $dataRombel,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $this->authorize('edit-10');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $rombel = Rombel::where('rombelid', $id)
            ->where('dlt', 0)
            ->first();
            $rombel->norombel = $request->norombel;
            $rombel->namarombel = $request->namarombel;

            $rombel->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $rombel->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('rombel.index')
                ->with('success', 'Data rombel berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // $this->authorize('delete-10');

        $user = auth('sanctum')->user();

        $rombel = Rombel::find($id);

        $rombel->dlt = '1';

        $rombel->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $rombel->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'rombel deleted successfully.',
        ], 200);
    }

    public function getnextno()
    {
        $data = DB::table('tbmrombel')
        ->select(DB::raw('max(cast(replace(tbmrombel.norombel, \'.\', \'\') as int)) + 1 as kode'))
        ->where('tbmrombel.dlt', '0')
        ->get();
        
        $kode = 1;
        if ($data[0]->kode != null) $kode = $data[0]->kode;

        $nextno = '1';
        if (isset($kode)) {
            $nextno = $kode;
        }
        $nextno = str_pad($nextno, 3, '0', STR_PAD_LEFT);
        $nextno = $nextno . '.';

        return $nextno;
    }
}
