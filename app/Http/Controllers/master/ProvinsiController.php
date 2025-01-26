<?php

namespace App\Http\Controllers\master;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\master\Kota;
use App\Models\master\Provinsi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ProvinsiController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Provinsi';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-1');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $prov = [];
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
                // $kota = DB::table('tbmkota')
                //         ->join('tbmprov', function($join)
                //         {
                //             $join->on('tbmprov.provid', '=', 'tbmkota.provid');
                //             $join->on('tbmprov.dlt','=',DB::raw("'0'"));
                //         })
                //         ->select('tbmkota.*', 'tbmprov.kodeprov', 'tbmprov.namaprov')
                //         ->where('tbmkota.dlt', '0')
                //         ->where(function($query) use ($provid, $search)
                //         {
                //             if (!is_null($provid) && $provid!='') $query->where('tbmkota.provid', $provid);
                //             if (!is_null($search) && $search!='') {
                //                 // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                //                 $query->where(DB::raw('tbmkota.kodekota'), 'ilike', '%'.$search.'%');
                //                 $query->orWhere(DB::raw('tbmkota.namakota'), 'ilike', '%'.$search.'%');
                //             }
                //         })
                //         ->orderBy('tbmkota.kodekota');

                $prov = DB::table('tbmprovinsi')
                ->select('tbmprovinsi.*')
                ->where('tbmprovinsi.dlt', '0')
                ->where(function($query) use ($search)
                {
                    // if (!is_null($provid) && $provid!='') $query->where('tbmkota.provid', $provid);
                    if (!is_null($search) && $search!='') {
                        // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                        $query->where(DB::raw('tbmprovinsi.kodeprov'), 'ilike', '%'.$search.'%');
                        $query->orWhere(DB::raw('tbmprovinsi.namaprov'), 'ilike', '%'.$search.'%');
                    }
                })
                ->orderBy('tbmprovinsi.kodeprov');

                $count = $prov->count();
                $data = $prov->skip($page)->take($perpage)->get();
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
            ], 'Provinsi retrieved successfully.');  
        }


        // $prov = DB::table('tbmprov')
        // ->select('tbmprov.provid', 'tbmprov.kodeprov', 'tbmprov.namaprov')
        // ->where('tbmprov.dlt', 0)
        // ->orderBy('tbmprov.kodeprov')
        // ->get();

        return view(
            'master.prov.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('prov.create'), 
                'prov' => $prov
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
        $this->authorize('add-1');

        $kodeprov = $this->getnextno();

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);

        // $prov = DB::table('tbmprov')
        // ->select('tbmprov.provid', 'tbmprov.kodeprov', 'tbmprov.namaprov')
        // ->where('tbmprov.dlt', 0)
        // ->orderBy('tbmprov.kodeprov')
        // ->get();

        return view(
            'master.prov.create', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('prov.index'), 
                'kodeprov' => $kodeprov
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
        $this->authorize('add-1');

        try{
            $user = auth('sanctum')->user();

            $model = new Provinsi;

            DB::beginTransaction();

            $model->kodeprov = $request->kodeprov;
            $model->namaprov = $request->namaprov;
            // $model->jenis = $request->jenis;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('prov.index')
            ->with('success', 'Data provinsi berhasil ditambah.', ['page' => $this->page]);
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
        $this->authorize('view-1');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataProv = Provinsi::where('provid', $id)->firstOrFail();

        return view('master.prov.show', ['page' => $this->page, 'child' => 'Lihat Data', 'masterurl' => route('prov.index'), 'prov' => $dataProv]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('edit-1');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $prov = Provinsi::where('provid', $id)->firstOrFail();

        return view(
            'master.prov.edit', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('prov.create'), 
                'child' => 'Ubah Data', 
                'masterurl' => route('prov.index'), 
                'prov' => $prov
            ]
        );
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
        $this->authorize('edit-1');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $prov = Provinsi::find($id);
            $prov->kodeprov = $request->kodeprov;
            $prov->namaprov = $request->namaprov;

            $prov->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $prov->save();

            DB::commit();
        }catch(QueryException $e){
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('prov.index')
                ->with('success', 'Data berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $this->authorize('delete-1');

        $user = auth('sanctum')->user();

        $prov = Provinsi::find($id);

        // if (count($prov->kecamatan)>0) 
        //     return response([
        //         'success' => false,
        //         'data'    => 'Data tidak bisa dihapus karena sudah digunakan pada menu Kecamatan'
        //     ], 200);

        $prov->dlt = '1';

        $prov->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $prov->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Prov deleted successfully.',
        ], 200);
    }

    public function getnextno()
    {
        $data = DB::table('tbmprovinsi')
        ->select(DB::raw('max(cast(replace(tbmprovinsi.kodeprov, \'.\', \'\') as int)) + 1 as kode'))
        ->where('tbmprovinsi.dlt', '0')
        // ->where('tbmkota.provid', $parentid)
        ->get();
        
        $kode = 1;
        if ($data[0]->kode != null) $kode = $data[0]->kode;

        $nextno = '1';
        if (isset($kode)) {
            $nextno = $kode;
        }
        $nextno = str_pad($nextno, 4, '0', STR_PAD_LEFT);
        $nextno = $nextno . '.';

        return $nextno;
    }
}
