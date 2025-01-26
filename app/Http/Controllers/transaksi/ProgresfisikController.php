<?php

namespace App\Http\Controllers\transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\master\Perusahaan;
use App\Models\master\Sekolah;
use App\Models\perencanaansarpras\DetailPaguPenganggaran;
use App\Models\perencanaansarpras\DetailPenganggaran;
use App\Models\sarpras\DetailJumlahSarpras;
use App\Models\sarpras\DetailPaguSarprasTersedia;
use App\Models\sarpras\DetailSarprasTersedia;
use App\Models\sarpras\FileDetailJumlahSarpras;
use App\Models\sarpras\SarprasKebutuhan;
use App\Models\sarpras\SarprasTersedia;
use App\Models\transaksi\DetailJumlahPeralatan;
use App\Models\transaksi\DetailLaporan;
use App\Models\transaksi\FileDetailJumlahPeralatan;
use App\Models\verifikasi\StatusKebutuhanSarpras;
use Illuminate\Support\Facades\Auth;

class ProgresfisikController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Progress Fisik';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-27');
        $user = auth('sanctum')->user();

        Log::channel('mibedil')->info('Halaman '.$this->page);

        $progresfisik = [];
        $perusahaan = [];

        if($request->ajax())
        {
            $search = $request->search;
            $kotaid = $request->kotaid;
            $jenis = $request->jenis;
            $jenjang = $request->jenjang;
            $perusahaanid = Auth::user()->isPerusahaan() ? auth('sanctum')->user()->perusahaanid : $request->perusahaanid;
            $sekolahid = Auth::user()->isSekolah() ? auth('sanctum')->user()->sekolahid : $request->sekolahid;

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length', 50);

            try {
                $progresfisik = DB::table('tbdetailpagupenganggaran')
                        ->join('tbdetailpenganggaran', function($join)
                        {
                            $join->on('tbdetailpenganggaran.detailpenganggaranid', '=', 'tbdetailpagupenganggaran.detailpenganggaranid');
                            $join->on('tbdetailpenganggaran.dlt','=',DB::raw("'0'"));
                        })
                        ->join('tbtranssarpraskebutuhan', function($join)
                        {
                            $join->on('tbtranssarpraskebutuhan.sarpraskebutuhanid', '=', 'tbdetailpenganggaran.sarpraskebutuhanid');
                            $join->on('tbtranssarpraskebutuhan.dlt', '=', DB::raw("'0'"));
                            // $join->on('tbtranssarpraskebutuhan.status', '=', DB::raw("'5'"));
                        })
                        ->leftJoin('tbmperusahaan', function($join)
                        {
                            $join->on('tbmperusahaan.perusahaanid', '=', 'tbdetailpagupenganggaran.perusahaanid');
                            $join->on('tbmperusahaan.dlt','=',DB::raw("'0'"));
                        })
                        ->join('tbmsubkeg', function($join)
                        {
                            $join->on('tbmsubkeg.subkegid', '=', 'tbdetailpenganggaran.subkegid');
                            $join->on('tbmsubkeg.dlt','=',DB::raw("'0'"));
                        })
                        ->join('tbmsekolah', function($join){
                            $join->on('tbmsekolah.sekolahid', '=', 'tbtranssarpraskebutuhan.sekolahid');
                            $join->on('tbmsekolah.dlt', '=', DB::raw("'0'"));
                        })
                        // ->leftJoin('tbdetaillaporan', function($join)
                        // {
                        //     $join->on('tbdetaillaporan.detailpaguanggaranid', '=', 'tbdetailpagupenganggaran.detailpaguanggaranid');
                        //     $join->on('tbdetaillaporan.dlt', '=', DB::raw("'0'"));
                        // })
                        ->select(
                            // DB::raw("'0' as progres"),
                            'tbtranssarpraskebutuhan.sarpraskebutuhanid',
                            'tbtranssarpraskebutuhan.jenissarpras',
                            'tbtranssarpraskebutuhan.sekolahid',
                            'tbmsekolah.namasekolah',
                            'tbdetailpenganggaran.detailpenganggaranid',
                            'tbdetailpagupenganggaran.detailpaguanggaranid',
                            'tbdetailpagupenganggaran.jenispagu', 
                            'tbtranssarpraskebutuhan.jeniskebutuhan',
                            'tbmperusahaan.nama', 
                            'tbmsubkeg.subkegnama', 
                            'tbdetailpenganggaran.subdetailkegiatan',
                            'tbdetailpagupenganggaran.nokontrak',
                            'tbdetailpagupenganggaran.nilaikontrak',
                            'tbdetailpagupenganggaran.isselesai',
                            // 'tbdetaillaporan.detaillaporanid',
                            DB::raw('coalesce(tbdetailpagupenganggaran.progres, 0) as progres')
                            // DB::raw('MAX(tbdetaillaporan.progres) as progres')
                        )
                        ->where('tbtranssarpraskebutuhan.status', '>=', enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_PEMBANGUNAN)
                        ->where(function($query){
                            if(Auth::user()->isPerusahaan()) $query->where('tbmperusahaan.perusahaanid', auth('sanctum')->user()->perusahaanid);
                        })
                        ->where(function($query){
                            if(Auth::user()->isSekolah()) $query->where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid);
                        })
                        // ->distinct()
                        // ->whereIn(DB::raw('(tbdetaillaporan.progres)'), function ($query) {
                        //     $query->select(DB::raw('coalesce(tbdetaillaporan.progres, 0) as progres'))
                        //         ->from('tbdetaillaporan')
                        //         ->where('tbdetaillaporan.dlt', '0')
                        //         ->orderBy('tbdetaillaporan.progres', 'desc')
                        //         ->skip(0)->take(1)
                        //         ->groupBy(
                        //             // 'tbdetaillaporan.detailpaguanggaranid',
                        //             'tbdetaillaporan.detaillaporanid'
                        //         );
                        // })
                        ->where(function($query) use ($perusahaanid, $kotaid, $jenis, $jenjang, $sekolahid, $search)
                        {
                            if (!is_null($perusahaanid) && $perusahaanid!='') $query->where('tbdetailpagupenganggaran.perusahaanid', $perusahaanid);
                            if (!is_null($kotaid) && $kotaid!='') $query->where('tbmsekolah.kotaid', $kotaid);
                            if (!is_null($jenis) && $jenis!='') $query->where('tbmsekolah.jenis', $jenis);
                            if (!is_null($jenjang) && $jenjang!='') $query->where('tbmsekolah.jenjang', $jenjang);
                            if (!is_null($sekolahid) && $sekolahid!='') $query->where('tbmsekolah.sekolahid', $sekolahid);

                            if (!is_null($search) && $search!='') {
                                // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                                $query->where(DB::raw('tbdetailpagupenganggaran.jenispagu'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmperusahaan.nama'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbdetailpenganggaran.subdetailkegiatan'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmsubkeg.subkegnama'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbdetailpagupenganggaran.nokontrak'), 'ilike', '%'.$search.'%');
                            }
                        })
                        // ->whereIn(DB::raw('(tbdetaillaporan.detaillaporanid, tbdetaillaporan.tgladd)'), function ($query) {
                        //     $query->select('detailpaguanggaranid', DB::raw('MAX(tgladd)'))
                        //         ->from('tbdetaillaporan')
                        //         ->where('tbdetaillaporan.dlt', '0')
                        //         ->groupBy('detaillaporanid');
                        // })
                        ->where('tbdetailpagupenganggaran.dlt', '0')
                        ->groupBy(
                            'tbdetailpenganggaran.detailpenganggaranid',
                            'tbdetailpagupenganggaran.detailpaguanggaranid',
                            'tbdetailpagupenganggaran.jenispagu', 
                            'tbmperusahaan.nama', 
                            'tbmsubkeg.subkegnama', 
                            'tbdetailpenganggaran.subdetailkegiatan',
                            'tbdetailpagupenganggaran.nokontrak',
                            'tbdetailpagupenganggaran.nilaikontrak',
                            'tbtranssarpraskebutuhan.jeniskebutuhan',
                            'tbtranssarpraskebutuhan.sarpraskebutuhanid',
                            'tbtranssarpraskebutuhan.status',
                            'tbmsekolah.namasekolah',
                            // 'tbdetaillaporan.progres',
                            // 'tbdetaillaporan.detaillaporanid',
                        )
                ;
                $count = $progresfisik->count();
                $data = $progresfisik->skip($page)->take($perpage)->get();

                // $querySum = DB::table('tbdetailpagupenganggaran')
                //     ->select(DB::raw('SUM(nilaipagu) as countpagu'), DB::raw('SUM(nilaikontrak) as countkontrak'))
                //     ->where('tbdetailpagupenganggaran.detailpenganggaranid', $id)
                //     ->where('tbdetailpagupenganggaran.dlt', 0)
                // ;

                // $sumNilaiKontrak = [];
                // foreach ($data as $key => $value) {
                //     $sumNilaiKontrak = $value[$key]->nilaikontrak;
                // }

                return $this->sendResponse([
                    'data' => $data,
                    'count' => $count,
                    // 'sumNilaiKontrak' => $sumNilaiKontrak,
                ], 'progres fisik retrieved successfully.');
            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }  
        }

        $kota = DB::table('tbmkota')
            ->select('tbmkota.kotaid', 'tbmkota.kodekota', 'tbmkota.namakota')
            ->where('tbmkota.dlt', 0)
            ->orderBy('tbmkota.kodekota')
            ->get()
        ;

        $perusahaan = DB::table('tbmperusahaan')
            ->select('tbmperusahaan.perusahaanid', 'tbmperusahaan.nama')
            ->where('tbmperusahaan.dlt', 0)
            ->orderBy('tbmperusahaan.nama')
            ->get()
        ;

        $sekolah = DB::table('tbmsekolah')
            ->select('tbmsekolah.sekolahid', 'tbmsekolah.namasekolah')
            ->where('tbmsekolah.dlt', 0)
            ->orderBy('tbmsekolah.namasekolah')
            ->get()
        ;

        $jenisperalatan = DB::table('tbmjenisperalatan')
            ->select('tbmjenisperalatan.jenisperalatanid', 'tbmjenisperalatan.nama')
            ->where('tbmjenisperalatan.dlt', 0)
            ->get()
        ;

        $userPerusahaan = Perusahaan::where('tbmperusahaan.perusahaanid', auth('sanctum')->user()->perusahaanid == null ? -1 : auth('sanctum')->user()->perusahaanid)->first();
        $userSekolah = Sekolah::where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid == null ? -1 : auth('sanctum')->user()->sekolahid)->first();

        return view(
            'transaksi.progresfisik.index', 
            [
                'page' => $this->page, 
                'createbutton' => false, 
                'kota' => $kota,
                'perusahaan' => $perusahaan,
                'sekolah' => $sekolah,
                'jenisperalatan' => $jenisperalatan,
                'isPerusahaan' => Auth::user()->isPerusahaan(),
                'isSekolah' => Auth::user()->isSekolah(),
                'userPerusahaan' => $userPerusahaan 
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function loadDetailLaporan(Request $request, $id)
    {

        $this->authorize('view-27');

        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $detaillaporan = DB::table('tbdetaillaporan')
                    ->select('tbdetaillaporan.*')
                    ->where('tbdetaillaporan.detailpaguanggaranid', $id)
                    ->where('tbdetaillaporan.dlt', 0)
                    ->orderBy('tbdetaillaporan.detaillaporanid')
                ;

                $count = $detaillaporan->count();
                $data = $detaillaporan->skip($page)->take($perpage)->get();

                return $this->sendResponse([
                    'data' => $data,
                    'count' => $count,
                ], 'Data retrieved successfully.'); 

            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
        }
    }

    public function loadDetailJenisPeralatan(Request $request, $id)
    {
        $this->authorize('view-27');

        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $detailjenisperalatan = DB::table('tbdetailjumlahperalatan')
                    ->select('tbdetailjumlahperalatan.*')
                    ->where('tbdetailjumlahperalatan.detailpenganggaranid', $id)
                    ->where('tbdetailjumlahperalatan.dlt', 0)
                    ->orderBy('tbdetailjumlahperalatan.detailjumlahperalatanid')
                ;

                $count = $detailjenisperalatan->count();
                $data = $detailjenisperalatan->skip($page)->take($perpage)->get();

                return $this->sendResponse([
                    'data' => $data,
                    'count' => $count,
                ], 'Data retrieved successfully.'); 

            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
        }
    }

    public function storeDetailLaporan(Request $request)
    {
        $this->authorize('add-27');

        $user = auth('sanctum')->user();

        try {
                $tglnow = date('Y-m-d');
                DB::beginTransaction();
                // Save multiple and file 
                $modelDetailLaporan = new DetailLaporan;

                $modelDetailLaporan->bulan = $request->bulan;
                $modelDetailLaporan->minggu = $request->minggu;
                $modelDetailLaporan->daritgl = $request->daritgl;
                $modelDetailLaporan->sampaitgl = $request->sampaitgl;
                $modelDetailLaporan->target = $request->target;
                $modelDetailLaporan->progres = $request->progres;
                $modelDetailLaporan->keterangan = $request->keterangan;
                $modelDetailLaporan->detailpaguanggaranid = $request->detailpaguanggaranid;

                $modelDetailLaporan->fill(['opadd' => $user->login, 'pcedit' => $request->ip()]);


                if ($modelDetailLaporan->save()) {
                    $modelDetailPagu = DetailPaguPenganggaran::find($modelDetailLaporan->detailpaguanggaranid);

                    $modelDetailPagu->progres = $request->progres;
                    $modelDetailPagu->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

                    $modelDetailPagu->save();
                }

                DB::commit();
                
                return response([
                    'success' => true,
                    'data'    => 'Success',
                    'message' => 'detail laporan added successfully.',
                ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function updateDetailLaporan(Request $request, $id)
    {
        $this->authorize('edit-27');

        $user = auth('sanctum')->user();

        try {
                $tglnow = date('Y-m-d');
                DB::beginTransaction();
                // Save multiple and file 
                $modelDetailLaporan = DetailLaporan::find($id);

                $modelDetailLaporan->bulan = $request->bulan;
                $modelDetailLaporan->minggu = $request->minggu;
                $modelDetailLaporan->daritgl = $request->daritgl;
                $modelDetailLaporan->sampaitgl = $request->sampaitgl;
                $modelDetailLaporan->target = $request->target;
                $modelDetailLaporan->progres = $request->progres;
                $modelDetailLaporan->keterangan = $request->keterangan;

                $modelDetailLaporan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

                if ($modelDetailLaporan->save()) {
                    $modelDetailPagu = DetailPaguPenganggaran::find($modelDetailLaporan->detailpaguanggaranid);

                    $modelDetailPagu->progres = $request->progres;
                    $modelDetailPagu->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

                    $modelDetailPagu->save();
                }

                DB::commit();
                
                return response([
                    'success' => true,
                    'data'    => 'Success',
                    'message' => 'detail penganggaran sarpras updated successfully.',
                ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function selesai1(Request $request, $detailpaguanggaranid)
    {
        $this->authorize('edit-27');

        $user = auth('sanctum')->user();

        try {
            $progresfisik = DB::table('tbdetailpagupenganggaran')
                ->join('tbdetailpenganggaran', function($join)
                {
                    $join->on('tbdetailpenganggaran.detailpenganggaranid', '=', 'tbdetailpagupenganggaran.detailpenganggaranid');
                    $join->on('tbdetailpenganggaran.dlt','=',DB::raw("'0'"));
                })
                ->join('tbtranssarpraskebutuhan', function($join)
                {
                    $join->on('tbtranssarpraskebutuhan.sarpraskebutuhanid', '=', 'tbdetailpenganggaran.sarpraskebutuhanid');
                    $join->on('tbtranssarpraskebutuhan.dlt', '=', DB::raw("'0'"));
                    // $join->on('tbtranssarpraskebutuhan.status', '=', DB::raw("'5'"));
                })
                ->leftJoin('tbmperusahaan', function($join)
                {
                    $join->on('tbmperusahaan.perusahaanid', '=', 'tbdetailpagupenganggaran.perusahaanid');
                    $join->on('tbmperusahaan.dlt','=',DB::raw("'0'"));
                })
                ->join('tbmsubkeg', function($join)
                {
                    $join->on('tbmsubkeg.subkegid', '=', 'tbdetailpenganggaran.subkegid');
                    $join->on('tbmsubkeg.dlt','=',DB::raw("'0'"));
                })
                ->select(
                    // DB::raw("'0' as progres"),
                    'tbtranssarpraskebutuhan.sarpraskebutuhanid',
                    'tbtranssarpraskebutuhan.jenissarpras',
                    'tbtranssarpraskebutuhan.namasarprasid',
                    'tbtranssarpraskebutuhan.jumlahsetuju',
                    'tbtranssarpraskebutuhan.satuansetuju',
                    'tbtranssarpraskebutuhan.thang',
                    'tbtranssarpraskebutuhan.sekolahid',
                    'tbtranssarpraskebutuhan.jenissarpras',
                    'tbdetailpenganggaran.detailpenganggaranid',
                    'tbdetailpenganggaran.subkegid',
                    'tbdetailpenganggaran.sumberdana',
                    'tbdetailpagupenganggaran.detailpaguanggaranid',
                    'tbdetailpagupenganggaran.jenispagu', 
                    'tbdetailpagupenganggaran.nilaipagu', 
                    'tbdetailpagupenganggaran.nokontrak', 
                    'tbdetailpagupenganggaran.nilaikontrak', 
                    'tbdetailpagupenganggaran.perusahaanid', 
                    'tbdetailpagupenganggaran.tgldari',
                    'tbdetailpagupenganggaran.tglsampai',
                    'tbdetailpagupenganggaran.file',
                    'tbmperusahaan.nama', 
                    'tbmsubkeg.subkegnama', 
                    'tbdetailpenganggaran.subdetailkegiatan',
                    'tbdetailpagupenganggaran.nokontrak',
                    'tbdetailpagupenganggaran.nilaikontrak',
                    // 'tbdetaillaporan.detaillaporanid',
                    DB::raw('coalesce(tbdetailpagupenganggaran.progres, 0) as progres')
                    // DB::raw('MAX(tbdetaillaporan.progres) as progres')
                )
                ->where('tbdetailpagupenganggaran.detailpaguanggaranid', '=', $detailpaguanggaranid)
                ->where('tbdetailpagupenganggaran.dlt', '0')
                ->groupBy(
                    'tbdetailpagupenganggaran.detailpaguanggaranid',
                    'tbdetailpagupenganggaran.jenispagu', 
                    'tbmperusahaan.nama', 
                    'tbmsubkeg.subkegnama', 
                    'tbdetailpenganggaran.detailpenganggaranid',
                    'tbdetailpenganggaran.subdetailkegiatan',
                    'tbdetailpagupenganggaran.nokontrak',
                    'tbdetailpagupenganggaran.nilaikontrak',
                    'tbtranssarpraskebutuhan.jeniskebutuhan',
                    'tbtranssarpraskebutuhan.sarpraskebutuhanid',
                    'tbtranssarpraskebutuhan.status',
                    // 'tbdetaillaporan.progres',
                    // 'tbdetaillaporan.detaillaporanid',
                )
                ->get()
            ;

            // dd($countDetailPaguSarprasTersedia[0]->count == $countDetailPaguKebutuhanSarpras[0]->count);

            // $kebutuhansarpras = SarprasKebutuhan::where('sarpraskebutuhanid', )
            $detailpaguanggaran = DetailPaguPenganggaran::find($detailpaguanggaranid);

            // $sarprastersedia = SarprasTersedia::where('sarpraskebutuhanid', $progresfisik[0]->sarpraskebutuhanid)->get();
            $sarprastersedia = SarprasTersedia::where('sarpraskebutuhanid', $progresfisik[0]->sarpraskebutuhanid);

            DB::beginTransaction();

            if($sarprastersedia->count() != 0) {
                $checkExistDetailSarpras = DetailSarprasTersedia::where('detailpenganggaranid', $progresfisik[0]->detailpenganggaranid);
                if ($checkExistDetailSarpras->count() != 0) {
                    $checkExistDetailPaguSarpras = DetailPaguSarprasTersedia::where('detailpaguanggaranid', $progresfisik[0]->detailpaguanggaranid);
                    if ($checkExistDetailPaguSarpras->count() != 0) {
                        return response([
                            'success' => false,
                            'data'    => 'Error',
                            'message' => 'Data sudah tersedia di menu sarpras tersedia.',
                        ], 200);
                    }
                    else {
                        $detailpagusarpras = new DetailPaguSarprasTersedia;

                        $detailpagusarpras->detailsarprasid = $checkExistDetailSarpras->first()->detailsarprasid;
                        $detailpagusarpras->jenispagu = $progresfisik[0]->jenispagu;
                        $detailpagusarpras->nilaipagu = $progresfisik[0]->nilaipagu;
                        $detailpagusarpras->perusahaanid = $progresfisik[0]->perusahaanid;
                        $detailpagusarpras->tgldari = $progresfisik[0]->tgldari;
                        $detailpagusarpras->tglsampai = $progresfisik[0]->tglsampai;
                        $detailpagusarpras->nokontrak = $progresfisik[0]->nokontrak;
                        $detailpagusarpras->nilaikontrak = $progresfisik[0]->nilaikontrak;
                        $detailpagusarpras->file = $progresfisik[0]->file;
                        $detailpagusarpras->detailpaguanggaranid = $progresfisik[0]->detailpaguanggaranid;
                        $detailpagusarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                        
                        $detailpagusarpras->save();
                        
                        // Set is selesai tbdetailpagupenganggaran
                        $detailpaguanggaran->isselesai = 1;
                        $detailpaguanggaran->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                        $detailpaguanggaran->save();

                        // DB::commit();

                        // return response([
                        //     'success' => true,
                        //     'data'    => 'Success',
                        //     'message' => 'success kondisi 1.1.',
                        // ], 200);
                    }
                }
                else {
                    $detailsarprastersedia = new DetailSarprasTersedia;

                    $detailsarprastersedia->sarprastersediaid = $sarprastersedia->first()->sarprastersediaid;
                    $detailsarprastersedia->detailpenganggaranid = $progresfisik[0]->detailpenganggaranid;
                    $detailsarprastersedia->subkegid = $progresfisik[0]->subkegid;
                    $detailsarprastersedia->sumberdana = $progresfisik[0]->sumberdana;
                    $detailsarprastersedia->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                    if($detailsarprastersedia->save())
                    {
                        $detailpagusarpras = new DetailPaguSarprasTersedia;

                        $detailpagusarpras->detailsarprasid = $detailsarprastersedia->detailsarprasid;
                        $detailpagusarpras->jenispagu = $progresfisik[0]->jenispagu;
                        $detailpagusarpras->nilaipagu = $progresfisik[0]->nilaipagu;
                        $detailpagusarpras->perusahaanid = $progresfisik[0]->perusahaanid;
                        $detailpagusarpras->tgldari = $progresfisik[0]->tgldari;
                        $detailpagusarpras->tglsampai = $progresfisik[0]->tglsampai;
                        $detailpagusarpras->nokontrak = $progresfisik[0]->nokontrak;
                        $detailpagusarpras->nilaikontrak = $progresfisik[0]->nilaikontrak;
                        $detailpagusarpras->file = $progresfisik[0]->file;
                        $detailpagusarpras->detailpaguanggaranid = $progresfisik[0]->detailpaguanggaranid;
                        $detailpagusarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                        $detailpagusarpras->save();

                        // Set is selesai tbdetailpagupenganggaran
                        $detailpaguanggaran->isselesai = 1;
                        $detailpaguanggaran->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                        $detailpaguanggaran->save();
                    }

                    // // ketika berhasil menyelesaikan progres pembangunan, status pengajuan kebutuhan sarpras berubah menjadi progres selesai
                    // if ($countDetailPaguSarprasTersedia[0]->count == $countDetailPaguKebutuhanSarpras[0]->count) {
                    //     $kebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_SELESAI;
                    //     $kebutuhansarpras->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                    //     $kebutuhansarpras->save();

                    //     if($kebutuhansarpras->save())
                    //     {
                    //         $statuskebutuhansarpras = new StatusKebutuhanSarpras;
                    //         $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    //         $statuskebutuhansarpras->sarpraskebutuhanid = $kebutuhansarpras->sarpraskebutuhanid;
                    //         $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_SELESAI;
                    //         $statuskebutuhansarpras->tgl = date('Y-m-d');
                    //         $statuskebutuhansarpras->keterangan = $request->keterangan;

                    //         $statuskebutuhansarpras->save();
                    //     }
                    // }

                    // DB::commit();

                    // return response([
                    //     'success' => true,
                    //     'data'    => 'Success',
                    //     'message' => 'success kondisi 1.2.',
                    // ], 200);
                }
            }
            else {
                $sarprastersedia = new SarprasTersedia;

                $sarprastersedia->namasarprasid = $progresfisik[0]->namasarprasid;
                $sarprastersedia->jumlahunit = $progresfisik[0]->jumlahsetuju;
                $sarprastersedia->satuan = $progresfisik[0]->satuansetuju;
                $sarprastersedia->sekolahid = $progresfisik[0]->sekolahid;
                $sarprastersedia->jenissarpras = $progresfisik[0]->jenissarpras;
                $sarprastersedia->thang = $progresfisik[0]->thang;
                $sarprastersedia->sarpraskebutuhanid = $progresfisik[0]->sarpraskebutuhanid;
                $sarprastersedia->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                if($sarprastersedia->save())
                {
                    $detailsarprastersedia = new DetailSarprasTersedia;

                    $detailsarprastersedia->sarprastersediaid = $sarprastersedia->sarprastersediaid;
                    $detailsarprastersedia->detailpenganggaranid = $progresfisik[0]->detailpenganggaranid;
                    $detailsarprastersedia->subkegid = $progresfisik[0]->subkegid;
                    $detailsarprastersedia->sumberdana = $progresfisik[0]->sumberdana;
                    $detailsarprastersedia->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                    if($detailsarprastersedia->save())
                    {
                        $detailpagusarpras = new DetailPaguSarprasTersedia;

                        $detailpagusarpras->detailsarprasid = $detailsarprastersedia->detailsarprasid;
                        $detailpagusarpras->jenispagu = $progresfisik[0]->jenispagu;
                        $detailpagusarpras->nilaipagu = $progresfisik[0]->nilaipagu;
                        $detailpagusarpras->perusahaanid = $progresfisik[0]->perusahaanid;
                        $detailpagusarpras->tgldari = $progresfisik[0]->tgldari;
                        $detailpagusarpras->tglsampai = $progresfisik[0]->tglsampai;
                        $detailpagusarpras->nokontrak = $progresfisik[0]->nokontrak;
                        $detailpagusarpras->nilaikontrak = $progresfisik[0]->nilaikontrak;
                        $detailpagusarpras->file = $progresfisik[0]->file;
                        $detailpagusarpras->detailpaguanggaranid = $progresfisik[0]->detailpaguanggaranid;
                        $detailpagusarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                        $detailpagusarpras->save();

                        // Set is selesai tbdetailpagupenganggaran
                        $detailpaguanggaran->isselesai = 1;
                        $detailpaguanggaran->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                        $detailpaguanggaran->save();
                    }
                }

                // // ketika berhasil menyelesaikan progres pembangunan, status pengajuan kebutuhan sarpras berubah menjadi progres selesai
                // if ($countDetailPaguSarprasTersedia[0]->count == $countDetailPaguKebutuhanSarpras[0]->count) {
                //     $kebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_SELESAI;
                //     $kebutuhansarpras->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                //     $kebutuhansarpras->save();

                //     if($kebutuhansarpras->save())
                //     {
                //         $statuskebutuhansarpras = new StatusKebutuhanSarpras;
                //         $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                //         $statuskebutuhansarpras->sarpraskebutuhanid = $kebutuhansarpras->sarpraskebutuhanid;
                //         $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_SELESAI;
                //         $statuskebutuhansarpras->tgl = date('Y-m-d');
                //         $statuskebutuhansarpras->keterangan = $request->keterangan;

                //         $statuskebutuhansarpras->save();
                //     }
                    
                // }
            }

            DB::commit();

            $countDetailPaguKebutuhanSarpras = DB::select(
                DB::raw("SELECT 
                count(total.*) 
                from(
                        select dtpg.detailpaguanggaranid from tbdetailpagupenganggaran dtpg
                        inner join tbdetailpenganggaran dtpeng on dtpeng.detailpenganggaranid = dtpg.detailpenganggaranid
                        inner join tbtranssarpraskebutuhan spkb on spkb.sarpraskebutuhanid = dtpeng.sarpraskebutuhanid 
                        where spkb.sarpraskebutuhanid = '".$progresfisik[0]->sarpraskebutuhanid."'
                    ) as total")
            );

            $countDetailPaguSarprasTersedia = DB::select(
                DB::raw("
                    SELECT 
                    count(total.*) 
                    from(
                            select dtpg.detailpagusarprasid from tbtransdetailpagusarpras dtpg
                            inner join tbtransdetailsarpras dtpeng on dtpeng.detailsarprasid = dtpg.detailsarprasid 
                            inner join tbtranssarprastersedia spkb on spkb.sarprastersediaid = dtpeng.sarprastersediaid 
                            where spkb.sarpraskebutuhanid = '".$progresfisik[0]->sarpraskebutuhanid."' and dtpg.detailpaguanggaranid IS NOT NULL
                        ) as total
                ")
            );

            // $kebutuhansarpras = SarprasKebutuhan::where('sarpraskebutuhanid', $progresfisik[0]->sarpraskebutuhanid);
            $kebutuhansarpras = SarprasKebutuhan::find($progresfisik[0]->sarpraskebutuhanid);
            // ketika berhasil menyelesaikan progres pembangunan, status pengajuan kebutuhan sarpras berubah menjadi progres selesai
            if ($countDetailPaguSarprasTersedia[0]->count == $countDetailPaguKebutuhanSarpras[0]->count) {
                DB::beginTransaction();
                $kebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_SELESAI;
                // $kebutuhansarpras->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                // $kebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                $kebutuhansarpras->save();

                if($kebutuhansarpras->save())
                {
                    $statuskebutuhansarpras = new StatusKebutuhanSarpras;
                    $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $statuskebutuhansarpras->sarpraskebutuhanid = $kebutuhansarpras->sarpraskebutuhanid;
                    $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_SELESAI;
                    $statuskebutuhansarpras->tgl = date('Y-m-d');
                    $statuskebutuhansarpras->keterangan = $request->keterangan;

                    $statuskebutuhansarpras->save();
                }
                DB::commit();
            }

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'success kondisi 2.',
            ], 200);
        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }  
    }

    public function selesai2(Request $request, $detailpaguanggaranid)
    {
        $this->authorize('edit-27');

        $user = auth('sanctum')->user();

        try {
            $progresfisik = DB::table('tbdetailpagupenganggaran')
                ->join('tbdetailpenganggaran', function($join)
                {
                    $join->on('tbdetailpenganggaran.detailpenganggaranid', '=', 'tbdetailpagupenganggaran.detailpenganggaranid');
                    $join->on('tbdetailpenganggaran.dlt','=',DB::raw("'0'"));
                })
                ->join('tbtranssarpraskebutuhan', function($join)
                {
                    $join->on('tbtranssarpraskebutuhan.sarpraskebutuhanid', '=', 'tbdetailpenganggaran.sarpraskebutuhanid');
                    $join->on('tbtranssarpraskebutuhan.dlt', '=', DB::raw("'0'"));
                    // $join->on('tbtranssarpraskebutuhan.status', '=', DB::raw("'5'"));
                })
                ->leftJoin('tbmperusahaan', function($join)
                {
                    $join->on('tbmperusahaan.perusahaanid', '=', 'tbdetailpagupenganggaran.perusahaanid');
                    $join->on('tbmperusahaan.dlt','=',DB::raw("'0'"));
                })
                ->join('tbmsubkeg', function($join)
                {
                    $join->on('tbmsubkeg.subkegid', '=', 'tbdetailpenganggaran.subkegid');
                    $join->on('tbmsubkeg.dlt','=',DB::raw("'0'"));
                })
                ->select(
                    // DB::raw("'0' as progres"),
                    'tbtranssarpraskebutuhan.sarpraskebutuhanid',
                    'tbtranssarpraskebutuhan.jenissarpras',
                    'tbtranssarpraskebutuhan.namasarprasid',
                    'tbtranssarpraskebutuhan.jumlahsetuju',
                    'tbtranssarpraskebutuhan.satuansetuju',
                    'tbtranssarpraskebutuhan.thang',
                    'tbtranssarpraskebutuhan.sekolahid',
                    'tbdetailpenganggaran.detailpenganggaranid',
                    'tbdetailpenganggaran.subkegid',
                    'tbdetailpenganggaran.sumberdana',
                    'tbdetailpagupenganggaran.detailpaguanggaranid',
                    'tbdetailpagupenganggaran.jenispagu', 
                    'tbdetailpagupenganggaran.nilaipagu', 
                    'tbdetailpagupenganggaran.nokontrak', 
                    'tbdetailpagupenganggaran.nilaikontrak', 
                    'tbdetailpagupenganggaran.perusahaanid', 
                    'tbdetailpagupenganggaran.tgldari',
                    'tbdetailpagupenganggaran.tglsampai',
                    'tbdetailpagupenganggaran.file',
                    'tbmperusahaan.nama', 
                    'tbmsubkeg.subkegnama', 
                    'tbdetailpenganggaran.subdetailkegiatan',
                    'tbdetailpagupenganggaran.nokontrak',
                    'tbdetailpagupenganggaran.nilaikontrak',
                    // 'tbdetaillaporan.detaillaporanid',
                    DB::raw('coalesce(tbdetailpagupenganggaran.progres, 0) as progres')
                    // DB::raw('MAX(tbdetaillaporan.progres) as progres')
                )
                ->where('tbdetailpagupenganggaran.detailpaguanggaranid', '=', $detailpaguanggaranid)
                ->where('tbdetailpagupenganggaran.dlt', '0')
                ->groupBy(
                    'tbdetailpagupenganggaran.detailpaguanggaranid',
                    'tbdetailpagupenganggaran.jenispagu', 
                    'tbmperusahaan.nama', 
                    'tbmsubkeg.subkegnama', 
                    'tbdetailpenganggaran.detailpenganggaranid',
                    'tbdetailpenganggaran.subdetailkegiatan',
                    'tbdetailpagupenganggaran.nokontrak',
                    'tbdetailpagupenganggaran.nilaikontrak',
                    'tbtranssarpraskebutuhan.jeniskebutuhan',
                    'tbtranssarpraskebutuhan.sarpraskebutuhanid',
                    'tbtranssarpraskebutuhan.status',
                    // 'tbdetaillaporan.progres',
                    // 'tbdetaillaporan.detaillaporanid',
                )
                ->get()
            ;

            // dd($countDetailPaguSarprasTersedia[0]->count == $countDetailPaguKebutuhanSarpras[0]->count);

            // $kebutuhansarpras = SarprasKebutuhan::where('sarpraskebutuhanid', )
            $detailpaguanggaran = DetailPaguPenganggaran::find($detailpaguanggaranid);

            // $sarprastersedia = SarprasTersedia::where('sarpraskebutuhanid', $progresfisik[0]->sarpraskebutuhanid)->get();
            $sarprastersedia = SarprasTersedia::where('sarpraskebutuhanid', $progresfisik[0]->sarpraskebutuhanid);

            DB::beginTransaction();

            // Save multiple and file 
            $tglnow = date('Y-m-d');

            foreach ($request->jenisperalatanid as $key => $value) {
                $modelDetailJumlahPeralatan = new DetailJumlahPeralatan;
                $modelDetailJumlahPeralatan->detailpenganggaranid = $progresfisik[0]->detailpenganggaranid;
                $modelDetailJumlahPeralatan->jenisperalatanid = $request->jenisperalatanid[$key];
                $modelDetailJumlahPeralatan->jumlah = $request->jumlah[$key];
                $modelDetailJumlahPeralatan->satuan = $request->satuan[$key];

                $modelDetailJumlahPeralatan->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                if($modelDetailJumlahPeralatan->save())
                {
                    if($request->hasFile('file'))
                    {
                        foreach ($request->file('file') as $key => $value) {
                        
                            $modelFileDetailJumlahPeralatan = new FileDetailJumlahPeralatan;
                            $modelFileDetailJumlahPeralatan->detailjumlahperalatanid = $modelDetailJumlahPeralatan->detailjumlahperalatanid;
                            
                            $fileName = $tglnow.'_'.rand(1,100000).'_'.$request->file('file')[$key]->getClientOriginalName();   
                            $filePath = $request->file('file')[$key]->storeAs('public/uploaded/sarprastersedia/detailjumlahsarpras', $fileName);   
                            $modelFileDetailJumlahPeralatan->file = $fileName;
                            $modelFileDetailJumlahPeralatan->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                            $modelFileDetailJumlahPeralatan->save();
                        }

                    }
                }

                $modelSarprasTersedia = new SarprasTersedia;
                $modelSarprasTersedia->namasarprasid = $progresfisik[0]->namasarprasid;
                $modelSarprasTersedia->jenissarpras = $progresfisik[0]->jenissarpras;
                $modelSarprasTersedia->sekolahid = $progresfisik[0]->sekolahid;
                $modelSarprasTersedia->jumlahunit = $progresfisik[0]->jumlahsetuju;
                $modelSarprasTersedia->satuan = $progresfisik[0]->satuansetuju;
                $modelSarprasTersedia->sarpraskebutuhanid = $progresfisik[0]->sarpraskebutuhanid;
                $modelSarprasTersedia->jenisperalatanid = $modelDetailJumlahPeralatan->jenisperalatanid;

                $modelSarprasTersedia->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                
                if ($modelSarprasTersedia->save()) {

                    $modelDetailSarprasTersedia = new DetailSarprasTersedia;

                    $modelDetailSarprasTersedia->sarprastersediaid = $modelSarprasTersedia->sarprastersediaid;
                    $modelDetailSarprasTersedia->detailpenganggaranid = $progresfisik[0]->detailpenganggaranid;
                    $modelDetailSarprasTersedia->subkegid = $progresfisik[0]->subkegid;
                    $modelDetailSarprasTersedia->sumberdana = $progresfisik[0]->sumberdana;
                    $modelDetailSarprasTersedia->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                    if ($modelDetailSarprasTersedia->save()) {

                        $modelDetailPaguSarpras = new DetailPaguSarprasTersedia;

                        $modelDetailPaguSarpras->detailsarprasid = $modelDetailSarprasTersedia->detailsarprasid;
                        $modelDetailPaguSarpras->jenispagu = $progresfisik[0]->jenispagu;
                        $modelDetailPaguSarpras->nilaipagu = $progresfisik[0]->nilaipagu;
                        $modelDetailPaguSarpras->perusahaanid = $progresfisik[0]->perusahaanid;
                        $modelDetailPaguSarpras->tgldari = $progresfisik[0]->tgldari;
                        $modelDetailPaguSarpras->tglsampai = $progresfisik[0]->tglsampai;
                        $modelDetailPaguSarpras->nokontrak = $progresfisik[0]->nokontrak;
                        $modelDetailPaguSarpras->nilaikontrak = $progresfisik[0]->nilaikontrak;
                        $modelDetailPaguSarpras->file = $progresfisik[0]->file;
                        $modelDetailPaguSarpras->detailpaguanggaranid = $progresfisik[0]->detailpaguanggaranid;
                        $modelDetailPaguSarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                        $modelDetailPaguSarpras->save();

                        // Set is selesai tbdetailpagupenganggaran
                        $detailpaguanggaran->isselesai = 1;
                        $detailpaguanggaran->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                        $detailpaguanggaran->save();

                        $modelDetailJumlahSarpras = new DetailJumlahSarpras;

                        $modelDetailJumlahSarpras->detailsarprasid = $modelDetailSarprasTersedia->detailsarprasid;
                        $modelDetailJumlahSarpras->kondisi = enum::KONDISI_SARPRAS_BAIK;
                        $modelDetailJumlahSarpras->jumlah = $modelDetailJumlahPeralatan->jumlah;
                        // $modelDetailJumlahSarpras->satuan = $modelDetailJumlahPeralatan->satuan;

                        $modelDetailJumlahSarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                        
                        
                        if($modelDetailJumlahSarpras->save())
                        {
                            $modelFileDetailJumlahSarpras =  new FileDetailJumlahSarpras;
                            $modelFileDetailJumlahSarpras->detailjumlahsarprasid = $modelDetailSarprasTersedia->detailjumlahsarprasid;
                            $modelFileDetailJumlahSarpras->file = $modelFileDetailJumlahPeralatan->file;
                            $modelFileDetailJumlahSarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                            
                            $modelFileDetailJumlahSarpras->save();
                        }

                    }

                }

            }

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'success kondisi 2.',
            ], 200);
        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }  
    }

    public function selesai(Request $request, $detailpaguanggaranid)
    {
        $this->authorize('edit-27');

        $user = auth('sanctum')->user();

        try {
            $progresfisik = DB::table('tbdetailpagupenganggaran')
                ->join('tbdetailpenganggaran', function($join)
                {
                    $join->on('tbdetailpenganggaran.detailpenganggaranid', '=', 'tbdetailpagupenganggaran.detailpenganggaranid');
                    $join->on('tbdetailpenganggaran.dlt','=',DB::raw("'0'"));
                })
                ->join('tbtranssarpraskebutuhan', function($join)
                {
                    $join->on('tbtranssarpraskebutuhan.sarpraskebutuhanid', '=', 'tbdetailpenganggaran.sarpraskebutuhanid');
                    $join->on('tbtranssarpraskebutuhan.dlt', '=', DB::raw("'0'"));
                    // $join->on('tbtranssarpraskebutuhan.status', '=', DB::raw("'5'"));
                })
                ->leftJoin('tbmperusahaan', function($join)
                {
                    $join->on('tbmperusahaan.perusahaanid', '=', 'tbdetailpagupenganggaran.perusahaanid');
                    $join->on('tbmperusahaan.dlt','=',DB::raw("'0'"));
                })
                ->join('tbmsubkeg', function($join)
                {
                    $join->on('tbmsubkeg.subkegid', '=', 'tbdetailpenganggaran.subkegid');
                    $join->on('tbmsubkeg.dlt','=',DB::raw("'0'"));
                })
                ->select(
                    // DB::raw("'0' as progres"),
                    'tbtranssarpraskebutuhan.sarpraskebutuhanid',
                    'tbtranssarpraskebutuhan.jenissarpras',
                    'tbtranssarpraskebutuhan.namasarprasid',
                    'tbtranssarpraskebutuhan.jumlahsetuju',
                    'tbtranssarpraskebutuhan.satuansetuju',
                    'tbtranssarpraskebutuhan.thang',
                    'tbtranssarpraskebutuhan.sekolahid',
                    'tbdetailpenganggaran.detailpenganggaranid',
                    'tbdetailpenganggaran.subkegid',
                    'tbdetailpenganggaran.sumberdana',
                    'tbdetailpagupenganggaran.detailpaguanggaranid',
                    'tbdetailpagupenganggaran.jenispagu', 
                    'tbdetailpagupenganggaran.nilaipagu', 
                    'tbdetailpagupenganggaran.nokontrak', 
                    'tbdetailpagupenganggaran.nilaikontrak', 
                    'tbdetailpagupenganggaran.perusahaanid', 
                    'tbdetailpagupenganggaran.tgldari',
                    'tbdetailpagupenganggaran.tglsampai',
                    'tbdetailpagupenganggaran.file',
                    'tbmperusahaan.nama', 
                    'tbmsubkeg.subkegnama', 
                    'tbdetailpenganggaran.subdetailkegiatan',
                    'tbdetailpagupenganggaran.nokontrak',
                    'tbdetailpagupenganggaran.nilaikontrak',
                    // 'tbdetaillaporan.detaillaporanid',
                    DB::raw('coalesce(tbdetailpagupenganggaran.progres, 0) as progres')
                    // DB::raw('MAX(tbdetaillaporan.progres) as progres')
                )
                ->where('tbdetailpagupenganggaran.detailpaguanggaranid', '=', $detailpaguanggaranid)
                ->where('tbdetailpagupenganggaran.dlt', '0')
                ->groupBy(
                    'tbdetailpagupenganggaran.detailpaguanggaranid',
                    'tbdetailpagupenganggaran.jenispagu', 
                    'tbmperusahaan.nama', 
                    'tbmsubkeg.subkegnama', 
                    'tbdetailpenganggaran.detailpenganggaranid',
                    'tbdetailpenganggaran.subdetailkegiatan',
                    'tbdetailpagupenganggaran.nokontrak',
                    'tbdetailpagupenganggaran.nilaikontrak',
                    'tbtranssarpraskebutuhan.jeniskebutuhan',
                    'tbtranssarpraskebutuhan.sarpraskebutuhanid',
                    'tbtranssarpraskebutuhan.status',
                    // 'tbdetaillaporan.progres',
                    // 'tbdetaillaporan.detaillaporanid',
                )
                ->get()
            ;

            // dd($countDetailPaguSarprasTersedia[0]->count == $countDetailPaguKebutuhanSarpras[0]->count);

            // $kebutuhansarpras = SarprasKebutuhan::where('sarpraskebutuhanid', )
            // $detailpaguanggaran = DetailPaguPenganggaran::find($detailpaguanggaranid);

            // $sarprastersedia = SarprasTersedia::where('sarpraskebutuhanid', $progresfisik[0]->sarpraskebutuhanid)->get();
            // $sarprastersedia = SarprasTersedia::where('sarpraskebutuhanid', $progresfisik[0]->sarpraskebutuhanid);
            $kebutuhansarpras = SarprasKebutuhan::find($progresfisik[0]->sarpraskebutuhanid);

            $detailjumlahperalatan = DetailJumlahPeralatan::where('detailpenganggaranid', $progresfisik[0]->detailpenganggaranid)->where('dlt', 0);

            DB::beginTransaction();

            // Save multiple and file 
            $tglnow = date('Y-m-d');
            
            // dd($detailjumlahperalatan);
            // dd(
            //     $detailpaguanggaran = DB::table('tbtranssarpraskebutuhan')
            //             ->join('tbdetailpenganggaranid', function($join){
            //                 $join->on('tbdetailpenganggaran.sarpraskebutuhanid', '=', 'tbtranssarpraskebutuhan.sarpraskebutuhanid');
            //             })
            //             ->join('tbdetailpagupenganggaran', function($join){
            //                 $join->on('tbdetailpagupenganggaran.sarpraskebutuhanid', '=', );
            //                 $join->on('tbtranssarpraskebutuhan.dlt', '=', DB::raw("'0'"));
            //             })
            //             ->select('tbdetailpagupenganggaran.*')
            //             ->where('detailpenganggaranid', $progresfisik[0]->sarpraskebutuhanid)->get()
            // );

            if($kebutuhansarpras->jenissarpras == enum::SARPRAS_PERALATAN)
            {
                if($detailjumlahperalatan->count() == 0)
                {
                    return response([
                        'success' => false,
                        'data'    => 'failed',
                        'message' => 'Anda belum melengkapi data detail jenis peralatan, silakan isi detail jenis peralatan terlebih dahulu',
                    ], 200);
                }
                else 
                {
                    $sarprastersediaExist = SarprasTersedia::where('sekolahid', $progresfisik[0]->sekolahid)->where('jenisperalatanid', 2);
                    dd($sarprastersediaExist);
                    foreach($detailjumlahperalatan->get() as $itemJumlahPeralatan){

                        // $checkSarprasTersedia = SarprasTersedia::where('')

                        $sarprastersedia = new SarprasTersedia;
        
                        $sarprastersedia->namasarprasid = $progresfisik[0]->namasarprasid;
                        $sarprastersedia->jumlahunit = $itemJumlahPeralatan->jumlah;
                        $sarprastersedia->satuan = $itemJumlahPeralatan->satuan;
                        $sarprastersedia->sekolahid = $progresfisik[0]->sekolahid;
                        $sarprastersedia->jenissarpras = $progresfisik[0]->jenissarpras;
                        $sarprastersedia->thang = $progresfisik[0]->thang;
                        $sarprastersedia->sarpraskebutuhanid = $progresfisik[0]->sarpraskebutuhanid;
                        $sarprastersedia->jenisperalatanid = $itemJumlahPeralatan->jenisperalatanid;
                        $sarprastersedia->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
        
                        if ($sarprastersedia->save()) {
        
                            $detailpenganggaran = DetailPenganggaran::where('sarpraskebutuhanid', $progresfisik[0]->sarpraskebutuhanid)->where('dlt', 0)->get();
        
                            foreach($detailpenganggaran as $itemDetailPenganggaran) {
                                $detailsarprastersedia = new DetailSarprasTersedia;
        
                                $detailsarprastersedia->sarprastersediaid = $sarprastersedia->sarprastersediaid;
                                $detailsarprastersedia->detailpenganggaranid = $itemDetailPenganggaran->detailpenganggaranid;
                                $detailsarprastersedia->subkegid = $itemDetailPenganggaran->subkegid;
                                $detailsarprastersedia->sumberdana = $itemDetailPenganggaran->sumberdana;
                                $detailsarprastersedia->subdetailkegiatan = $itemDetailPenganggaran->subdetailkegiatan;
                                $detailsarprastersedia->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                                $detailsarprastersedia->save();
        
                                $detailpaguanggaran = DetailPaguPenganggaran::where('detailpenganggaranid', $itemDetailPenganggaran->detailpenganggaranid)->where('dlt', 0)->get();
        
                                foreach ($detailpaguanggaran as $itempaguanggaran) {
                                    $detailpagusarpras = new DetailPaguSarprasTersedia;
        
                                    $detailpagusarpras->detailsarprasid = $detailsarprastersedia->detailsarprasid;
                                    $detailpagusarpras->jenispagu = $itempaguanggaran->jenispagu;
                                    $detailpagusarpras->nilaipagu = $itempaguanggaran->nilaipagu;
                                    $detailpagusarpras->perusahaanid = $itempaguanggaran->perusahaanid;
                                    $detailpagusarpras->tgldari = $itempaguanggaran->tgldari;
                                    $detailpagusarpras->tglsampai = $itempaguanggaran->tglsampai;
                                    $detailpagusarpras->nokontrak = $itempaguanggaran->nokontrak;
                                    $detailpagusarpras->nilaikontrak = $itempaguanggaran->nilaikontrak;
                                    $detailpagusarpras->file = $itempaguanggaran->file;
                                    $detailpagusarpras->detailpaguanggaranid = $itempaguanggaran->detailpaguanggaranid;
                                    // $detailpagusarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                                    $detailpagusarpras->opadd = $user->login;
                                    $detailpagusarpras->pcadd = $request->ip();
        
                                    $detailpagusarpras->save();
        
                                    // Set is selesai tbdetailpagupenganggaran
                                    $detailPaguPenganggaran = DetailPaguPenganggaran::where('detailpenganggaranid', $progresfisik[0]->detailpenganggaranid)->where('dlt', 0)->first();
        
                                    // foreach($detailPaguPenganggaran->get() as $itemDetailPaguPenganggaran){
                                        $detailPaguPenganggaran->isselesai = 1;
                                        // $detailPaguPenganggaran->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                                        $detailPaguPenganggaran->opadd = $user->login;
                                        $detailPaguPenganggaran->pcadd = $request->ip();
                                        $detailPaguPenganggaran->save();
                                    // }
                                }
        
                                // if($kebutuhansarpras->jenissarpras == enum::SARPRAS_PERALATAN){
                                    $detailJumlahSarpras = new DetailJumlahSarpras;
                                    $detailJumlahSarpras->detailsarprasid = $detailsarprastersedia->detailsarprasid;
                                    $detailJumlahSarpras->kondisi = enum::KONDISI_SARPRAS_BAIK;
                                    $detailJumlahSarpras->jumlah = $itemJumlahPeralatan->jumlah;
                                    $detailJumlahSarpras->satuan = $itemJumlahPeralatan->satuan;
                                    $detailJumlahSarpras->detailjumlahperalatanid = $itemJumlahPeralatan->detailjumlahperalatanid;
        
                                    $detailJumlahSarpras->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
        
                                    if ($detailJumlahSarpras->save()) {
                                        $detailJumlahPeralatan = FileDetailJumlahPeralatan::where('detailjumlahperalatanid', $itemJumlahPeralatan->detailjumlahperalatanid)->where('dlt', 0)->get();
        
                                        foreach($detailJumlahPeralatan as $itemDetailJumlahPeralatan) {
                                            $fileDetailJumlahSarpras = new FileDetailJumlahSarpras;
                                            $fileDetailJumlahSarpras->detailjumlahsarprasid = $detailJumlahSarpras->detailjumlahsarprasid;
                                            $fileDetailJumlahSarpras->file = $itemDetailJumlahPeralatan->file;
        
                                            $fileDetailJumlahSarpras->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
        
                                            $fileDetailJumlahSarpras->save();
                                        }
                                    }
                                // }
                            }
                        }
                    }
                }
            }
            else 
            {
                $sarprastersedia = new SarprasTersedia;

                $sarprastersedia->namasarprasid = $progresfisik[0]->namasarprasid;
                $sarprastersedia->jumlahunit = $progresfisik[0]->jumlahsetuju;
                $sarprastersedia->satuan = $progresfisik[0]->satuansetuju;
                $sarprastersedia->sekolahid = $progresfisik[0]->sekolahid;
                $sarprastersedia->jenissarpras = $progresfisik[0]->jenissarpras;
                $sarprastersedia->thang = $progresfisik[0]->thang;
                $sarprastersedia->sarpraskebutuhanid = $progresfisik[0]->sarpraskebutuhanid;
                // $sarprastersedia->jenisperalatanid = $progresfisik[0]->jenisperalatanid;
                $sarprastersedia->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                if ($sarprastersedia->save()) {

                    $detailpenganggaran = DetailPenganggaran::where('sarpraskebutuhanid', $progresfisik[0]->sarpraskebutuhanid)->where('dlt', 0)->get();

                    foreach($detailpenganggaran as $itemDetailPenganggaran) {
                        $detailsarprastersedia = new DetailSarprasTersedia;

                        $detailsarprastersedia->sarprastersediaid = $sarprastersedia->sarprastersediaid;
                        $detailsarprastersedia->detailpenganggaranid = $itemDetailPenganggaran->detailpenganggaranid;
                        $detailsarprastersedia->subkegid = $itemDetailPenganggaran->subkegid;
                        $detailsarprastersedia->sumberdana = $itemDetailPenganggaran->sumberdana;
                        $detailsarprastersedia->subdetailkegiatan = $itemDetailPenganggaran->subdetailkegiatan;
                        $detailsarprastersedia->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                        $detailsarprastersedia->save();

                        $detailpaguanggaran = DetailPaguPenganggaran::where('detailpenganggaranid', $itemDetailPenganggaran->detailpenganggaranid)->where('dlt', 0)->get();

                        foreach ($detailpaguanggaran as $itempaguanggaran) {
                            $detailpagusarpras = new DetailPaguSarprasTersedia;

                            $detailpagusarpras->detailsarprasid = $detailsarprastersedia->detailsarprasid;
                            $detailpagusarpras->jenispagu = $itempaguanggaran->jenispagu;
                            $detailpagusarpras->nilaipagu = $itempaguanggaran->nilaipagu;
                            $detailpagusarpras->perusahaanid = $itempaguanggaran->perusahaanid;
                            $detailpagusarpras->tgldari = $itempaguanggaran->tgldari;
                            $detailpagusarpras->tglsampai = $itempaguanggaran->tglsampai;
                            $detailpagusarpras->nokontrak = $itempaguanggaran->nokontrak;
                            $detailpagusarpras->nilaikontrak = $itempaguanggaran->nilaikontrak;
                            $detailpagusarpras->file = $itempaguanggaran->file;
                            $detailpagusarpras->detailpaguanggaranid = $itempaguanggaran->detailpaguanggaranid;
                            // $detailpagusarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                            $detailpagusarpras->opadd = $user->login;
                            $detailpagusarpras->pcadd = $request->ip();

                            $detailpagusarpras->save();

                            // Set is selesai tbdetailpagupenganggaran
                            $detailPaguPenganggaran = DetailPaguPenganggaran::where('detailpenganggaranid', $progresfisik[0]->detailpenganggaranid)->where('dlt', 0)->first();

                            // foreach($detailPaguPenganggaran->get() as $itemDetailPaguPenganggaran){
                                $detailPaguPenganggaran->isselesai = 1;
                                // $detailPaguPenganggaran->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                                $detailPaguPenganggaran->opadd = $user->login;
                                $detailPaguPenganggaran->pcadd = $request->ip();
                                $detailPaguPenganggaran->save();
                            // }
                        }
                    }
                }
            }

            $kebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_SELESAI;
            $kebutuhansarpras->save();

            if($kebutuhansarpras->save())
            {
                $statuskebutuhansarpras = new StatusKebutuhanSarpras;
                $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                $statuskebutuhansarpras->sarpraskebutuhanid = $kebutuhansarpras->sarpraskebutuhanid;
                $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_SELESAI;
                $statuskebutuhansarpras->tgl = date('Y-m-d');
                $statuskebutuhansarpras->keterangan = $request->keterangan;

                $statuskebutuhansarpras->save();
            }

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'success kondisi 2.',
            ], 200);
        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }  
    }

    public function storedetailjumlahperalatan(Request $request, $detailpaguanggaranid)
    {
        $this->authorize('add-27');

        $user = auth('sanctum')->user();

        try {

            $progresfisik = DB::table('tbdetailpagupenganggaran')
                ->join('tbdetailpenganggaran', function($join)
                {
                    $join->on('tbdetailpenganggaran.detailpenganggaranid', '=', 'tbdetailpagupenganggaran.detailpenganggaranid');
                    $join->on('tbdetailpenganggaran.dlt','=',DB::raw("'0'"));
                })
                ->join('tbtranssarpraskebutuhan', function($join)
                {
                    $join->on('tbtranssarpraskebutuhan.sarpraskebutuhanid', '=', 'tbdetailpenganggaran.sarpraskebutuhanid');
                    $join->on('tbtranssarpraskebutuhan.dlt', '=', DB::raw("'0'"));
                    // $join->on('tbtranssarpraskebutuhan.status', '=', DB::raw("'5'"));
                })
                ->leftJoin('tbmperusahaan', function($join)
                {
                    $join->on('tbmperusahaan.perusahaanid', '=', 'tbdetailpagupenganggaran.perusahaanid');
                    $join->on('tbmperusahaan.dlt','=',DB::raw("'0'"));
                })
                ->join('tbmsubkeg', function($join)
                {
                    $join->on('tbmsubkeg.subkegid', '=', 'tbdetailpenganggaran.subkegid');
                    $join->on('tbmsubkeg.dlt','=',DB::raw("'0'"));
                })
                ->select(
                    // DB::raw("'0' as progres"),
                    'tbtranssarpraskebutuhan.sarpraskebutuhanid',
                    'tbtranssarpraskebutuhan.jenissarpras',
                    'tbtranssarpraskebutuhan.namasarprasid',
                    'tbtranssarpraskebutuhan.jumlahsetuju',
                    'tbtranssarpraskebutuhan.satuansetuju',
                    'tbtranssarpraskebutuhan.thang',
                    'tbtranssarpraskebutuhan.sekolahid',
                    'tbtranssarpraskebutuhan.jenissarpras',
                    'tbdetailpenganggaran.detailpenganggaranid',
                    'tbdetailpenganggaran.subkegid',
                    'tbdetailpenganggaran.sumberdana',
                    'tbdetailpagupenganggaran.detailpaguanggaranid',
                    'tbdetailpagupenganggaran.jenispagu', 
                    'tbdetailpagupenganggaran.nilaipagu', 
                    'tbdetailpagupenganggaran.nokontrak', 
                    'tbdetailpagupenganggaran.nilaikontrak', 
                    'tbdetailpagupenganggaran.perusahaanid', 
                    'tbdetailpagupenganggaran.tgldari',
                    'tbdetailpagupenganggaran.tglsampai',
                    'tbdetailpagupenganggaran.file',
                    'tbmperusahaan.nama', 
                    'tbmsubkeg.subkegnama', 
                    'tbdetailpenganggaran.subdetailkegiatan',
                    'tbdetailpagupenganggaran.nokontrak',
                    'tbdetailpagupenganggaran.nilaikontrak',
                    // 'tbdetaillaporan.detaillaporanid',
                    DB::raw('coalesce(tbdetailpagupenganggaran.progres, 0) as progres')
                    // DB::raw('MAX(tbdetaillaporan.progres) as progres')
                )
                ->where('tbdetailpagupenganggaran.detailpaguanggaranid', '=', $detailpaguanggaranid)
                ->where('tbdetailpagupenganggaran.dlt', '0')
                ->groupBy(
                    'tbdetailpagupenganggaran.detailpaguanggaranid',
                    'tbdetailpagupenganggaran.jenispagu', 
                    'tbmperusahaan.nama', 
                    'tbmsubkeg.subkegnama', 
                    'tbdetailpenganggaran.detailpenganggaranid',
                    'tbdetailpenganggaran.subdetailkegiatan',
                    'tbdetailpagupenganggaran.nokontrak',
                    'tbdetailpagupenganggaran.nilaikontrak',
                    'tbtranssarpraskebutuhan.jeniskebutuhan',
                    'tbtranssarpraskebutuhan.sarpraskebutuhanid',
                    'tbtranssarpraskebutuhan.status',
                    // 'tbdetaillaporan.progres',
                    // 'tbdetaillaporan.detaillaporanid',
                )
                ->get()
            ;

            $tglnow = date('Y-m-d');
            DB::beginTransaction();
            // Save multiple and file 
            $modelDetailJumlahPeralatan = new DetailJumlahPeralatan;

            $modelDetailJumlahPeralatan->detailpenganggaranid = $progresfisik[0]->detailpenganggaranid;
            $modelDetailJumlahPeralatan->jenisperalatanid = $request->jenisperalatanid;
            $modelDetailJumlahPeralatan->jumlah = $request->jumlah;
            $modelDetailJumlahPeralatan->satuan = $request->satuan;

            $modelDetailJumlahPeralatan->fill(['opadd' => $user->login, 'pcedit' => $request->ip()]);

            $modelDetailJumlahPeralatan->save();

            // if ($modelDetailJumlahPeralatan->save()) {

                if($request->hasFile('file'))
                {
                    foreach ($request->file('file') as $key => $value) {
                    
                        $modelFileDetailJumlahPeralatan = new FileDetailJumlahPeralatan;
                        $modelFileDetailJumlahPeralatan->detailjumlahperalatanid = $modelDetailJumlahPeralatan->detailjumlahperalatanid;
                        
                        $fileName = $tglnow.'_'.rand(1,100000).'_'.$request->file('file')[$key]->getClientOriginalName();   
                        $filePath = $request->file('file')[$key]->storeAs('public/uploaded/sarprastersedia/detailjumlahsarpras', $fileName);   
                        $modelFileDetailJumlahPeralatan->file = $fileName;
                        $modelFileDetailJumlahPeralatan->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                        $modelFileDetailJumlahPeralatan->save();
                    }

                }

            // }

            DB::commit();
            
            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail jumlah peralatan added successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function updatedetailjumlahperalatan(Request $request, $detailjumlahperalatanid)
    {
        $this->authorize('edit-27');

        $user = auth('sanctum')->user();

        try {

            $tglnow = date('Y-m-d');
            DB::beginTransaction();
            // Save multiple and file 
            $modelDetailJumlahPeralatan = DetailJumlahPeralatan::find($detailjumlahperalatanid);
            // dd($modelDetailJumlahPeralatan);

            // $modelDetailJumlahPeralatan->detailpenganggaranid = $progresfisik[0]->detailpenganggaranid;
            $modelDetailJumlahPeralatan->jenisperalatanid = $request->jenisperalatanid;
            $modelDetailJumlahPeralatan->jumlah = $request->jumlah;
            $modelDetailJumlahPeralatan->satuan = $request->satuan;

            $modelDetailJumlahPeralatan->fill(['opadd' => $user->login, 'pcedit' => $request->ip()]);

            $modelDetailJumlahPeralatan->save();

            DB::commit();
            
            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail jumlah peralatan updated successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function deletedetailjumlahperalatan(Request $request, $detailjumlahperalatanid)
    {
        $this->authorize('edit-27');

        $user = auth('sanctum')->user();

        try {

            DB::beginTransaction();
            // Save multiple and file 
            $modelDetailJumlahPeralatan = DetailJumlahPeralatan::find($detailjumlahperalatanid);

            $modelDetailJumlahPeralatan->dlt = '1';

            $modelDetailJumlahPeralatan->fill(['opedit' => $user->login, 'opedit' => $request->ip()]);

            $modelDetailJumlahPeralatan->save();

            DB::commit();
            
            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail jumlah peralatan deleted successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function loadFotoJenisPeralatan(Request $request, $detailjumlahperalatanid)
    {
        $this->authorize('view-27');

        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $filejenisperalatanid = DB::table('tbfiledetailjumlahperalatan')
                    ->select(
                        'tbfiledetailjumlahperalatan.filedetailjumlahperalatanid', 
                        'tbfiledetailjumlahperalatan.detailjumlahperalatanid', 
                        'tbfiledetailjumlahperalatan.file'
                    )
                    ->where('tbfiledetailjumlahperalatan.detailjumlahperalatanid', $detailjumlahperalatanid)
                    ->where('tbfiledetailjumlahperalatan.dlt', 0)
                    ->orderBy('tbfiledetailjumlahperalatan.detailjumlahperalatanid')
                ;

                $count = $filejenisperalatanid->count();
                $data = $filejenisperalatanid->skip($page)->take($perpage)->get();

                return $this->sendResponse([
                    'data' => $data,
                    'count' => $count,
                ], 'Data retrieved successfully.'); 

            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
        }
    }

    public function storedetailfotoperalatan(Request $request, $detailjumlahperalatanid) 
    {
        $this->authorize('add-27');

        $user = auth('sanctum')->user();

        try {

            $tglnow = date('Y-m-d');
            DB::beginTransaction();

            if($request->hasFile('file'))
            {
                $modelFileDetailJumlahPeralatan = new FileDetailJumlahPeralatan;
                $modelFileDetailJumlahPeralatan->detailjumlahperalatanid = $detailjumlahperalatanid;
                
                $fileName = $tglnow.'_'.rand(1,100000).'_'.$request->file('file')->getClientOriginalName();   
                $filePath = $request->file('file')->storeAs('public/uploaded/sarprastersedia/detailjumlahsarpras', $fileName);   
                $modelFileDetailJumlahPeralatan->file = $fileName;
                $modelFileDetailJumlahPeralatan->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                $modelFileDetailJumlahPeralatan->save();
            }


            DB::commit();
            
            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'file detail jumlah peralatan added successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function updatedetailfotoperalatan(Request $request, $filedetailjumlahperalatanid) 
    {
        $this->authorize('add-27');

        $user = auth('sanctum')->user();

        try {

            $tglnow = date('Y-m-d');
            DB::beginTransaction();

            if($request->hasFile('file'))
            {
                $modelFileDetailJumlahPeralatan = FileDetailJumlahPeralatan::find($filedetailjumlahperalatanid);

                if($modelFileDetailJumlahPeralatan->file != ''  && $modelFileDetailJumlahPeralatan->file != null){
                    $file_old = public_path().'/storage/uploaded/sarprastersedia/detailjumlahsarpras/'.$modelFileDetailJumlahPeralatan->file;
                    unlink($file_old);
                }  
                $fileName = $tglnow.'_'.rand(1,100000).'_'.$request->file('file')->getClientOriginalName();   
                $filePath = $request->file('file')->storeAs('public/uploaded/sarprastersedia/detailjumlahsarpras', $fileName);   
                $modelFileDetailJumlahPeralatan->file = $fileName;

                $modelFileDetailJumlahPeralatan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                $modelFileDetailJumlahPeralatan->save();
            }


            DB::commit();
            
            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'file detail jumlah peralatan added successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function deletedetailfotoperalatan(Request $request, $filedetailjumlahperalatanid)
    {
        $this->authorize('edit-27');

        $user = auth('sanctum')->user();

        try {

            $tglnow = date('Y-m-d');
            $modelFileDetailJumlahPeralatan = FileDetailJumlahPeralatan::find($filedetailjumlahperalatanid);

            if($modelFileDetailJumlahPeralatan->file != ''  && $modelFileDetailJumlahPeralatan->file != null){
                $file_old = public_path().'/storage/uploaded/sarprastersedia/detailjumlahsarpras/'.$modelFileDetailJumlahPeralatan->file;
                unlink($file_old);
            }

            $modelFileDetailJumlahPeralatan->dlt = '1';
            $modelFileDetailJumlahPeralatan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
            $modelFileDetailJumlahPeralatan->save();

            DB::commit();
            
            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'file detail jumlah peralatan deleted successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    } 

    // public function deleteDetailLaporan(Request $request, $id)
    // {
    //     $this->authorize('add-27');

    //     $user = auth('sanctum')->user();

    //     try {
    //             $tglnow = date('Y-m-d');
    //             DB::beginTransaction();
    //             // Save multiple and file 
    //             $modelDetailLaporan = DetailLaporan::find($id);

    //             $modelDetailLaporan->dlt = '1';

    //             $modelDetailLaporan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

    //             if ($modelDetailLaporan->save()) {

    //                 $progres = DetailLaporan::where('detaillaporanid', $id)->select(DB::raw('MAX(tbdetaillaporan.progres) as progres'))->get();
    //                 // dd($progres[0]['progres']);

    //                 $modelDetailPagu = DetailPaguPenganggaran::find($modelDetailLaporan->detailpaguanggaranid);

    //                 $modelDetailPagu->progres = intval($progres[0]['progres']);
    //                 $modelDetailPagu->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

    //                 $modelDetailPagu->save();
    //             }

    //             DB::commit();
                
    //             return response([
    //                 'success' => true,
    //                 'data'    => 'Success',
    //                 'message' => 'detail penganggaran sarpras updated successfully.',
    //             ], 200);

    //     } catch (QueryException $e) {
    //         return $this->sendError('SQL Error', $this->getQueryError($e));
    //     }
    //     catch (Exception $e) {
    //         return $this->sendError('Error', $e->getMessage());
    //     }
    // }
}
