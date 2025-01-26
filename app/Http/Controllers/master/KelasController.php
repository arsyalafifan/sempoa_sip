<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\master\Kelas;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KelasController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Kelas';
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
                $kelas = DB::table('tbmkelas')
                    ->select('tbmkelas.*')
                    ->where('tbmkelas.dlt', '=', DB::raw("'0'"))
                    ->where(function($query) use ($search)
                    {
                        if (!is_null($search) && $search!='') {
                            $query->where(DB::raw('tbmkelas.nokelas || tbmkelas.namakelas'), 'ilike', '%'.$search.'%');
                        }
                    })->orderBy('tbmkelas.kelasid');

                $count = $kelas->count();
                $data = $kelas->skip($page)->take($perpage)->get();
                
            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'kelas retrieved successfully.');
        }

        return view(
            'master.kelas.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('kelas.create'), 
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
        
        return view('master.kelas.create', [
            'page' => $this->page, 
            'child' => 'Tambah Data', 
            'masterurl' => route('kelas.index'), 
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
            $model = new Kelas();

            DB::beginTransaction();

            $model->nokelas = $request->nokelas;
            $model->namakelas = $request->namakelas;
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil ditambah.', ['page' => $this->page]);
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

        $dataKelas = Kelas::where('kelasid', $id)->firstOrFail();

        return view(
            'master.kelas.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('kelas.index'), 
                'kelas' => $dataKelas, 
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

        $dataKelas = Kelas::where('kelasid', $id)->firstOrFail();

        return view(
            'master.kelas.edit', 
            [
                'page' => $this->page, 
                'createbutton' => false, 
                'createurl' => route('kelas.create'), 
                'child' => 'Ubah Data', 
                'masterurl' => route('kelas.index'), 
                'kelas' => $dataKelas,
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

            $kelas = Kelas::where('kelasid', $id)
            ->where('dlt', 0)
            ->first();
            $kelas->nokelas = $request->nokelas;
            $kelas->namakelas = $request->namakelas;

            $kelas->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $kelas->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('kelas.index')
                ->with('success', 'Data kelas berhasil diubah.', ['page' => $this->page]);
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

        $kelas = Kelas::find($id);

        $kelas->dlt = '1';

        $kelas->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $kelas->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'kelas deleted successfully.',
        ], 200);
    }

    public function getnextno()
    {
        $data = DB::table('tbmkelas')
        ->select(DB::raw('max(cast(replace(tbmkelas.nokelas, \'.\', \'\') as int)) + 1 as kode'))
        ->where('tbmkelas.dlt', '0')
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
