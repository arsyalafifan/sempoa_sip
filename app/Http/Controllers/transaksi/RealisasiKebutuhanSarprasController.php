<?php

namespace App\Http\Controllers\transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\transaksi\Realisasi\CreateRequest;
use App\Http\Requests\transaksi\Realisasi\UpdateRequest;
use App\Models\master\Sekolah;
use App\Models\transaksi\RealisasiKebutuhanSarpras;
use Illuminate\Support\Facades\Auth;

class RealisasiKebutuhanSarprasController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Realisasi';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-28');

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
                    ->leftJoin('tbdetailpenganggaran', function($join){
                        $join->on('tbdetailpenganggaran.sarpraskebutuhanid', '=', 'tbtranssarpraskebutuhan.sarpraskebutuhanid');
                        $join->on('tbdetailpenganggaran.dlt', '=', DB::raw("'0'"));
                    })
                    ->join('tbmsubkeg', function($join)
                    {
                        $join->on('tbmsubkeg.subkegid', '=', 'tbdetailpenganggaran.subkegid');
                        $join->on('tbmsubkeg.dlt', '=', DB::raw("'0'"));
                    })
                    ->select(
                        'tbtranssarpraskebutuhan.sarpraskebutuhanid', 
                        'tbtranssarpraskebutuhan.nopengajuan', 
                        'tbtranssarpraskebutuhan.tglpengajuan', 
                        'tbtranssarpraskebutuhan.namasarprasid', 
                        'tbtranssarpraskebutuhan.jenissarpras', 
                        'tbtranssarpraskebutuhan.jeniskebutuhan', 
                        'tbtranssarpraskebutuhan.jumlahsetuju', 
                        'tbtranssarpraskebutuhan.satuansetuju', 
                        'tbtranssarpraskebutuhan.status', 
                        'tbmsekolah.namasekolah', 
                        'tbmnamasarpras.namasarpras', 
                        'tbmsubkeg.subkegnama', 
                        'tbdetailpenganggaran.subdetailkegiatan', 
                    )
                    ->whereIn(DB::raw('(tbdetailpenganggaran.sarpraskebutuhanid, tbdetailpenganggaran.tgladd)'), function ($query) {
                        $query->select('sarpraskebutuhanid', DB::raw('MAX(tgladd)'))
                            ->from('tbdetailpenganggaran')
                            ->where('tbdetailpenganggaran.dlt', '0')
                            ->groupBy('sarpraskebutuhanid');
                    })
                    ->where('tbtranssarpraskebutuhan.dlt', DB::raw("'0'"))
                    // ->where('tbtranssarpraskebutuhan.status', '=', enum::STATUS_KEBUTUHAN_SARPRAS_PROSES_TENDER)
                    ->where('tbtranssarpraskebutuhan.status', '>=', enum::STATUS_KEBUTUHAN_SARPRAS_PROGRES_PEMBANGUNAN)
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
                                $query->orWhere(DB::raw('tbmnamasarpras.namasarpras'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmsubkeg.subkegnama'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbdetailpenganggaran.subdetailkegiatan'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbtranssarpraskebutuhan.nopengajuan'), 'ilike', '%'.$search.'%');
                            }
                        })
                    ->orderBy('tbtranssarpraskebutuhan.nopengajuan')
                ;
                $count = $sarpraskebutuhan->count();
                $data = $sarpraskebutuhan->skip($page)->take($perpage)->get();

                return $this->sendResponse([
                    'data' => $data,
                    'count' => $count,
                ], 'Sarpras kebutuhan retrieved successfully.');  

            } catch (QueryException $e) {
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
            'transaksi.realisasi.index', 
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
    public function create(Request $request)
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

    public function loadRealisasi(Request $request, $id)
    {

        $this->authorize('view-28');

        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $realisasikebutuhansarpras = DB::table('tbrealisasikebutuhansarpras')
                    ->select(
                        'tbrealisasikebutuhansarpras.realisasiid',
                        'tbrealisasikebutuhansarpras.sarpraskebutuhanid', 
                        'tbrealisasikebutuhansarpras.nosp2d',
                        'tbrealisasikebutuhansarpras.tglsp2d',
                        'tbrealisasikebutuhansarpras.nilaisp2d',
                        'tbrealisasikebutuhansarpras.keterangan',
                        'tbrealisasikebutuhansarpras.file',
                    )
                    ->where('tbrealisasikebutuhansarpras.sarpraskebutuhanid', $id)
                    ->where('tbrealisasikebutuhansarpras.dlt', 0)
                    ->orderBy('tbrealisasikebutuhansarpras.realisasiid')
                ;

                $count = $realisasikebutuhansarpras->count();
                $data = $realisasikebutuhansarpras->skip($page)->take($perpage)->get();

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

    public function storeRealisasi(CreateRequest $request, $id)
    {
        $this->authorize('add-28');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');

        try {
            DB::beginTransaction();

            $realisasiModel = new RealisasiKebutuhanSarpras;


            $realisasiModel->nosp2d = $request->nosp2d;
            $realisasiModel->tglsp2d = $request->tglsp2d;
            $realisasiModel->nilaisp2d = str_replace('.', '', $request->nilaisp2d);
            $realisasiModel->keterangan = $request->keterangan;
            $realisasiModel->sarpraskebutuhanid = $id;

            if ($request->hasFile('file')) {
                $fileName = $tglnow.'_'.rand(1,10000).'_'.$request->file('file')->getClientOriginalName();   
                $filePath = $request->file('file')->storeAs('public/uploaded/sarpraskebutuhan', $fileName);   
                $realisasiModel->file = $fileName;
            }

            $realisasiModel->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $realisasiModel->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'realisasi added successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function updateRealisasi(UpdateRequest $request, $id)
    {
        $this->authorize('edit-28');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');

        try {
            DB::beginTransaction();

            $realisasiModel = RealisasiKebutuhanSarpras::find($id);

            $realisasiModel->nosp2d = $request->nosp2d;
            $realisasiModel->tglsp2d = $request->tglsp2d;
            $realisasiModel->nilaisp2d = str_replace('.', '', $request->nilaisp2d);
            $realisasiModel->keterangan = $request->keterangan;

            if ($request->hasFile('file')) {
                $fileName = $tglnow.'_'.rand(1,10000).'_'.$request->file('file')->getClientOriginalName();   
                $filePath = $request->file('file')->storeAs('public/uploaded/sarpraskebutuhan', $fileName);   
                $realisasiModel->file = $fileName;
            }

            $realisasiModel->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $realisasiModel->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'realisasi updated successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function deleteRealisasi(Request $request, $id)
    {
        $this->authorize('delete-28');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');

        try {
            DB::beginTransaction();

            $realisasiModel = RealisasiKebutuhanSarpras::find($id);

            $realisasiModel->dlt = '1';
            $realisasiModel->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            if($realisasiModel->file != ''  && $realisasiModel->file != null){
                $file_old = public_path().'/storage/uploaded/sarpraskebutuhan/'.$realisasiModel->file;
                unlink($file_old);
            }
            $realisasiModel->file = null;

            $realisasiModel->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'realisasi deleted successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }
}
