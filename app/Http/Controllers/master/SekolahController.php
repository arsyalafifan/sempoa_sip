<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master\Sekolah\CreateRequest;
use App\Models\master\Akreditasi;
use App\Models\master\JumlahGuru;
use App\Models\master\JumlahRombel;
use App\Models\master\MasterPlanSekolah;
use App\Models\master\PesertaDidik;
// use App\Models\master\Akreditasi;
// use App\Models\master\Akreditasi;
use App\Models\master\Sekolah;
use App\Models\master\SertifikatLahan;
use Illuminate\Support\Facades\Response;

class SekolahController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Sekolah';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-4');

        Log::channel('mibedil')->info('Halaman '.$this->page);

        $sekolah = [];
        $kota = [];
        $kecamatan = [];
        $tahunajaran = [];

        if($request->ajax())
        {
            $search = $request->search;
            $kotaid = $request->kotaid;
            $kecamatanid = $request->kecamatanid;
            $jenjang = $request->jenjang;
            $jenis = $request->jenis;

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            // $sekolah = Sekolah::join()

            try {
                $sekolah = DB::table('tbmsekolah')
                        ->join('tbmkota', function($join)
                                {
                                    $join->on('tbmkota.kotaid', '=', 'tbmsekolah.kotaid');
                                    $join->on('tbmkota.dlt','=',DB::raw("'0'"));
                                })
                        ->join('tbmkecamatan', function($join)
                                {
                                    $join->on('tbmkecamatan.kecamatanid', '=', 'tbmsekolah.kecamatanid');
                                    $join->on('tbmkecamatan.dlt','=',DB::raw("'0'"));
                                })
                        ->select('tbmsekolah.*', 'tbmkota.namakota')
                        ->where('tbmsekolah.dlt', '0')
                        ->where(function($query) use ($kotaid, $kecamatanid, $jenjang, $jenis, $search)
                        {
                            // if (!is_null($provid) && $provid!='') $query->where('tbmkota.provid', $provid);
                            if (!is_null($kotaid) && $kotaid!='') $query->where('tbmsekolah.kotaid', $kotaid);
                            if (!is_null($kecamatanid) && $kecamatanid!='') $query->where('tbmsekolah.kecamatanid', $kecamatanid);
                            if (!is_null($jenjang) && $jenjang!='') $query->where('tbmsekolah.jenjang', $jenjang);
                            if (!is_null($jenis) && $jenis!='') $query->where('tbmsekolah.jenis', $jenis);

                            if (!is_null($search) && $search!='') {
                                // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                                $query->where(DB::raw('tbmsekolah.npsn'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmsekolah.namasekolah'), 'ilike', '%'.$search.'%');
                            }
                        })
                        ->orderBy('tbmsekolah.npsn')
                ;

                $count = $sekolah->count();
                $data = $sekolah->skip($page)->take($perpage)->get();
            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'Sekolah retrieved successfully.');  
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

        return view(
            'master.sekolah.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('sekolah.create'), 
                'sekolah' => $sekolah, 
                'kota' => $kota,
                'kecamatan' => $kecamatan,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $this->authorize('add-4');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
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

        $tahunajaran = DB::table('tbmtahunajaran')
            ->select('tbmtahunajaran.tahunajaranid', 'tbmtahunajaran.daritahun', 'tbmtahunajaran.sampaitahun')
            ->where('tbmtahunajaran.dlt', 0)
            ->orderBy('tbmtahunajaran.daritahun')
            ->get()
        ;
        
        return view(
            'master.sekolah.create', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('sekolah.index'), 
                'kota' => $kota,
                'kecamatan' => $kecamatan,
                'tahunajaran' => $tahunajaran
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $this->authorize('add-4');

        $user = auth('sanctum')->user();

        try{
            $user = auth('sanctum')->user();
            $model = new Sekolah();

            DB::beginTransaction();

            $model->namasekolah = $request->namasekolah;
            $model->npsn = $request->npsn;
            $model->jenjang = $request->jenjang;
            $model->jenis = $request->jenis;
            $model->alamat = $request->alamat;
            $model->kotaid = $request->kota;
            $model->kecamatanid = $request->kecamatan;
            $model->lintang = $request->lintang;
            $model->bujur = $request->bujur;
            $model->kurikulum = $request->kurikulum;
            $model->predikat = $request->predikat;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            if ($model->save()) {
                // Save multiple akreditasi data and image
                $files = [];
                $dataAkreditasi = [];

                foreach ($request->file('fileakreditasi') as $key => $value) {
                    // $fileName = time().'_'.rand(1,1000).'.'.$request->file('fileakreditasi')[$key]->extension();
                    // $filePath = $request->file('fileakreditasi')[$key]->move(public_path('uploaded/akreditasi'), $fileName);
                    // $files = $filePath;
                    $filePath = $request->file('fileakreditasi')[$key]->store('public/uploaded/fileakreditasi');
                    $files = $filePath;

                    $modelAkreditasi = new Akreditasi;
                    $dataAkreditasi = $request->akreditasi[$key];
                    $modelAkreditasi->akreditasi = $dataAkreditasi;

                    $modelAkreditasi->fileakreditasi = $files;
                    $modelAkreditasi->sekolahid = $model->sekolahid;
                    $modelAkreditasi->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $modelAkreditasi->save();
                }

                // Save multiple sertifikat lahan data and image
                $filesertifikatlahans = [];
                $datasertifikatlahan = [];

                foreach ($request->file('filesertifikatlahan') as $key => $value) {
                    // $fileName = time().'_'.rand(1,1000).'.'.$request->file('filesertifikatlahan')[$key]->extension();
                    // $filePath = $request->file('filesertifikatlahan')[$key]->move(public_path('uploaded/sertifikatlahan'), $fileName);
                    $filePath = $request->file('filesertifikatlahan')[$key]->store('public/uploaded/filesertifikatlahan');
                    $filesertifikatlahans = $filePath;

                    $modelSertifikatLahan = new SertifikatLahan;
                    $datasertifikatlahan = $request->sertifikatlahan[$key];
                    $modelSertifikatLahan->sertifikatlahan = $datasertifikatlahan;

                    $modelSertifikatLahan->filesertifikatlahan = $filesertifikatlahans;
                    $modelSertifikatLahan->sekolahid = $model->sekolahid;
                    $modelSertifikatLahan->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $modelSertifikatLahan->save();
                }

                // Save multiple master plan sekolah data and image
                $filemasterplans = [];
                $datamasterplan = [];

                foreach ($request->file('filemasterplan') as $key => $value) {
                    // $fileName = time().'_'.rand(1,1000).'.'.$request->file('filemasterplan')[$key]->extension();
                    // $filePath = $request->file('filemasterplan')[$key]->move(public_path('uploaded/masterplan'), $fileName);
                    $filePath = $request->file('filemasterplan')[$key]->store('public/uploaded/filemasterplan');
                    $filemasterplans = $filePath;

                    $modelMasterPlanSekolah = new MasterPlanSekolah();
                    $datamasterplan = $request->masterplan[$key];
                    $modelMasterPlanSekolah->masterplan = $datamasterplan;

                    $modelMasterPlanSekolah->filemasterplan = $filemasterplans;
                    $modelMasterPlanSekolah->sekolahid = $model->sekolahid;
                    $modelMasterPlanSekolah->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $modelMasterPlanSekolah->save();
                }


                // Save multiple jumlah guru data
                foreach ($request->statuspegawai as $key => $value) {

                    $modelJumlahGuru = new JumlahGuru;
                    $modelJumlahGuru->statuspegawai = $request->statuspegawai[$key];
                    $modelJumlahGuru->jumlahguru = $request->jumlahguru[$key];
                    $modelJumlahGuru->jeniskelamin = $request->jeniskelaminguru[$key];
                    $modelJumlahGuru->tahunajaranid = $request->tahunajaranguru[$key];
                    $modelJumlahGuru->sekolahid = $model->sekolahid;
                    $modelJumlahGuru->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $modelJumlahGuru->save();
                }

                // Save multiple jumlah peserta didik data
                foreach ($request->kelaspesertadidik as $key => $value) {

                    $modelKelasPesertaDidik = new PesertaDidik;
                    $modelKelasPesertaDidik->kelas = $request->kelaspesertadidik[$key];
                    $modelKelasPesertaDidik->jumlahpesertadidik = $request->jumlahpesertadidik[$key];
                    $modelKelasPesertaDidik->jeniskelamin = $request->jeniskelaminpesertadidik[$key];
                    $modelKelasPesertaDidik->tahunajaranid = $request->tahunajaranpesertadidik[$key];
                    $modelKelasPesertaDidik->sekolahid = $model->sekolahid;
                    $modelKelasPesertaDidik->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $modelKelasPesertaDidik->save();
                }

                // Save multiple jumlah rombel data
                foreach ($request->kelasrombel as $key => $value) {

                    $modelJumlahRombel = new JumlahRombel;
                    $modelJumlahRombel->kelas = $request->kelasrombel[$key];
                    $modelJumlahRombel->jumlahrombel = $request->jumlahrombel[$key];
                    $modelJumlahRombel->tahunajaranid = $request->tahunajaranrombel[$key];
                    $modelJumlahRombel->sekolahid = $model->sekolahid;
                    $modelJumlahRombel->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $modelJumlahRombel->save();
                }
            }

            // dd($request->all());

            DB::commit();

            return redirect()->route('sekolah.index')
            ->with('success', 'Data sekolah berhasil ditambah.', ['page' => $this->page]);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->authorize('edit-4');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $sekolah = Sekolah::where('sekolahid', $id)->firstOrFail();

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

        $tahunajaran = DB::table('tbmtahunajaran')
            ->select('tbmtahunajaran.tahunajaranid', 'tbmtahunajaran.daritahun', 'tbmtahunajaran.sampaitahun')
            ->where('tbmtahunajaran.dlt', 0)
            ->orderBy('tbmtahunajaran.daritahun')
            ->get()
        ;

        $akreditasiBySekolahId = DB::table('tbmsekolahakreditasi')
        ->select('tbmsekolahakreditasi.*')
        ->where('tbmsekolahakreditasi.sekolahid', $id)
        ->where('tbmsekolahakreditasi.dlt', 0)
        ->orderBy('tbmsekolahakreditasi.akreditasi')
        ->get();

        if($request->ajax())
        {
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            // akreditasi
            try {
                // $akreditasi = Akreditasi::find($id);
                $akreditasi = Akreditasi::where('sekolahid', $id)
                    ->where('dlt', '0')
                    ->orderBy('akreditasi')
                ;
                $countAkreditasi = $akreditasi->count();
                $akreditasi = $akreditasi->skip($page)->take($perpage)->get();
            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            // sertifikat lahan
            try {
                $sertifikatlahan = SertifikatLahan::where('sekolahid', $id)
                    ->where('dlt', '0')
                    ->orderBy('sertifikatlahan')
                ;

                $countSertifikatLahan = $sertifikatlahan->count();
                $sertifikatLahan = $sertifikatlahan->skip($page)->take($perpage)->get();

            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }


            // master plan sekolah
            try {
                $masterplan = MasterPlanSekolah::where('sekolahid', $id)
                    ->where('dlt', '0')
                    ->orderBy('masterplan')
                ;

                $countMasterPlan = $masterplan->count();
                $dataMasterPlan = $masterplan->skip($page)->take($perpage)->get();

            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            // jumlah guru
            try {
                $jumlahguru = DB::table('tbmsekolahjumlahguru')
                    ->join('tbmtahunajaran', function($join){
                        $join->on('tbmsekolahjumlahguru.tahunajaranid', '=', 'tbmtahunajaran.tahunajaranid');
                        $join->on('tbmtahunajaran.dlt','=',DB::raw("'0'"));
                    })
                    ->select('tbmsekolahjumlahguru.*', 'tbmtahunajaran.*')
                    ->where('tbmsekolahjumlahguru.sekolahid', $id)
                    ->where('tbmsekolahjumlahguru.dlt', '0')
                    ->orderBy('tbmsekolahjumlahguru.statuspegawai')
                ;

                $countJumlahGuru = $jumlahguru->count();
                $dataJumlahGuru = $jumlahguru->skip($page)->take($perpage)->get();

            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }


            // jumlah guru
            try {
                $jumlahpesertadidik = DB::table('tbmpesertadidik')
                    ->join('tbmtahunajaran', function($join){
                        $join->on('tbmpesertadidik.tahunajaranid', '=', 'tbmtahunajaran.tahunajaranid');
                        $join->on('tbmtahunajaran.dlt','=',DB::raw("'0'"));
                    })
                    ->select('tbmpesertadidik.*', 'tbmtahunajaran.*')
                    ->where('tbmpesertadidik.sekolahid', $id)
                    ->where('tbmpesertadidik.dlt', '0')
                    ->orderBy('tbmpesertadidik.kelas')
                ;

                $countPesertaDidik = $jumlahpesertadidik->count();
                $dataJumlahPesertaDidik = $jumlahpesertadidik->skip($page)->take($perpage)->get();

            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }


            // jumlah rombel
            try {
                $jumlahrombel = DB::table('tbmjumlahrombel')
                    ->join('tbmtahunajaran', function($join){
                        $join->on('tbmjumlahrombel.tahunajaranid', '=', 'tbmtahunajaran.tahunajaranid');
                        $join->on('tbmtahunajaran.dlt','=',DB::raw("'0'"));
                    })
                    ->select('tbmjumlahrombel.*', 'tbmtahunajaran.*')
                    ->where('tbmjumlahrombel.sekolahid', $id)
                    ->where('tbmjumlahrombel.dlt', '0')
                    ->orderBy('tbmjumlahrombel.kelas')
                ;

                $countRombel = $jumlahrombel->count();
                $dataJumlahRombel = $jumlahrombel->skip($page)->take($perpage)->get();

            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }


            return $this->sendResponse([
                'akreditasi' => $akreditasi,
                'countAkreditasi' => $countAkreditasi,
                'sertifikatLahan' => $sertifikatLahan,
                'countSertifikatLahan' => $countSertifikatLahan,
                'masterplan' => $dataMasterPlan,
                'countMasterPlan' => $countMasterPlan,
                'jumlahguru' => $dataJumlahGuru,
                'countJumlahGuru' => $countJumlahGuru,
                'jumlahpesertadidik' => $dataJumlahPesertaDidik,
                'countPesertaDidik' => $countPesertaDidik,
                'jumlahrombel' => $dataJumlahRombel,
                'countJumlahRombel' => $countRombel,
            ], 'Data retrieved successfully.'); 
        }

        
        // $fileName = null;
        // if($request->file('fileakreditasi')) {
        //     // $fileName = time().'_'.rand(1,1000).'.'.$request->fileakreditasi->getClientOriginalName();
        //     // $filePath = $request->file('fileakreditasi')->move(public_path('images/fileakreditasi'), $fileName);
        //     $file = $request->file('fileakreditasi');
        //     $fileName = $file->getClientOriginalName();
        //     $akreditasi->fileakreditasi = $fileName;
        // };

        if($request->file('fileakreditasi')){//check if file are exists on request
            $fileakreditasi = $request->file('fileakreditasi');
            $tujuan_upload = public_path('images/fileakreditasi');
            $fileakreditasi->move($tujuan_upload, $fileakreditasi->getClientOriginalName());
        }
        // $akreditasi->fileakreditasi = 'test';

        return view(
            'master.sekolah.edit', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('sekolah.index'), 
                'sekolah' => $sekolah,
                'kota' => $kota,
                'kecamatan' => $kecamatan,
                'tahunajaran' => $tahunajaran,
                'akreditasiBySekolahId' => $akreditasiBySekolahId
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
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $modelSekolah = Sekolah::find($id);

            $modelSekolah->namasekolah = $request->namasekolah;
            $modelSekolah->npsn = $request->npsn;
            $modelSekolah->jenjang = $request->jenjang;
            $modelSekolah->jenis = $request->jenis;
            $modelSekolah->alamat = $request->alamat;
            $modelSekolah->kotaid = $request->kota;
            $modelSekolah->kecamatanid = $request->kecamatan;
            $modelSekolah->lintang = $request->lintang;
            $modelSekolah->bujur = $request->bujur;
            $modelSekolah->kurikulum = $request->kurikulum;
            $modelSekolah->predikat = $request->predikat;

            $modelSekolah->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $modelSekolah->save();

            DB::commit();

            return back()
            ->with('success', 'Data sekolah berhasil ditambah.', ['page' => $this->page]);

        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    // store akreditasi ketika edit data sekolah
    public function storeakreditasi(Request $request)
    {
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();
            $akreditasi = new Akreditasi;
            $akreditasi->akreditasi = $request->akreditasi;
            $akreditasi->sekolahid = $request->sekolahid;
            $akreditasi->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
            $request->validate([
                'fileakreditasi' => 'mimetypes:application/pdf,image/jpeg,image/jpg,image/png',
            ]);
            if ($request->hasFile('fileakreditasi')) {
                if ($request->file('fileakreditasi')->isValid()) {
                    $filePath = $request->file('fileakreditasi')->store('public/uploaded/fileakreditasi');      
                    $akreditasi->fileakreditasi = $filePath;
                }
            }
            
            $akreditasi->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'akreditasi added successfully.',
                'akreditasi' => $akreditasi,
            ], 200);

        } catch (\Throwable $th) {
            return response([
                'success' => false,
                'data'    => 'error',
                'message' => 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: PDF, JPEG, JPG, PNG.',
                'akreditasi' => $akreditasi,
            ], 200);
            // return response(['error' => $th->getMessage()], 500);
        }
    }

    // update akreditasi ketika edit data sekolah
    public function updateakreditasi(Request $request, $id)
    {
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();
            $akreditasi = Akreditasi::find($id);
            $akreditasi->akreditasi = $request->akreditasi;
            $akreditasi->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $request->validate([
                'fileakreditasi' => 'mimetypes:application/pdf,image/jpeg,image/jpg,image/png',
            ]);
            if ($request->hasFile('fileakreditasi')) {
                if ($request->file('fileakreditasi')->isValid()) {
                    $filePath = $request->file('fileakreditasi')->store('public/uploaded/fileakreditasi');

                    if($akreditasi->fileakreditasi != ''  && $akreditasi->fileakreditasi != null){
                        $file_old = storage_path().'/app/'.$akreditasi->fileakreditasi;
                        if (file_exists($file_old)) {
                            unlink($file_old);
                        } else {
                            return response([
                                'success' => false,
                                'data' => 'Error',
                                'message' => 'File tidak ditemukan.',
                                'akreditasi' => $akreditasi,
                            ], 200);
                        }
                    }
                
                $akreditasi->fileakreditasi = $filePath;
                }
            }

            $akreditasi->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'akreditasi updated successfully.',
                'akreditasi' => $akreditasi,
            ], 200);

        } catch (\Throwable $th) {
            return response([
                'success' => false,
                'data'    => 'Error',
                'message' => 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: PDF, JPEG, JPG, PNG.',
                'akreditasi' => $akreditasi,
            ], 200);
            // return response(['error' => $th->getMessage()], 500);
        }
    }

    // hapus akreditasi ketika edit data sekolah
    public function hapusakreditasi(Request $request, $id)
    {
         $this->authorize('edit-4');

         $user = auth('sanctum')->user();

         $akreditasi = Akreditasi::find($id);
 
         $akreditasi->dlt = '1';
 
         $akreditasi->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
 
         $akreditasi->save();
 
         return response([
             'success' => true,
             'data'    => 'Success',
             'message' => 'Akreditasi deleted successfully.',
         ], 200);
    }

    // download file akreditasi
    public function downloadakreditasifile($id)
    {
        $this->authorize('print-4');

        $akreditasi = Akreditasi::find($id);
        $filename = $akreditasi->fileakreditasi;

        $file = storage_path().'/app/'.$filename;

        return response()->download($file);
    }

     // store sertifikat lahan ketika edit data sekolah
     public function storesertifikatlahan(Request $request)
     {
         $this->authorize('edit-4');
 
         $user = auth('sanctum')->user();
 
         try {
             DB::beginTransaction();
             $sertifikatlahan = new SertifikatLahan;
             $sertifikatlahan->sertifikatlahan = $request->sertifikatlahan;
             $sertifikatlahan->sekolahid = $request->sekolahid;
             $sertifikatlahan->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
             $request->validate([
                'filesertifikatlahan' => 'mimetypes:application/pdf,image/jpeg,image/jpg,image/png',
            ]);
            if ($request->hasFile('filesertifikatlahan')) {
                if ($request->file('filesertifikatlahan')->isValid()) {
                    $filePath = $request->file('filesertifikatlahan')->store('public/uploaded/filesertifikatlahan');      
                    $sertifikatlahan->filesertifikatlahan = $filePath;
                }
            }
 
             $sertifikatlahan->save();
             DB::commit();
 
             return response([
                 'success' => true,
                 'data'    => 'Success',
                 'message' => 'sertifikatlahan added successfully.',
                 'sertifikatlahan' => $sertifikatlahan,
             ], 200);
 
         } catch (\Throwable $th) {
            return response([
                'success' => false,
                'data'    => 'Error',
                'message' => 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: PDF, JPEG, JPG, PNG.',
                'sertifikatlahan' => $sertifikatlahan,
            ], 200);
            //  return response(['error' => $th->getMessage()], 500);
         }
     }

     // update sertifikat lahan ketika edit data sekolah
    public function updatesertifikatlahan(Request $request, $id)
    {
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();
            $sertifikatlahan = SertifikatLahan::find($id);
            $sertifikatlahan->sertifikatlahan = $request->sertifikatlahan;
            $sertifikatlahan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $request->validate([
                'filesertifikatlahan' => 'mimetypes:application/pdf,image/jpeg,image/jpg,image/png',
            ]);
            if ($request->hasFile('filesertifikatlahan')) {
                if ($request->file('filesertifikatlahan')->isValid()) {
                    $filePath = $request->file('filesertifikatlahan')->store('public/uploaded/filesertifikatlahan');

                    if($sertifikatlahan->filesertifikatlahan != ''  && $sertifikatlahan->filesertifikatlahan != null){
                        $file_old = storage_path().'/app/'.$sertifikatlahan->filesertifikatlahan;
                        if (file_exists($file_old)) {
                            unlink($file_old);
                        } else {
                            return response([
                                'success' => false,
                                'data' => 'Error',
                                'message' => 'File tidak ditemukan.',
                                'sertifikatlahan' => $sertifikatlahan,
                            ], 200);
                        }
                }
                
                $sertifikatlahan->filesertifikatlahan = $filePath;
                }
            }

            $sertifikatlahan->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'sertifikatlahan updated successfully.',
                'sertifikatlahan' => $sertifikatlahan,
            ], 200);

        } catch (\Throwable $th) {
            return response([
                'success' => false,
                'data'    => 'Error',
                'message' => 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: PDF, JPEG, JPG, PNG.',
                'sertifikatlahan' => $sertifikatlahan,
            ], 200);
            // return response(['error' => $th->getMessage()], 500);
        }
    }

    // hapus sertifikat lahan ketika edit data sekolah
    public function hapussertifikatlahan(Request $request, $id)
    {
         $this->authorize('edit-4');

         $user = auth('sanctum')->user();

         $sertifikatlahan = SertifikatLahan::find($id);
 
         $sertifikatlahan->dlt = '1';
 
         $sertifikatlahan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
 
         $sertifikatlahan->save();
 
         return response([
             'success' => true,
             'data'    => 'Success',
             'message' => 'Sertifikat lahan deleted successfully.',
         ], 200);
    }

    // download file sertifikat lahan 
    public function downloadsertifikatlahanfile($id)
    {
        $this->authorize('print-4');
        $sertifikatlahan = SertifikatLahan::find($id);
        $filename = $sertifikatlahan->filesertifikatlahan;

        $file = storage_path().'/app/'.$filename;

        return response()->download($file);
    }

     // store master plan ketika edit data sekolah
     public function storemasterplan(Request $request)
     {
         $this->authorize('edit-4');
 
         $user = auth('sanctum')->user();
 
         try {
             DB::beginTransaction();
             $masterplan = new MasterPlanSekolah;
             $masterplan->masterplan = $request->masterplan;
             $masterplan->sekolahid = $request->sekolahid;
             $masterplan->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
             $request->validate([
                'filemasterplan' => 'mimetypes:application/pdf,image/jpeg,image/jpg,image/png',
            ]);

            if ($request->hasFile('filemasterplan')) {
                if ($request->file('filemasterplan')->isValid()) {
                $filePath = $request->file('filemasterplan')->store('public/uploaded/filemasterplan');      
                $masterplan->filemasterplan = $filePath;
                }
            }
 
             $masterplan->save();
             DB::commit();
 
             return response([
                 'success' => true,
                 'data'    => 'Success',
                 'message' => 'sertifikatlahan added successfully.',
                 'masterplan' => $masterplan,
             ], 200);
 
         } catch (\Throwable $th) {
            return response([
                'success' => false,
                'data'    => 'Error',
                'message' => 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: PDF, JPEG, JPG, PNG.',
                'masterplan' => $masterplan,
            ], 200);
            // return response(['error' => $th->getMessage()], 500);
         }
     }

     // update master plan ketika edit data sekolah
    public function updatemasterplan(Request $request, $id)
    {
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();
            $masterplan = MasterPlanSekolah::find($id);
            $masterplan->masterplan = $request->masterplan;
            $masterplan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
            $request->validate([
                'filemasterplan' => 'mimetypes:application/pdf,image/jpeg,image/jpg,image/png',
            ]);
            if ($request->hasFile('filemasterplan')) {
                if ($request->file('filemasterplan')->isValid()) {
                    $filePath = $request->file('filemasterplan')->store('public/uploaded/filemasterplan');

                    if($masterplan->filemasterplan != ''  && $masterplan->filemasterplan != null){
                        $file_old = storage_path().'/app/'.$masterplan->filemasterplan;
                        if (file_exists($file_old)) {
                            unlink($file_old);
                        } else {
                            return response([
                                'success' => false,
                                'data' => 'Error',
                                'message' => 'File tidak ditemukan.',
                                'masterplan' => $masterplan,
                            ], 200);
                        }
                    }

                    $masterplan->filemasterplan = $filePath;
                }
            }

            $masterplan->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'master plan sekolah updated successfully.',
                'masterplan' => $masterplan,
            ], 200);

        } catch (\Throwable $th) {
                return response([
                    'success' => false,
                    'data' => 'Success',
                    'message' => 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: PDF, JPEG, JPG, PNG.',
                    'masterplan' => $masterplan,
                ], 200);
                // return response(['error' => $th->getMessage()], 500);
        }
    }

    // hapus master plan ketika edit data sekolah
    public function hapusmasterplan(Request $request, $id)
    {
         $this->authorize('edit-4');

         $user = auth('sanctum')->user();

         $masterplan = MasterPlanSekolah::find($id);
 
         $masterplan->dlt = '1';
 
         $masterplan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
 
         $masterplan->save();
 
         return response([
             'success' => true,
             'data'    => 'Success',
             'message' => 'master plan deleted successfully.',
         ], 200);
    }

    // download file master plan 
    public function downloadmasterplanfile($id)
    {
        $this->authorize('print-4');

        $masterplan = MasterPlanSekolah::find($id);
        $filename = $masterplan->filemasterplan;

        $file = storage_path().'/app/'.$filename;

        return response()->download($file);
    }

    // store jumlah guru ketika edit data sekolah
    public function storejumlahguru(Request $request)
    {
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();
            $jumlahguru = new JumlahGuru;
            $jumlahguru->jumlahguru = $request->jumlahguru;
            $jumlahguru->statuspegawai = $request->statuspegawai;
            $jumlahguru->jeniskelamin = $request->jeniskelamin;
            $jumlahguru->sekolahid = $request->sekolahid;
            $jumlahguru->tahunajaranid = $request->tahunajaranid;

            $jumlahguru->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $jumlahguru->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'sertifikatlahan added successfully.',
                'jumlahguru' => $jumlahguru,
            ], 200);

        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    // update jumlah guru ketika edit data sekolah
    public function updatejumlahguru(Request $request, $id)
    {
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();
            $jumlahguru = JumlahGuru::find($id);
            $jumlahguru->jumlahguru = $request->jumlahguru;
            $jumlahguru->statuspegawai = $request->statuspegawai;
            $jumlahguru->jeniskelamin = $request->jeniskelamin;
            $jumlahguru->tahunajaranid = $request->tahunajaranid;
            $jumlahguru->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $jumlahguru->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'master plan sekolah updated successfully.',
                'jumlahguru' => $jumlahguru,
            ], 200);

        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    // hapus jumlah guru ketika edit data sekolah
    public function hapusjumlahguru(Request $request, $id)
    {
         $this->authorize('edit-4');

         $user = auth('sanctum')->user();

         $jumlahguru = JumlahGuru::find($id);
 
         $jumlahguru->dlt = '1';
 
         $jumlahguru->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
 
         $jumlahguru->save();
 
         return response([
             'success' => true,
             'data'    => 'Success',
             'message' => 'jumlah guru deleted successfully.',
         ], 200);
    }

    // store peserta didik ketika edit data sekolah
    public function storepesertadidik(Request $request)
    {
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();
            $pesertadidik = new PesertaDidik;
            $pesertadidik->kelas = $request->kelas;
            $pesertadidik->jumlahpesertadidik = $request->jumlahpesertadidik;
            $pesertadidik->jeniskelamin = $request->jeniskelamin;
            $pesertadidik->sekolahid = $request->sekolahid;
            $pesertadidik->tahunajaranid = $request->tahunajaranid;

            $pesertadidik->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $pesertadidik->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'peserta didik added successfully.',
                'pesertadidik' => $pesertadidik,
            ], 200);

        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    // update peserta didik ketika edit data sekolah
    public function updatepesertadidik(Request $request, $id)
    {
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();
            $pesertadidik = PesertaDidik::find($id);
            $pesertadidik->kelas = $request->kelas;
            $pesertadidik->jumlahpesertadidik = $request->jumlahpesertadidik;
            $pesertadidik->jeniskelamin = $request->jeniskelamin;
            $pesertadidik->tahunajaranid = $request->tahunajaranid;
            $pesertadidik->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $pesertadidik->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'peserta didik updated successfully.',
                'pesertadidik' => $pesertadidik,
            ], 200);

        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    // hapus peserta didik ketika edit data sekolah
    public function hapuspesertadidik(Request $request, $id)
    {
         $this->authorize('edit-4');

         $user = auth('sanctum')->user();

         $pesertadidik = PesertaDidik::find($id);
 
         $pesertadidik->dlt = '1';
 
         $pesertadidik->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
 
         $pesertadidik->save();
 
         return response([
             'success' => true,
             'data'    => 'Success',
             'message' => 'jumlah guru deleted successfully.',
         ], 200);
    }

    // store jumlah rombel ketika edit data sekolah
    public function storejumlahrombel(Request $request)
    {
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();
            $jumlahrombel = new JumlahRombel();
            $jumlahrombel->kelas = $request->kelas;
            $jumlahrombel->jumlahrombel = $request->jumlahrombel;
            $jumlahrombel->sekolahid = $request->sekolahid;
            $jumlahrombel->tahunajaranid = $request->tahunajaranid;

            $jumlahrombel->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $jumlahrombel->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'peserta didik added successfully.',
                'jumlahrombel' => $jumlahrombel,
            ], 200);

        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    // update peserta didik ketika edit data sekolah
    public function updatejumlahrombel(Request $request, $id)
    {
        $this->authorize('edit-4');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();
            $jumlahrombel = JumlahRombel::find($id);
            $jumlahrombel->kelas = $request->kelas;
            $jumlahrombel->jumlahrombel = $request->jumlahrombel;
            $jumlahrombel->tahunajaranid = $request->tahunajaranid;
            $jumlahrombel->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $jumlahrombel->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'peserta didik updated successfully.',
                'jumlahrombel' => $jumlahrombel,
            ], 200);

        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    // hapus peserta didik ketika edit data sekolah
    public function hapusjumlahrombel(Request $request, $id)
    {
         $this->authorize('edit-4');

         $user = auth('sanctum')->user();

         $jumlahrombel = JumlahRombel::find($id);
 
         $jumlahrombel->dlt = '1';
 
         $jumlahrombel->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
 
         $jumlahrombel->save();
 
         return response([
             'success' => true,
             'data'    => 'Success',
             'message' => 'jumlah rombel deleted successfully.',
         ], 200);
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $this->authorize('delete-4');

        $user = auth('sanctum')->user();

        $sekolah = Sekolah::find($id);
        $akreditasi = Akreditasi::find($id);
        // $akreditasi = Akreditasi::where('sekolahid', $id)
        //     ->where('dlt', 0)->first()
        // ;
        $sertifikatlahan = SertifikatLahan::find($id);
        // $sertifikatlahan = SertifikatLahan::where('sekolahid', $id)
        //     ->where('dlt', 0)->first()
        // ;
        $masterplan = MasterPlanSekolah::find($id);
        // $masterplan = MasterPlanSekolah::where('sekolahid', $id)
        //     ->where('dlt', 0)->first()
        // ;
        $jumlahguru = JumlahGuru::find($id);
        // $jumlahguru = JumlahGuru::where('sekolahid', $id)
        //     ->where('dlt', 0)->first()
        // ;
        $jumlahpesertadidik = PesertaDidik::find($id);
        // $jumlahpesertadidik = PesertaDidik::where('sekolahid', $id)
        //     ->where('dlt', 0)->first()
        // ;
        $jumlahrombel = JumlahRombel::find($id);
        // $jumlahrombel = JumlahRombel::where('sekolahid', $id)
        //     ->where('dlt', 0)->first()
        // ;

        $sekolah->dlt = '1';
        $sekolah->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
        // $sekolah->save();

        if ($sekolah->save()) {
            if($akreditasi != null){
                $akreditasi->dlt = '1';
                // $akreditasi->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                $akreditasi->save();
            }
            if ($sertifikatlahan != null) {
                $sertifikatlahan->dlt = '1';
                // $sertifikatlahan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                $sertifikatlahan->save();
            }
            if ($masterplan != null) {
                $masterplan->dlt = '1';
                // $masterplan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                $masterplan->save();
            }
            if ($jumlahguru != null) {
                $jumlahguru->dlt = '1';
                // $jumlahguru->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                $jumlahguru->save();
            }
            if ($jumlahpesertadidik != null) {
                $jumlahpesertadidik->dlt = '1';
                // $jumlahpesertadidik->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                $jumlahpesertadidik->save();
            }
            if ($jumlahrombel != null) {
                $jumlahrombel->dlt = '1';
                // $jumlahrombel->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
                $jumlahrombel->save();
            }
        }
        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Sekolah deleted successfully.',
        ], 200);

    }
}
