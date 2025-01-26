<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master\Struk\CreateRequest;
use App\Http\Requests\master\Struk\UpdateRequest;
use App\Models\master\Struk;

class StrukController extends BaseController
{

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Struk';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-25');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $struk = [];
        // $prov = [];

        if($request->ajax())
        {    
            // $provid = $request->provid;
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);        
            try {

                $struk = DB::table('tbmstruk')
                ->select('tbmstruk.*')
                ->where('tbmstruk.dlt', '0')
                ->where(function($query) use ($search)
                {
                    if (!is_null($search) && $search!='') {
                        $query->where(DB::raw('tbmstruk.strukkode'), 'ilike', '%'.$search.'%');
                        $query->orWhere(DB::raw('tbmstruk.struknama'), 'ilike', '%'.$search.'%');
                    }
                })
                ->orderBy('tbmstruk.strukkode');

                $count = $struk->count();
                $data = $struk->skip($page)->take($perpage)->get();
            }
            catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'Struk retrieved successfully.');  
        }

        return view('master.struk.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('struk.create'), 'struk' => $struk]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-25');

        $strukkode = $this->getnextno();

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);

        return view('master.struk.create', 
                    [
                        'page' => $this->page, 'child' => 'Tambah Data', 'masterurl' => route('struk.index'), 'strukkode' => $strukkode
                    ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {

        $this->authorize('add-25');

        try{
            $user = auth('sanctum')->user();

            $model = new Struk;

            DB::beginTransaction();
            $tahun = Date('Y');
            $model->strukkode = $request->strukkode;
            $model->struknama = $request->struknama;
            $model->tahun = $tahun;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('struk.index')
            ->with('success', 'Data struk berhasil ditambah.', ['page' => $this->page]);
        }catch (\Throwable $th) {
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
        $this->authorize('view-25');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataStruk = Struk::where('strukid', $id)->firstOrFail();

        return view('master.struk.show', ['page' => $this->page, 'child' => 'Lihat Data', 'masterurl' => route('struk.index'), 'struk' => $dataStruk]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Struk $StrukModel, $id)
    {
        $this->authorize('edit-25');

        Log::channel('disnaker')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $struk = $StrukModel->where('strukid', $id)->firstOrFail();

        return view('master.struk.edit', 
                    [
                        'page' => $this->page, 'createbutton' => true, 'createurl' => route('struk.create'), 'child' => 'Ubah Data', 'masterurl' => route('struk.index'), 'struk' => $struk
                    ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $this->authorize('edit-25');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $struk = Struk::where('strukid', $id)
            ->where('dlt', 0)
            ->first();
            $struk->strukkode = $request->strukkode;
            $struk->struknama = $request->struknama;
            $tahun = Date('Y');
            $struk->tahun = $tahun;

            $struk->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $struk->save();

            DB::commit();
        }catch(QueryException $e){
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('struk.index')
                ->with('success', 'Data berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-25');

        $user = auth('sanctum')->user();

        $struk = Struk::find($id);

        $struk->dlt = '1';

        $struk->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $struk->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Struk deleted successfully.',
        ], 200);

    }

    public function getnextno()
    {
        $data = DB::table('tbmstruk')
        ->select(DB::raw('max(cast(replace(tbmstruk.strukkode, \'.\', \'\') as bigint)) + 1 as kode'))
        ->where('tbmstruk.dlt', '0')
        ->get();
        
        $kode = 1;
        if ($data[0]->kode != null) $kode = $data[0]->kode;

        $nextno = '1';
        if (isset($kode)) {
            $nextno = $kode;
        }
        $nextno = str_pad($nextno, 1, '0', STR_PAD_LEFT);
        $nextno = $nextno . '.';

        return $nextno;
    }
}
