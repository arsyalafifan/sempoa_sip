<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master\Pegawai\CreateRequest;
use App\Models\master\DetailPegawai;
use App\Models\master\Sekolah;
use App\Models\master\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class PegawaiController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Pegawai';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-5');

        // dd(auth('sanctum')->user()->sekolahid);
        // dd(Auth::user()->isSekolah());
        Log::channel('mibedil')->info('Halaman '.$this->page);

        $sekolah = [];
        $pegawai = [];
        $kota = [];
        $kecamatan = [];

        if($request->ajax())
        {
            $search = $request->search;
            $kotaid = $request->kotaid;
            $kecamatanid = $request->kecamatanid;
            $jenjang = $request->jenjang;
            $jenis = $request->jenis;
            $sekolahid = Auth::user()->isSekolah() ? auth('sanctum')->user()->sekolahid : $request->sekolahid;
            $unit = $request->unit;

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $pegawai = DB::table('tbmpegawai')
                        ->leftJoin('tbmsekolah', function($join)
                        {
                            $join->on('tbmsekolah.sekolahid', '=', 'tbmpegawai.sekolahid');
                            $join->on('tbmsekolah.dlt','=',DB::raw("'0'"));
                        })
                        ->leftJoin('tbmkota', function($join)
                        {
                            $join->on('tbmkota.kotaid', '=', 'tbmsekolah.kotaid');
                            $join->on('tbmkota.dlt','=',DB::raw("'0'"));
                        })
                        ->leftJoin('tbmkecamatan', function($join)
                        {
                            $join->on('tbmkecamatan.kecamatanid', '=', 'tbmsekolah.kecamatanid');
                            $join->on('tbmkecamatan.dlt','=',DB::raw("'0'"));
                        })
                        ->select('tbmpegawai.*', 'tbmsekolah.sekolahid', 'tbmsekolah.namasekolah')
                        ->where('tbmpegawai.dlt', '0')
                        ->where(function($query){
                            if(Auth::user()->isSekolah()) $query->where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid);
                        })
                        ->where(function ($query) use ($unit, $sekolahid, $kotaid, $kecamatanid, $jenjang, $search) {
                                if (!is_null($unit) && $unit != '') $query->where('tbmpegawai.unit', $unit);
                                if (!is_null($kotaid) && $kotaid != '') $query->where('tbmsekolah.kotaid', $kotaid);
                                if (!is_null($kecamatanid) && $kecamatanid != '') $query->where('tbmsekolah.kecamatanid', $kecamatanid);
                                if (!is_null($jenjang) && $jenjang != '') $query->where('tbmsekolah.jenjang', $jenjang);
                                if (!is_null($sekolahid) && $sekolahid != '') $query->where('tbmpegawai.sekolahid', $sekolahid);

                                if (!is_null($search) && $search!='') {
                                    // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                                    $query->where(DB::raw('tbmpegawai.nama'), 'ilike', '%'.$search.'%');
                                    $query->orWhere(DB::raw('tbmpegawai.nip'), 'ilike', '%'.$search.'%');
                                    // $query->where('tbmpegawai.sekolahid', $sekolahid);
                                }
                                // if(Auth::user()->isSekolah()) $query->where('tbmpegawai.sekolahid', auth('sanctum')->user()->sekolahid);
                        });

                $count = $pegawai->count();
                $data = $pegawai->skip($page)->take($perpage)->get();
            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'Pegawai retrieved successfully.');  
        }

        $kota = DB::table('tbmkota')
            ->select('tbmkota.kotaid', 'tbmkota.kodekota', 'tbmkota.namakota')
            ->where('tbmkota.dlt', 0)
            ->orderBy('tbmkota.kodekota')
            ->get()
        ;

        $kecamatan = DB::table('tbmkecamatan')
            ->select('tbmkecamatan.kecamatanid', 'tbmkecamatan.kodekec', 'tbmkecamatan.namakec')
            ->where('tbmkecamatan.dlt', 0)
            ->orderBy('tbmkecamatan.kodekec')
            ->get()
        ;

        $sekolah = DB::table('tbmsekolah')
        ->select('tbmsekolah.sekolahid', 'tbmsekolah.namasekolah')
        ->where('tbmsekolah.dlt', 0)
        ->orderBy('tbmsekolah.namasekolah')
        ->get()
        ;

        $golongan = DB::table('tbmgolongan')
        ->select('tbmgolongan.golonganid', 'tbmgolongan.golongankode')
        ->where('tbmgolongan.dlt', 0)
        ->orderBy('tbmgolongan.golonganid')
        ->get()
        ;

        $jabatan = DB::table('tbmjabatan')
        ->select('tbmjabatan.jabatanid', 'tbmjabatan.namajabatan')
        ->where('tbmjabatan.dlt', 0)
        ->orderBy('tbmjabatan.jabatanid')
        ->get()
        ;

        $userSekolah = Sekolah::where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid == null ? -1 : auth('sanctum')->user()->sekolahid)->first();

        return view(
            'master.pegawai.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' =>  isset($unit) == enum::UNIT_OPD ? route('pegawai.create') : route('pegawai.createWithSekolah',['sekolahId' => ':id']), 
                'pegawai' => $pegawai, 
                'kota' => $kota,
                'kecamatan' => $kecamatan,
                'sekolah' => $sekolah,
                'golongan' => $golongan,
                'jabatan' => $jabatan,
                'isSekolah' => Auth::user()->isSekolah(),
                'userSekolah' => $userSekolah
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id=null)
    {   

        $this->authorize('add-5');

        // if(Auth::user()->isSekolah() && $id != auth('sanctum')->user()->sekolahid) return $this->sendError('Anda tidak berhak mengakses halaman ini', [], 403);
        if(Auth::user()->isSekolah() && $id != auth('sanctum')->user()->sekolahid) return view('errors.403');

        if (is_null($id)) {
            Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
            return view(
                'master.pegawai.create', 
                [
                    'page' => $this->page, 
                    'child' => 'Tambah Data', 
                    'masterurl' => route('pegawai.index'), 
                    'sekolah' =>null,
                ]
            );
        }else{
            Log::channel('mibedil')->info('Halaman Tambah '.$this->page, ['id' => $id]);

            $sekolah = Sekolah::where('sekolahid', $id)
            ->where('dlt',0)
            ->firstOrFail();

            return view(
                'master.pegawai.create', 
                [
                    'page' => $this->page, 
                    'child' => 'Tambah Data', 
                    'masterurl' => route('pegawai.index'), 
                    'sekolah' =>$sekolah,
                ]
            );
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $this->authorize('add-5');

        $user = auth('sanctum')->user();

        try{
            $user = auth('sanctum')->user();
            $model = new Pegawai();

            DB::beginTransaction();

            $sekolahid = $request->sekolahid;

            if($sekolahid == null || $sekolahid == ''){
                $model->unit = enum::UNIT_OPD;
            }else{
                $model->unit = enum::UNIT_SEKOLAH;
            }
            $tglnow = date('Y-m-d');
            $model->sekolahid = $sekolahid;
            $model->nama = $request->nama;
            $model->jenispeg = $request->jenispeg;
            $model->nip = $request->nip;
            $model->jabatan = $request->jabatan;
            $model->npwp = $request->npwp;
            $model->ketjabatan = $request->ketjabatan;
            $model->tgllahir = $request->tgllahir;
            $model->judulsk = $request->judulsk;
            $model->nosk = $request->nosk;
            $model->tgl_sk = $request->tgl_sk;
            $model->status = isset($request->status) ? 1 : 0;
            // $filename = $tglnow.'_'.rand(1,1000).'_'.$request->nama.'.png';
            
            // if ($request->file('file_ttd') != '' || $request->file('file_ttd') != null){
            //     $model->file_ttd = $request->file('file_ttd')->storeAs('pegawai', $filename);
            // }else{
            $model->file_ttd = 'pegawai/QRcode1.png';
            // }

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            if ($model->save()) {
                
            }

            // dd($request->all());

            DB::commit();

            return redirect()->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambah.', ['page' => $this->page]);
        }catch(\Throwable $th)
        {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    public function storedetailpegawai(Request $request)
    {
        $this->authorize('add-5');

        $user = auth('sanctum')->user();

        try {

            $model = new DetailPegawai;
 
            DB::beginTransaction();

            $model->pegawaiid = $request->pegawaiid;
            $model->tahun = $request->tahun;
            $model->golpegawaiid = $request->golpegawaiid;
            $model->jenisjab = $request->jenisjab;
            $model->golruangberkalaid = $request->golruangberkalaid;
            $model->jabatanid = $request->jabatanid;
            $model->eselon = $request->eselon;
            $model->msgajiberkalathn = $request->msgajiberkalathn;
            $model->msgajiberkalabln = $request->msgajiberkalabln;
            $model->tmtberkala = $request->tmtberkala;
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
            $model->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail pegawai added successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
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
        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataPegawai = Pegawai::where('pegawaiid', $id)
        ->where('dlt', 0)
        ->first();

        return view('master.pegawai.show', ['page' => $this->page, 'child' => 'Lihat Data', 'masterurl' => route('pegawai.index'), 'pegawai' => $dataPegawai]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->authorize('edit-5');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $pegawai = Pegawai::where('pegawaiid', $id)
        ->where('dlt', 0)
        ->first();

        // dd($pegawai);

        return view(
            'master.pegawai.edit', 
            [
                'page' => $this->page, 
                'child' => 'Ubah Data', 
                'masterurl' => route('pegawai.index'), 
                'pegawai' => $pegawai,
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
        $this->authorize('edit-5');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::where('pegawaiid', $id)
            ->where('dlt', 0)
            ->first();
            
            $pegawai->nama = $request->nama;
            $pegawai->jenispeg = $request->jenispeg;
            $pegawai->nip = $request->nip;
            $pegawai->jabatan = $request->jabatan;
            $pegawai->npwp = $request->npwp;
            $pegawai->ketjabatan = $request->ketjabatan;
            $pegawai->tgllahir = $request->tgllahir;
            $pegawai->judulsk = $request->judulsk;
            $pegawai->nosk = $request->nosk;
            $pegawai->tgl_sk = $request->tgl_sk;
            $pegawai->status = isset($request->status) ? 1 : 0;
            $pegawai->pcedit = $request->ip();
            $pegawai->opedit = $user->login;

            $pegawai->save();

            DB::commit();
        }catch(QueryException $e){
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('pegawai.index')
                ->with('success', 'Data berhasil diubah.', ['page' => $this->page]);
    }

    public function updatedetailpegawai(Request $request, $id)
    {
        $this->authorize('edit-5');

        $user = auth('sanctum')->user();

        try {

            $model = DetailPegawai::where('pegawaiid', $id)->where('dlt', 0)->first();
 
            DB::beginTransaction();

            // $model->pegawaiid = $request->pegawaiid;
            $model->tahun = $request->tahun;
            $model->golpegawaiid = $request->golpegawaiid;
            $model->jenisjab = $request->jenisjab;
            $model->golruangberkalaid = $request->golruangberkalaid;
            $model->jabatanid = $request->jabatanid;
            $model->eselon = $request->eselon;
            $model->msgajiberkalathn = $request->msgajiberkalathn;
            $model->msgajiberkalabln = $request->msgajiberkalabln;
            $model->tmtberkala = $request->tmtberkala;
            $model->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
            $model->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail pegawai updated successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $this->authorize('delete-5');

        $user = auth('sanctum')->user();

        $pegawai = Pegawai::where('pegawaiid', $id)
        ->where('dlt', 0)
        ->first();

        $pegawai->dlt = '1';

        $pegawai->pcedit = $request->ip();
        $pegawai->opedit = $user->login;

        $pegawai->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Pegawai Berhasil Dihapus.',
        ], 200);
    }

    public function destroydetailpegawai(Request $request, $id)
    {

        $this->authorize('delete-5');

        $user = auth('sanctum')->user();

        $detailpegawaiid = DetailPegawai::where('detailpegawaiid', $id)
        ->where('dlt', 0)
        ->first();

        $detailpegawaiid->dlt = '1';

        $detailpegawaiid->pcedit = $request->ip();
        $detailpegawaiid->opedit = $user->login;

        $detailpegawaiid->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'detail pegawaiegawai Berhasil Dihapus.',
        ], 200);
    }

    public function showdetailpegawai(Request $request, $id)
    {

        $this->authorize('view-5');

        if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $detailpegawai = DB::table('tbmdetailpegawai')
                    ->join('tbmpegawai', function($join){
                        $join->on('tbmpegawai.pegawaiid', '=', 'tbmdetailpegawai.pegawaiid');
                        $join->on('tbmpegawai.dlt', '=', DB::raw("'0'"));
                    })
                    ->leftJoin('tbmjabatan', function($join){
                        $join->on('tbmjabatan.jabatanid', '=', 'tbmdetailpegawai.jabatanid');
                        $join->on('tbmjabatan.dlt', '=', DB::raw("'0'"));
                    })
                    ->select('tbmdetailpegawai.*', 'tbmjabatan.namajabatan')
                    ->where('tbmdetailpegawai.pegawaiid', $id)
                    ->where('tbmdetailpegawai.dlt', 0)
                    ->orderBy('tbmdetailpegawai.detailpegawaiid')
                ;

                $count = $detailpegawai->count();
                $data = $detailpegawai->skip($page)->take($perpage)->get();

            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count,
            ], 'Data retrieved successfully.'); 

        }
    }
}
