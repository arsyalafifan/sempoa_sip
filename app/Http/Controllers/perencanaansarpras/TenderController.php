<?php

namespace App\Http\Controllers\perencanaansarpras;

use App\enumVar as enum;
use App\Http\Controllers\Controller;
use App\Models\master\Sekolah;
use App\Models\perencanaansarpras\DetailPaguPenganggaran;
use App\Models\perencanaansarpras\DetailPenganggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\sarpras\SarprasKebutuhan;
use App\Models\verifikasi\StatusKebutuhanSarpras;

class TenderController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Tender';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-20');

        Log::channel('mibedil')->info('Halaman '.$this->page);

        $sekolah = [];
        $kota = [];
        $kecamatan = [];

        if($request->ajax()){
            $search = $request->search;
            $kotaid = $request->kotaid;
            $jenis = $request->jenis;
            $jenjang = $request->jenjang;
            $kecamatanid = $request->kecamatanid;
            $sekolahid = Auth::user()->isSekolah() ? auth('sanctum')->user()->sekolahid : $request->sekolahid;

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);
            try {
                $sarpraskebutuhan = DB::table('tbtranssarpraskebutuhan')
                    ->join('tbmsekolah', function($join)
                    {
                        $join->on('tbmsekolah.sekolahid', '=', 'tbtranssarpraskebutuhan.sekolahid');
                        $join->on('tbmsekolah.dlt','=', DB::raw("'0'"));
                    })
                    ->join('tbmnamasarpras', function($join)
                    {
                        $join->on('tbmnamasarpras.namasarprasid', '=', 'tbtranssarpraskebutuhan.namasarprasid');
                        $join->on('tbmnamasarpras.dlt', '=', DB::raw("'0'"));
                    })
                    ->join('tbmpegawai', function($join)
                    {
                        $join->on('tbmpegawai.pegawaiid', '=', 'tbtranssarpraskebutuhan.pegawaiid');
                        $join->on('tbmpegawai.dlt', '=', DB::raw("'0'"));
                    })
                    ->select('tbtranssarpraskebutuhan.*', 'tbmsekolah.*', 'tbmnamasarpras.*', 'tbmpegawai.pegawaiid', 'tbmpegawai.nama', 'tbmpegawai.nip')
                    ->where('tbtranssarpraskebutuhan.dlt', DB::raw("'0'"))
                    ->whereRaw(
                        'tbtranssarpraskebutuhan.status >= '.enum::STATUS_KEBUTUHAN_SARPRAS_PROSES_TENDER.''
                    )
                    // ->where('tbtranssarpraskebutuhan.status', '=', enum::STATUS_KEBUTUHAN_SARPRAS_PROSES_TENDER)
                    // ->orWhere('tbtranssarpraskebutuhan.status', '=', enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_PEMBANGUNAN)
                    ->where(function($query){
                        if(Auth::user()->isSekolah()) $query->where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid);
                    })
                    ->where(function($query) use ($kotaid, $jenis, $jenjang, $kecamatanid, $sekolahid, $search)
                        {
                            // if (!is_null($provid) && $provid!='') $query->where('tbmkota.provid', $provid);
                            if (!is_null($kotaid) && $kotaid!='') $query->where('tbmsekolah.kotaid', $kotaid);
                            if (!is_null($jenis) && $jenis!='') $query->where('tbmsekolah.jenis', $jenis);
                            if (!is_null($jenjang) && $jenjang!='') $query->where('tbmsekolah.jenjang', $jenjang);
                            if (!is_null($kecamatanid) && $kecamatanid!='') $query->where('tbmsekolah.kecamatanid', $kecamatanid);
                            if (!is_null($sekolahid) && $sekolahid!='') $query->where('tbmsekolah.sekolahid', $sekolahid);

                            if (!is_null($search) && $search!='') {
                                // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                                $query->where(DB::raw('tbmsekolah.namasekolah'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbtranssarpraskebutuhan.namasarprasid'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbtranssarpraskebutuhan.jenissarpras'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbtranssarpraskebutuhan.nopengajuan'), 'ilike', '%'.$search.'%');
                            }
                        })
                    ->orderBy('tbtranssarpraskebutuhan.nopengajuan')
                ;
                $count = $sarpraskebutuhan->count();
                $data = $sarpraskebutuhan->skip($page)->take($perpage)->get();

            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count,
            ], 'Sarpras kebutuhan retrieved successfully.');  
        }

        $kota = DB::table('tbmkota')
            ->select('tbmkota.kotaid', 'tbmkota.kodekota', 'tbmkota.namakota')
            ->where('tbmkota.dlt', 0)
            ->orderBy('tbmkota.kodekota')
            ->get()
        ;
        $sekolah = DB::table('tbmsekolah')
            ->select('tbmsekolah.sekolahid', 'tbmsekolah.namasekolah')
            ->where('tbmsekolah.dlt', 0)
            ->orderBy('tbmsekolah.namasekolah')
            ->get()
        ;

        $kecamatan = DB::table('tbmkecamatan')
            ->select('tbmkecamatan.kecamatanid', 'tbmkecamatan.kodekec', 'tbmkecamatan.namakec')
            ->where('tbmkecamatan.dlt', 0)
            ->orderBy('tbmkecamatan.kodekec')
            ->get()
        ;

        $subkegiatan = DB::table('tbmsubkeg')
            ->join('tbmkeg', function ($join){
                $join->on('tbmkeg.kegid', '=', 'tbmsubkeg.kegid');
                $join->on('tbmkeg.dlt', '=', DB::raw("'0'"));
            })
            ->join('tbmprog', function ($join){
                $join->on('tbmprog.progid', '=', 'tbmkeg.progid');
                $join->on('tbmprog.dlt', '=', DB::raw("'0'"));
            })
            ->select('tbmsubkeg.*', 'tbmkeg.kegkode', 'tbmprog.progkode')
            ->where('tbmsubkeg.dlt', 0)
            ->get()
        ;

        $perusahaan = DB::table('tbmperusahaan')
            ->select('tbmperusahaan.*')
            ->where('tbmperusahaan.dlt', 0)
            ->get()
        ;

        $userSekolah = Sekolah::where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid == null ? -1 : auth('sanctum')->user()->sekolahid)->first();

        return view(
            'perencanaansarpras.tender.index', 
            [
                'page' => $this->page, 
                // 'createbutton' => true, 
                // 'createurl' => route('sarpraskebutuhan.createBySekolahId', ['sekolahid' => ':sekolahid']), 
                'kota' => $kota,
                'sekolah' => $sekolah,
                'kecamatan' => $kecamatan,
                'subkegiatan' => $subkegiatan,
                'perusahaan' => $perusahaan,
                'isSekolah' => Auth::user()->isSekolah(),
                'userSekolah' => $userSekolah
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

    public function detailanggaran(Request $request, $sarpraskebutuhanid)
    {
        $this->authorize('view-20');

        if($request->ajax())
        {
            $data = [];
            $totalPagu = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $detailpenganggaran = DB::table('tbdetailpenganggaran')
                    ->join('tbmsubkeg', function($join){
                        $join->on('tbmsubkeg.subkegid', '=', 'tbdetailpenganggaran.subkegid');
                    })
                    ->select('tbdetailpenganggaran.*', 'tbmsubkeg.subkegnama')
                    ->where('tbdetailpenganggaran.sarpraskebutuhanid', $sarpraskebutuhanid)
                    ->where('tbdetailpenganggaran.dlt', 0)
                ;

                $queryTotalPagu = DB::table('tbdetailpenganggaran')
                    ->join('tbdetailpagupenganggaran', function($join){
                        $join->on('tbdetailpagupenganggaran.detailpenganggaranid', 'tbdetailpenganggaran.detailpenganggaranid');
                    })
                    ->select(DB::raw('SUM(tbdetailpagupenganggaran.nilaipagu) as countpagu'))
                    ->where('tbdetailpenganggaran.sarpraskebutuhanid', $sarpraskebutuhanid)
                ;

                $count = $detailpenganggaran->count();
                $data = $detailpenganggaran->skip($page)->take($perpage)->get();
                $totalPagu = $queryTotalPagu->get();

            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count,
                'totalPagu' => $totalPagu,
            ], 'Detail penganggaran retrieved successfully.');  
        }
    }

    public function storeDetailPenganggaran(Request $request, $id)
    {
        $this->authorize('add-20');

        $user = auth('sanctum')->user();

        try {
                $tglnow = date('Y-m-d');
                DB::beginTransaction();
                // Save multiple and file 
                $modelDetailPaguSarpras = DetailPaguPenganggaran::find($id);

                $modelDetailPaguSarpras->nokontrak = $request->nokontrak;
                $modelDetailPaguSarpras->nilaikontrak = str_replace('.', '', $request->nilaikontrak);
                $modelDetailPaguSarpras->perusahaanid = $request->perusahaanid;
                $modelDetailPaguSarpras->tgldari = $request->tgldari;
                $modelDetailPaguSarpras->tglsampai = $request->tglsampai;

                if ($request->hasFile('file')) {
                    if($modelDetailPaguSarpras->file != null) {
                        $file_old = public_path().'/storage/uploaded/sarprastersedia/detailsarpras/'.$modelDetailPaguSarpras->file;
                        unlink($file_old);
                    }
                    $fileName = $tglnow.'_'.rand(1,1000).'_'.$request->file('file')->getClientOriginalName();   
                    $filePath = $request->file('file')->storeAs('public/uploaded/sarprastersedia/detailsarpras', $fileName);   
                    $modelDetailPaguSarpras->file = $fileName;
                }

                $modelDetailPaguSarpras->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

                $modelDetailPaguSarpras->save();

                DB::commit();
                
                return response([
                    'success' => true,
                    'data'    => 'Success',
                    'message' => 'detail penganggaran sarpras added successfully.',
                ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function progrespembangunan(Request $request, $id)
    {
        $this->authorize('edit-20');
        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $sarpraskebutuhan = SarprasKebutuhan::where('sarpraskebutuhanid', $id)
                ->where('dlt', 0)
                ->firstOrFail()
            ;

            // $detailpaguanggaran = DetailPaguPenganggaran::where('detailpenganggaranid', $detailpaguanggaran->detailpenganggaranid);

            if($sarpraskebutuhan->status != enum::STATUS_KEBUTUHAN_SARPRAS_PROSES_TENDER)
            {
                return response([
                    'success' => false,
                    'data' => 'error',
                    'message' => 'Tidak dapat melakukan progres pembangunan'
                ]);
            }
            else
            {
                $sarpraskebutuhan->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_PEMBANGUNAN;
                $sarpraskebutuhan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

                if($sarpraskebutuhan->save())
                {
                    $statuskebutuhansarpras = new StatusKebutuhanSarpras;
                    $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $statuskebutuhansarpras->sarpraskebutuhanid = $sarpraskebutuhan->sarpraskebutuhanid;
                    $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_PEMBANGUNAN;
                    $statuskebutuhansarpras->tgl = date('Y-m-d');

                    $statuskebutuhansarpras->save();
                }

                DB::commit();

                return response([
                    'success' => true,
                    'data'    => 'Success',
                    'message' => 'Berhasil mengubah status menjadi progres pembangunan.',
                ], 200);
            }
        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function batalprogrespembangunan(Request $request, $id)
    {
        $this->authorize('edit-20');
        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $sarpraskebutuhan = SarprasKebutuhan::where('sarpraskebutuhanid', $id)
                ->where('dlt', 0)
                ->firstOrFail()
            ;

            if($sarpraskebutuhan->status != enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_PEMBANGUNAN)
            {
                return response([
                    'success' => false,
                    'data' => 'error',
                    'message' => 'Tidak dapat membatalkan progres pembangunan'
                ]);
            }
            else
            {
                $sarpraskebutuhan->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROSES_TENDER;
                $sarpraskebutuhan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

                if($sarpraskebutuhan->save())
                {
                    $statuskebutuhansarpras = new StatusKebutuhanSarpras;
                    $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $statuskebutuhansarpras->sarpraskebutuhanid = $sarpraskebutuhan->sarpraskebutuhanid;
                    $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROSES_TENDER;
                    $statuskebutuhansarpras->tgl = date('Y-m-d');

                    $statuskebutuhansarpras->save();
                }

                DB::commit();

                return response([
                    'success' => true,
                    'data'    => 'Success',
                    'message' => 'Berhasil membatalkan progres pembangunan.',
                ], 200);
            }
        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }
}
