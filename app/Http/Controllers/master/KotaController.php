<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master\Kota\CreateRequest;
use App\Http\Requests\master\Kota\UpdateRequest;
use App\Models\master\Kota;

class KotaController extends BaseController
{

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Kota/Kabupaten';
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
        $kota = [];
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

                $kota = DB::table('tbmkota')
                ->select('tbmkota.*')
                ->where('tbmkota.dlt', '0')
                ->where(function($query) use ($search)
                {
                    // if (!is_null($provid) && $provid!='') $query->where('tbmkota.provid', $provid);
                    if (!is_null($search) && $search!='') {
                        // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                        $query->where(DB::raw('tbmkota.kodekota'), 'ilike', '%'.$search.'%');
                        $query->orWhere(DB::raw('tbmkota.namakota'), 'ilike', '%'.$search.'%');
                    }
                })
                ->orderBy('tbmkota.kodekota');

                $count = $kota->count();
                $data = $kota->skip($page)->take($perpage)->get();
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
            ], 'Kota retrieved successfully.');  
        }


        // $prov = DB::table('tbmprov')
        // ->select('tbmprov.provid', 'tbmprov.kodeprov', 'tbmprov.namaprov')
        // ->where('tbmprov.dlt', 0)
        // ->orderBy('tbmprov.kodeprov')
        // ->get();

        return view('master.kota.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('kota.create'), 'kota' => $kota]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-1');

        $kodekota = $this->getnextno();

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);

        // $prov = DB::table('tbmprov')
        // ->select('tbmprov.provid', 'tbmprov.kodeprov', 'tbmprov.namaprov')
        // ->where('tbmprov.dlt', 0)
        // ->orderBy('tbmprov.kodeprov')
        // ->get();

        return view('master.kota.create', 
                    [
                        'page' => $this->page, 'child' => 'Tambah Data', 'masterurl' => route('kota.index'), 'kodekota' => $kodekota
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
        $this->authorize('add-1');

        try{
            $user = auth('sanctum')->user();

            $model = new Kota;

            DB::beginTransaction();

            $model->kodekota = $request->kodekota;
            $model->namakota = $request->namakota;
            $model->jenis = $request->jenis;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('kota.index')
            ->with('success', 'Data kota berhasil ditambah.', ['page' => $this->page]);
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
    public function show(Kota $kota, $id)
    {
        $this->authorize('view-1');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataKota = $kota->where('kotaid', $id)->firstOrFail();

        return view('master.kota.show', ['page' => $this->page, 'child' => 'Lihat Data', 'masterurl' => route('kota.index'), 'kota' => $dataKota]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Kota $KotaModel, $id)
    {
        $this->authorize('edit-1');

        Log::channel('disnaker')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $kota = $KotaModel->where('kotaid', $id)->firstOrFail();

        return view('master.kota.edit', 
                    [
                        'page' => $this->page, 'createbutton' => true, 'createurl' => route('kota.create'), 'child' => 'Ubah Data', 'masterurl' => route('kota.index'), 'kota' => $kota
                    ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Kota $KotaModel, $id)
    {
        $this->authorize('edit-1');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $kota = $KotaModel->find($id);
            $kota->kodekota = $request->kodekota;
            $kota->namakota = $request->namakota;
            $kota->jenis = $request->jenis;

            $kota->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $kota->save();

            DB::commit();
        }catch(QueryException $e){
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('kota.index')
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
        $this->authorize('delete-1');

        $user = auth('sanctum')->user();

        $kota = Kota::find($id);

        // if (count($kota->kecamatan)>0) 
        //     return response([
        //         'success' => false,
        //         'data'    => 'Data tidak bisa dihapus karena sudah digunakan pada menu Kecamatan'
        //     ], 200);

        $kota->dlt = '1';

        $kota->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $kota->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Kota deleted successfully.',
        ], 200);

    }

    public function getnextno()
    {
        $data = DB::table('tbmkota')
        ->select(DB::raw('max(cast(replace(tbmkota.kodekota, \'.\', \'\') as int)) + 1 as kode'))
        ->where('tbmkota.dlt', '0')
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
