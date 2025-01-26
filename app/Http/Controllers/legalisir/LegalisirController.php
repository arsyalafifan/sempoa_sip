<?php

namespace App\Http\Controllers\legalisir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\legalisir\CreateRequest;
use App\Models\legalisir\Legalisir;
use App\Models\master\Ijazah;
use App\Models\master\Ijazahtemp;
use App\Models\master\Pegawai;
use App\Models\master\Sekolah;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator; 
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\File;

class LegalisirController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Legalisir Ijazah';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-14');
        $user = auth('sanctum')->user();

        Log::channel('mibedil')->info('Halaman '.$this->page);

        $sekolah = [];
        $legalisir = [];
        $ijazahtemp = [];
        $kota = [];

        if($request->ajax())
        {
            $search = $request->search;
            $kotaid = $request->kotaid;
            $sekolahid = Auth::user()->isSekolah() ? auth('sanctum')->user()->sekolahid : $request->sekolahid;

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {

                $ijazahtemp = DB::table('tbmijazahtemp')
                ->leftjoin('tbmsekolah', function($join) {
                    $join->on('tbmsekolah.sekolahid', '=', 'tbmijazahtemp.sekolahid');
                    $join->on('tbmsekolah.dlt','=',DB::raw("'0'"));
                })
                ->leftjoin('tbmkota', function($join) {
                    $join->on('tbmkota.kotaid', '=', 'tbmsekolah.kotaid');
                    $join->on('tbmkota.dlt','=',DB::raw("'0'"));
                })
                ->select(DB::raw('0 as legalisirid'), 'tbmijazahtemp.ijazahid', 'tbmijazahtemp.namasiswa', 'tbmijazahtemp.noijazah', DB::raw('COALESCE(tbmijazahtemp.namasekolah, tbmsekolah.namasekolah) AS namasekolah'), 'tbmijazahtemp.keterangan', 'tbmijazahtemp.tgl_pengajuan', 'tbmijazahtemp.status')
                ->where('tbmijazahtemp.dlt', '0')
                ->whereIn(DB::raw('(tbmijazahtemp.ijazahid, tbmijazahtemp.tgladd)'), function ($query) {
                    $query->select('ijazahid', DB::raw('MAX(tgladd)'))
                        ->from('tbmijazahtemp')
                        ->where('tbmijazahtemp.dlt', '0')
                        ->groupBy('ijazahid');
                })
                ->where('tbmijazahtemp.dlt', '0')
                ->where(function($query){
                    if(Auth::user()->isSekolah()) $query->where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid);
                })
                ->where(function($query) use ($sekolahid, $kotaid, $search) {
                    if (!is_null($kotaid) && $kotaid!='') $query->where('tbmsekolah.kotaid', $kotaid);
                    if (!is_null($sekolahid) && $sekolahid!='') $query->where('tbmijazahtemp.sekolahid', $sekolahid);

                    if (!is_null($search) && $search!='') {
                        $query->where(DB::raw('tbmijazahtemp.namasiswa'), 'ilike', '%'.$search.'%');
                        $query->orWhere(DB::raw('tbmijazahtemp.noijazah'), 'ilike', '%'.$search.'%');
                    }
                });

                $legalisir = DB::table('tbmlegalisir')
                ->join('tbmijazah', function($join) {
                    $join->on('tbmijazah.ijazahid', '=', 'tbmlegalisir.ijazahid');
                    $join->on('tbmijazah.dlt','=',DB::raw("'0'"));
                })
                ->leftjoin('tbmsekolah', function($join) {
                    $join->on('tbmsekolah.sekolahid', '=', 'tbmijazah.sekolahid');
                    $join->on('tbmsekolah.dlt','=',DB::raw("'0'"));
                })
                ->leftjoin('tbmkota', function($join) {
                    $join->on('tbmkota.kotaid', '=', 'tbmsekolah.kotaid');
                    $join->on('tbmkota.dlt','=',DB::raw("'0'"));
                })
                ->select('tbmlegalisir.legalisirid', 'tbmlegalisir.ijazahid', 'tbmijazah.namasiswa', 'tbmijazah.noijazah', DB::raw('COALESCE(tbmijazah.namasekolah, tbmsekolah.namasekolah) AS namasekolah'), 'tbmlegalisir.keterangan', 'tbmlegalisir.tgl_pengajuan', 'tbmlegalisir.status')
                ->where('tbmijazah.dlt', '0')
                ->whereIn(DB::raw('(tbmlegalisir.ijazahid, tbmlegalisir.tgladd)'), function ($query) {
                    $query->select('ijazahid', DB::raw('MAX(tgladd)'))
                        ->from('tbmlegalisir')
                        ->where('tbmlegalisir.dlt', '0')
                        ->groupBy('ijazahid');
                })
                ->where('tbmlegalisir.dlt', '0')
                ->where(function($query){
                    if(Auth::user()->isSekolah()) $query->where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid);
                })
                ->where(function($query) use ($sekolahid, $kotaid, $search) {
                    if (!is_null($kotaid) && $kotaid!='') $query->where('tbmsekolah.kotaid', $kotaid);
                    if (!is_null($sekolahid) && $sekolahid!='') $query->where('tbmijazah.sekolahid', $sekolahid);

                    if (!is_null($search) && $search!='') {
                        $query->where(DB::raw('tbmijazah.namasiswa'), 'ilike', '%'.$search.'%');
                        $query->orWhere(DB::raw('tbmijazah.noijazah'), 'ilike', '%'.$search.'%');
                    }
                });

                // Gabungkan kedua query menggunakan union
                $result = $ijazahtemp->union($legalisir)->orderBy('tgl_pengajuan', 'desc')->get();    
                $count = $result->count();
                $data = $result;
                
            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'Legalisir retrieved successfully.');  
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

        $userSekolah = Sekolah::where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid == null ? -1 : auth('sanctum')->user()->sekolahid)->first();

        return view(
            'verifikasi.legalisirijazah.index', 
            [
                'page' => $this->page, 
                'createbutton' => false, 
                'createurl' =>  route('ijazah.createWithSekolah',['provId' => ':provid','sekolahId' => ':id']) , 
                'legalisir' => $legalisir, 
                'kota' => $kota,
                'sekolah' => $sekolah,
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
        // Create a new resource
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        // guest dapat menambah data legalisir(public akses)
        try{

            $model = new Legalisir();
            $tglnow = date("Y-m-d");

            DB::beginTransaction();
            $results = Ijazah::where('ijazahid', '=',  $request->ijazahid)->first();
            $model->ijazahid = $request->ijazahid;
            $model->tgl_pengajuan = $tglnow;
            $model->status = 0;
            $model->file_legalisir = null;
            $filename = $tglnow.'_'.rand(1,1000).'_'.$results->namasiswa.'.pdf';
            
            if ($request->file('file_ijazah')) {
                $request->file('file_ijazah')->storeAs('public/uploaded/ijazah', $filename);
                $model->file_ijazah = 'uploaded/ijazah/'. $filename;
            }
            if ($request->file('file_ktp')) {
                $request->file('file_ktp')->storeAs('public/uploaded/ktp', $filename);
                $model->file_ktp = 'uploaded/ktp/'. $filename;
            }

            $model->opadd = $results->namasiswa;
            $model->pcadd = $request->ip();

            if ($model->save()) {
                
            }

            DB::commit();

            return back()->withInput(['query' => $results->noijazah])->with('berhasil', 'Data pengajuan berhasil di uplaod.');

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
        // Get the resource
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        // Get the resource
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
        //update
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Delete the resource
    }

    public function tolak(Request $request, $id, $ijazahid)
    {
        $this->authorize('edit-14');
        $user = auth('sanctum')->user();
        if($id == 0){
            $ijazahtemp = Ijazahtemp::where('ijazahid', $ijazahid)
            ->where('dlt', 0)
            ->first();
            $ijazahtemp->status= enum::IJAZAH_DITOLAK;
            $ijazahtemp->keterangan = $request->keterangan;

            $ijazahtemp->pcedit = $request->ip();
            $ijazahtemp->opedit = $user->login;
            $ijazahtemp->save();

        }else{
            $legalisir = Legalisir::where('legalisirid', $id)
            ->where('dlt', 0)
            ->first();

            $legalisir->status= enum::LEGALISIR_DITOLAK;
            $legalisir->keterangan = $request->keterangan;

            $legalisir->pcedit = $request->ip();
            $legalisir->opedit = $user->login;
            $legalisir->save();
        }

        return redirect()->route('legalisir.index')->with('success', 'Data Permintaan Legalisir Berhasil Di tolak');
    }

    public function setuju(Request $request, $id, $ijazahid)
    {
        $this->authorize('edit-14');
        $user = auth('sanctum')->user();
        if($id == 0){
            
            $ijazahtemp = Ijazahtemp::where('ijazahid', $ijazahid)
            ->where('dlt', 0)
            ->first();

            $user = auth('sanctum')->user();
            $model = new Ijazah();
            $tglnow = date("Y-m-d");

            // DB::beginTransaction();
            $model->namasiswa = $ijazahtemp->namasiswa;
            $model->tempat_lahir = $ijazahtemp->tempat_lahir;
            $model->tgl_lahir = $ijazahtemp->tgl_lahir;
            $model->namaortu = $ijazahtemp->namaortu;
            $model->nis = $ijazahtemp->nis;
            $model->noijazah = $ijazahtemp->noijazah;
            $model->tgl_lulus = $ijazahtemp->tgl_lulus;
            $model->sekolahid = $ijazahtemp->sekolahid;
            $model->provinsiid = $ijazahtemp->provinsiid;
            $model->namaprov = $ijazahtemp->sekolahid == null ? $ijazahtemp->namaprov : null;
            $model->namakab = $ijazahtemp->sekolahid == null ? $ijazahtemp->namakab : null;
            $model->namakec = $ijazahtemp->sekolahid == null ? $ijazahtemp->namakec : null;
            $model->namasekolah = $ijazahtemp->sekolahid == null ? $ijazahtemp->namasekolah : null;
            $filename = $tglnow.'_'.rand(1,1000).'_'.$ijazahtemp->namasiswa.'.pdf';
        
            // $destinationDirectory = storage_path('app/public/uploaded/ijazah/');

            // if (!File::exists($destinationDirectory)) {
            //     File::makeDirectory($destinationDirectory, 0777, true, true);
            // }
            // Sekarang jalankan File::copy
            // File::copy($ijazahPath, $destinationDirectory . $filename);
            $model->file_ijazah = $ijazahtemp->file_ijazah;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            if($model->save()){
                $ijazahtemp->status = enum::IJAZAH_DISETUJUI;
                $ijazahtemp->pcedit = $request->ip();
                $ijazahtemp->opedit = $user->login;
                $ijazahtemp->save();
            }
        }else{
            // Legalisir
            $legalisir = Legalisir::where('legalisirid', $id)
            ->where('dlt', 0)
            ->firstOrFail(); 

            // Ijazah
            $ijazah = Ijazah::where('ijazahid', $legalisir->ijazahid)
            ->where('dlt', 0)
            ->firstOrFail(); 

            // Pegawai
            $pegawai = Pegawai::where('pegawaiid', $request->pegawaiid)
            ->where('dlt', 0)
            ->firstOrFail(); 

            $legalisir->status = enum::LEGALISIR_DISETUJUI;
            $legalisir->pegawaiid = $request->pegawaiid;
            $legalisir->keterangan = null;

            $tglnow = date('Y-m-d');
            $ijazahPath = storage_path('app/public/' . $legalisir->file_ijazah); 
            $ttdPegawai = storage_path('app/public/' . $pegawai->file_ttd); 

            $filename = $tglnow . '_' . rand(1, 1000) . '_' . $ijazah->namasiswa . '_Legalisir.pdf';

            // Create a new PDF instance
            $pdf = new Fpdi();
            $pdf->AddPage();

            // Load file PDF yang ada 
            $pageCount = $pdf->setSourceFile($ijazahPath);
            $templateId = $pdf->importPage(1);
            $pdf->useTemplate($templateId);

            // Asumsikan lebar dan tinggi halaman
            $pageWidth = 210; // size A4 dalam milimeter
            $pageHeight = 297;

            // Atur tinggi dan lebar gambar
            $imageWidth = 30; 
            $imageHeight = 30; 

            // Kalkulasi tingggi page dan image
            $imageX = $pageWidth - $imageWidth; // koordinat X
            $imageY = $pageHeight - $imageHeight; // koordinat Y

            // Tambahkan image ke pdf yang ada 
            $pdf->Image($ttdPegawai, $imageX, $imageY, $imageWidth, $imageHeight);

            // Tentukan lokasi penyimpanan file
            $legalisirPath = storage_path('app/public/uploaded/legalisir/' . $filename);

            // Buat baru jika belum ada
            if (!is_dir(dirname($legalisirPath))) {
                mkdir(dirname($legalisirPath), 0755, true);
            }

            // Simpan file dengan lokasi yang sudah ditentukan 
            $pdf->Output($legalisirPath, 'F'); 

            $legalisir->file_legalisir = 'uploaded/legalisir/' . $filename;
            $legalisir->status = enum::LEGALISIR_DISETUJUI;
            $legalisir->pegawaiid = $request->pegawaiid;
            $legalisir->keterangan = null;
            $legalisir->pcedit = $request->ip();
            $legalisir->opedit = $user->login;
            $legalisir->save();
        }
        
        return redirect()->route('legalisir.index')->with('success', 'Data Permintaan Legalisir Berhasil Disetujui');

    }


    public function history(Request $request, $legalisirid, $ijazahid)
    {
        $this->authorize('view-14');
        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        $sekolah = [];
        $legalisir = [];
        $kota = [];
        
            if($request->ajax())
            {

                $data = [];
                $count = 0;
                $page = $request->get('start', 0);  
                $perpage = $request->get('length',50);

                try {
                        if($legalisirid == 0){
                        $legalisir = DB::table('tbmijazahtemp')
                                ->select(DB::raw('0 as legalisirid'),'tbmijazahtemp.*')
                                ->where('tbmijazahtemp.dlt', '0')
                                ->where('tbmijazahtemp.ijazahid', $ijazahid)
                                ->where('tbmijazahtemp.dlt', '0')
                                ->orderBy('tbmijazahtemp.tgladd', 'desc')
                        ;
                        }else{
                            $legalisir = DB::table('tbmlegalisir')
                            ->join('tbmijazah', function($join)
                            {
                                $join->on('tbmijazah.ijazahid', '=', 'tbmlegalisir.ijazahid');
                                $join->on('tbmijazah.dlt','=',DB::raw("'0'"));
                            })
                            ->select('tbmlegalisir.*', 'tbmijazah.namasiswa', 'tbmijazah.noijazah')
                            ->where('tbmijazah.dlt', '0')
                            ->where('tbmijazah.ijazahid', $ijazahid)
                            ->where('tbmlegalisir.dlt', '0')
                            ->orderBy('tbmlegalisir.tgladd', 'desc')
                    ;
                        }
                    $count = $legalisir->count();
                    $data = $legalisir->skip($page)->take($perpage)->get();
                }catch (QueryException $e) {
                    return $this->sendError('SQL Error', $this->getQueryError($e));
                }
                catch (Exception $e) {
                    return $this->sendError('Error', $e->getMessage());
                }
                return $this->sendResponse([
                    'data' => $data,
                    'count' => $count
                ], 'Legalisir retrieved successfully.');  
            }
        return view(
            'verifikasi.legalisirijazah.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' =>  route('ijazah.createWithSekolah',['provId' => ':provid','sekolahId' => ':id']) , 
                'history' => $legalisir, 
                'kota' => $kota,
                'sekolah' => $sekolah,
            ]);
    }

    public function viewpengajuan($id)
    {   
        $this->authorize('view-14');
        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);
        $dataLegalisir = Legalisir::where('legalisirid', $id)
        ->where('dlt', 0)
        ->first();

        $pegawai = DB::table('tbmpegawai')
        ->select('tbmpegawai.pegawaiid', 'tbmpegawai.nama', 'tbmpegawai.jabatan')
        ->where('tbmpegawai.dlt', 0)
        ->where('tbmpegawai.status', 1)
        ->where('tbmpegawai.unit', enum::UNIT_OPD)
        ->orderBy('tbmpegawai.nama', 'ASC')
        ->orderBy('tbmpegawai.jabatan', 'ASC')
        ->get();

        return view('verifikasi.legalisirijazah.show', ['page' => $this->page, 'child' => 'Lihat Data Pengajuan Legalisir Ijazah', 'masterurl' => route('legalisir.index'), 'results' => $dataLegalisir, 'pegawai' => $pegawai]);
    }

    public function viewpengajuan2($id)
    {   
        $this->authorize('view-14');
        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);
        $dataIjazah = Ijazahtemp::where('ijazahid', $id)
        ->where('dlt', 0)
        ->first();

        if ($dataIjazah->provinsiid == enum::PROVINSI_LAINNYA){
            $dataIjazah->provinsiid = enum::PROVINSI_LAINNYA;
        }else{
            $sekolah = Sekolah::where('sekolahid', $dataIjazah->sekolahid)
            ->where('dlt', 0)
            ->firstOrFail();
            $dataIjazah->namasekolah = $sekolah->namasekolah;
            $dataIjazah->namakec = $sekolah->kecamatan->namakec;
            $dataIjazah->namakab = $sekolah->kota->namakota;
            $dataIjazah->namaprov = strtoupper(enum::PROVINSI_DESC_KEPRI);
            $dataIjazah->provinsiid = enum::PROVINSI_KEPRI;
            
        }
        $legalisirid = 0;
        $pegawai = DB::table('tbmpegawai')
        ->select('tbmpegawai.pegawaiid', 'tbmpegawai.nama', 'tbmpegawai.jabatan')
        ->where('tbmpegawai.dlt', 0)
        ->where('tbmpegawai.status', 1)
        ->where('tbmpegawai.unit', enum::UNIT_OPD)
        ->orderBy('tbmpegawai.nama', 'ASC')
        ->orderBy('tbmpegawai.jabatan', 'ASC')
        ->get();

        return view('verifikasi.legalisirijazah.show2', ['page' => $this->page, 'child' => 'Lihat Data Pengajuan Legalisir Ijazah', 'masterurl' => route('legalisir.index'), 'results' => $dataIjazah, 'pegawai' => $pegawai, 'legalisirid' => $legalisirid]);
    }

}
