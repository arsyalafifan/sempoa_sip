<?php

namespace App\Http\Controllers\sarpras;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\sarpras\SarprasKebutuhan\CreateRequest;
use App\Http\Requests\sarpras\SarprasKebutuhan\UpdateRequest;
use App\Models\master\Sekolah;
use App\Models\sarpras\FileSarprasKebutuhan;
use App\Models\sarpras\SarprasKebutuhan;
use App\Models\verifikasi\StatusKebutuhanSarpras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SarprasKebutuhanController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Kebutuhan Sarpras';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $oldsekolahid = '')
    {
        $this->authorize('view-13');

        Log::channel('mibedil')->info('Halaman '.$this->page);

        // $sekolahid = null;
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
                        $join->on('tbmsekolah.dlt','=',DB::raw("'0'"));
                    })
                    ->join('tbmnamasarpras', function($join)
                    {
                        $join->on('tbmnamasarpras.namasarprasid', '=', 'tbtranssarpraskebutuhan.namasarprasid');
                        $join->on('tbmnamasarpras.dlt', '=', DB::raw("'0'"));
                    })
                    ->select('tbtranssarpraskebutuhan.*', 'tbmsekolah.*', 'tbmnamasarpras.*')
                    ->where('tbtranssarpraskebutuhan.dlt', 0)
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
            ], 'Sekolah retrieved successfully.');  
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

        $userSekolah = Sekolah::where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid == null ? -1 : auth('sanctum')->user()->sekolahid)->first();

        return view(
            'sarpras.sarpraskebutuhan.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('sarpraskebutuhan.createBySekolahId', ['sekolahid' => ':sekolahid']), 
                'kota' => $kota,
                'sekolah' => $sekolah,
                'kecamatan' => $kecamatan,
                'isSekolah' => Auth::user()->isSekolah(),
                'userSekolah' => $userSekolah,
                // 'oldsekolahid' => old($request->sekolahid)
                'oldsekolahid' => $oldsekolahid
            ]
        );
    }

    public function getSekolah($kotaid)
    {
        try {
            $sekolah = DB::table('tbmsekolah')
                    ->select('tbmsekolah.sekolahid', 'tbmsekolah.namasekolah')
                    ->where('tbmsekolah.dlt', 0)
                    ->where('tbmsekolah.kotaid', $kotaid)
                    ->orderBy('tbmsekolah.namasekolah')
                    ->get()
                ;
        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return $this->sendResponse($sekolah, 'Sekolah retrieved successfully.');
    }

    public function getNamaSarpras($kategorisarpras)
    {
        try {
            $namasarpras = DB::table('tbmnamasarpras')
                    ->select('tbmnamasarpras.namasarprasid', 'tbmnamasarpras.namasarpras', 'tbmnamasarpras.kategorisarpras')
                    ->where('tbmnamasarpras.dlt', 0)
                    ->where('tbmnamasarpras.kategorisarpras', $kategorisarpras)
                    ->orderBy('tbmnamasarpras.namasarpras')
                    ->get()
                ;
        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return $this->sendResponse($namasarpras, 'Nama sarpras retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {

        $this->authorize('add-13');
        if(Auth::user()->isSekolah() && $id != auth('sanctum')->user()->sekolahid && (!is_null($id))) return view('errors.403');


        if (is_null($id)) {
            return redirect()->route('sarpraskebutuhan.index'); 
        }
        Log::channel('mibedil')->info('Halaman Tambah '.$this->page, ['id' => $id]);

        $sekolah = Sekolah::where('sekolahid', $id)->firstOrFail();
        $nopengajuan = $this->getnextno();

        $namasarpras = DB::table('tbmnamasarpras')
         ->select('tbmnamasarpras.namasarprasid', 'tbmnamasarpras.namasarpras')
         ->where('tbmnamasarpras.dlt', 0)
         ->orderBy('tbmnamasarpras.namasarpras')
         ->get();

         $pegawai = DB::table('tbmpegawai')
         ->select('tbmpegawai.pegawaiid', 'tbmpegawai.sekolahid', 'tbmpegawai.nama', 'tbmpegawai.nip')
         ->where('tbmpegawai.sekolahid', $id)
         ->where('tbmpegawai.jabatan', 1)
         ->where('tbmpegawai.status', 1)
         ->where('tbmpegawai.dlt', 0)
         ->orderBy('tbmpegawai.nip')
         ->get();

        return view(
            'sarpras.sarpraskebutuhan.create', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('sarpraskebutuhan.index'), 
                'sekolah' => $sekolah,
                'namasarpras' => $namasarpras,
                'nopengajuan' => $nopengajuan,
                'pegawai' => $pegawai
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
        $this->authorize('add-13');

        $user = auth('sanctum')->user();

        try{
            // $user = auth('sanctum')->user();
            $model = new SarprasKebutuhan;

            DB::beginTransaction();

            $model->nopengajuan = $request->nopengajuan;
            $model->tglpengajuan = $request->tglpengajuan;
            $model->jeniskebutuhan = $request->jeniskebutuhan;
            $model->pegawaiid = $request->pegawaiid;
            $model->jenissarpras = $request->jenissarpras;
            $model->namasarprasid = $request->namasarprasid;
            $model->jumlah = $request->jumlah;
            $model->satuan = $request->satuan;
            $model->thang = $request->thang;
            $model->analisakebsarpras = $request->analisakebsarpras;
            $model->sekolahid = $request->sekolahid;
            $model->status = enum::STATUS_KEBUTUHAN_SARPRAS_DRAFT;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

           //  dd($user->login);
            if ($model->save()) {
                // Save multiple akreditasi data and image
                $files = [];
                $tglnow = date('Y-m-d');

                foreach ($request->file('filesarpraskebutuhan') as $key => $value) {
                   //  $fileName = time().'_'.rand(1,1000).'.'.$request->file('filesarpraskebutuhan')[$key]->extension();
                    // $filePath = $request->file('filesarpraskebutuhan')[$key]->store('public/uploaded/sarpraskebutuhan');
                    // $files = $filePath;

                    $fileName = $tglnow.'_'.rand(1,1000).'_'.$request->file('filesarpraskebutuhan')[$key]->getClientOriginalName();   
                    $filePath = $request->file('filesarpraskebutuhan')[$key]->storeAs('public/uploaded/sarpraskebutuhan', $fileName);  
                    $files = $fileName; 
                    
                    $modelFileSarprasKebutuhan = new FileSarprasKebutuhan;
                    $modelFileSarprasKebutuhan->filesarpraskebutuhan = $files;

                    // $modelFileSarprasKebutuhan->filesarpraskebutuhan = $files;
                    $modelFileSarprasKebutuhan->sarpraskebutuhanid = $model->sarpraskebutuhanid;
                    $modelFileSarprasKebutuhan->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $modelFileSarprasKebutuhan->save();
                }

                $statuskebutuhansarpras = new StatusKebutuhanSarpras;
                $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                $statuskebutuhansarpras->sarpraskebutuhanid = $model->sarpraskebutuhanid;
                $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_DRAFT;
                $statuskebutuhansarpras->tgl = date('Y-m-d');

                $statuskebutuhansarpras->save();
            }

            // dd($request->all());

            DB::commit();

            return redirect()
               ->route('sarpraskebutuhan.index')
               ->with(
                'success', 
                'Data sarpras kebutuhan berhasil ditambah.', 
                    [
                        'page' => $this->page, 
                    ]
                )
                ->with(
                    'oldsekolahid', $request->sekolahid
                );
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
    public function show(Request $request, $id)
    {
        $this->authorize('view-13');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $sarpraskebutuhan = SarprasKebutuhan::where('sarpraskebutuhanid', $id)->firstOrFail();

        $namasarpras = DB::table('tbmnamasarpras')
         ->select('tbmnamasarpras.namasarprasid', 'tbmnamasarpras.namasarpras')
         ->where('tbmnamasarpras.dlt', 0)
         ->orderBy('tbmnamasarpras.namasarpras')
         ->get();

         $pegawai = DB::table('tbmpegawai')
         ->select('tbmpegawai.pegawaiid', 'tbmpegawai.sekolahid', 'tbmpegawai.nama', 'tbmpegawai.nip')
         ->where('tbmpegawai.sekolahid', $sarpraskebutuhan->sekolahid)
         ->where('tbmpegawai.jabatan', 1)
         ->where('tbmpegawai.status', 1)
         ->where('tbmpegawai.dlt', 0)
         ->orderBy('tbmpegawai.nip')
         ->get();

        if ($request->ajax()) {
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            // data table kondisi sarpras
            try {
                $filesarpraskebutuhan = FileSarprasKebutuhan::where('sarpraskebutuhanid', $id)
                    ->where('dlt', '0')
                    ->orderBy('filesarpraskebutuhanid')
                ;
                $countSarprasKebutuhan = $filesarpraskebutuhan->count();
                $filesarpraskebutuhan = $filesarpraskebutuhan->skip($page)->take($perpage)->get();
            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'filesarpraskebutuhan' => $filesarpraskebutuhan,
                'countSarprasKebutuhan' => $countSarprasKebutuhan,
            ], 'Data retrieved successfully.'); 
        }

        return view(
            'sarpras.sarpraskebutuhan.show', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('sarpraskebutuhan.index'), 
                'sarpraskebutuhan' => $sarpraskebutuhan,
                'pegawai' => $pegawai,
                'namasarpras' => $namasarpras
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
         $this->authorize('edit-13');

         Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

         $sarpraskebutuhan = SarprasKebutuhan::where('sarpraskebutuhanid', $id)->firstOrFail();

         $namasarpras = DB::table('tbmnamasarpras')
         ->select('tbmnamasarpras.namasarprasid', 'tbmnamasarpras.namasarpras')
         ->where('tbmnamasarpras.dlt', 0)
         ->orderBy('tbmnamasarpras.namasarpras')
         ->get();

         $pegawai = DB::table('tbmpegawai')
         ->select('tbmpegawai.pegawaiid', 'tbmpegawai.sekolahid', 'tbmpegawai.nama', 'tbmpegawai.nip')
         ->where('tbmpegawai.sekolahid', $sarpraskebutuhan->sekolahid)
         ->where('tbmpegawai.jabatan', 1)
         ->where('tbmpegawai.status', 1)
         ->where('tbmpegawai.dlt', 0)
         ->orderBy('tbmpegawai.nip')
         ->get();

         if ($request->ajax()) {
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            // data table kondisi sarpras
            try {
                $filesarpraskebutuhan = FileSarprasKebutuhan::where('sarpraskebutuhanid', $id)
                    ->where('dlt', '0')
                    ->orderBy('filesarpraskebutuhanid')
                ;
                $countSarprasKebutuhan = $filesarpraskebutuhan->count();
                $filesarpraskebutuhan = $filesarpraskebutuhan->skip($page)->take($perpage)->get();
            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'filesarpraskebutuhan' => $filesarpraskebutuhan,
                'countSarprasKebutuhan' => $countSarprasKebutuhan,
            ], 'Data retrieved successfully.'); 
         }

         return view(
            'sarpras.sarpraskebutuhan.edit', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('sarpraskebutuhan.index'), 
                'sarpraskebutuhan' => $sarpraskebutuhan,
                'namasarpras' => $namasarpras,
                'pegawai' => $pegawai
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
    public function update(UpdateRequest $request, $id)
    {
        $this->authorize('edit-13');

        $user = auth('sanctum')->user();

        try {

            DB::beginTransaction();

            $model = SarprasKebutuhan::find($id);

            $model->nopengajuan = $request->nopengajuan;
            $model->tglpengajuan = $request->tglpengajuan;
            $model->jeniskebutuhan = $request->jeniskebutuhan;
            $model->pegawaiid = $request->pegawaiid;
            $model->jenissarpras = $request->jenissarpras;
            $model->namasarprasid = $request->namasarprasid;
            $model->jumlah = $request->jumlah;
            $model->satuan = $request->satuan;
            $model->analisakebsarpras = $request->analisakebsarpras;
            $model->thang = $request->thang;

            $model->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $model->save();
            DB::commit();

        }catch(QueryException $e){
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()
                ->route('sarpraskebutuhan.index')
                ->with(
                    'success', 
                    'Data sarpras kebutuhan berhasil diubah.', 
                    [
                        'page' => $this->page,
                    ]
                )
                ->with(
                    'oldsekolahid', $model->sekolahid
                )
        ;
    }

    public function storedetailsarpraskebutuhan(Request $request)
    {
        $this->authorize('edit-13');

        $user = auth('sanctum')->user();
        $tglnow = date("Y-m-d");

        try {
            DB::beginTransaction();
            $detailSarprasKebutuhan = new FileSarprasKebutuhan;

            $detailSarprasKebutuhan->sarpraskebutuhanid = $request->sarpraskebutuhanid;
            $detailSarprasKebutuhan->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            if ($request->hasFile('filesarpraskebutuhan')) {
                $fileName = $tglnow.'_'.rand(1,1000).'_'.$request->file('filesarpraskebutuhan')->getClientOriginalName();   
                $filePath = $request->file('filesarpraskebutuhan')->storeAs('public/uploaded/sarpraskebutuhan', $fileName);   
                $detailSarprasKebutuhan->filesarpraskebutuhan = $fileName;
            }

            $validation = Validator::make($request->all(), [
                'filesarpraskebutuhan' => 'mimes:pdf,jpg,jpeg,png|max:2048'
            ],
            [
                'filesarpraskebutuhan.mimes' => 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: JPEG, JPG, PNG',
                'filesarpraskebutuhan.max' => 'Ukuran file maksimal 2MB'
            ]);

            if($validation->fails())
            {
                return response([
                    'errors' => $validation->errors()
                ]);
            };

            $detailSarprasKebutuhan->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail sarpras tersedia added successfully.',
                'detailSarprasTersedia' => $detailSarprasKebutuhan,
            ], 200);

        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    public function updatedetailsarpraskebutuhan(Request $request, $id)
    {
        $this->authorize('edit-13');

        $user = auth('sanctum')->user();
        $tglnow = date("Y-m-d");

        try {
            DB::beginTransaction();
            $detailSarprasKebutuhan = FileSarprasKebutuhan::find($id);

            $detailSarprasKebutuhan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            if ($request->hasFile('filesarpraskebutuhan')) {

                $fileName = $tglnow.'_'.rand(1,1000).'_'.$request->file('filesarpraskebutuhan')->getClientOriginalName();   
                $filePath = $request->file('filesarpraskebutuhan')->storeAs('public/uploaded/sarpraskebutuhan', $fileName);   
                if($detailSarprasKebutuhan->filesarpraskebutuhan != ''  && $detailSarprasKebutuhan->filesarpraskebutuhan != null){
                    $file_old = public_path().'/storage/uploaded/sarpraskebutuhan/'.$detailSarprasKebutuhan->filesarpraskebutuhan;
                    unlink($file_old);
                }
                $detailSarprasKebutuhan->filesarpraskebutuhan = $fileName;
            }

            $detailSarprasKebutuhan->save();
            DB::commit();

            $validation = Validator::make($request->all(), [
                'filesarpraskebutuhan' => 'mimes:pdf,jpg,jpeg,png|max:2048'
            ],
            [
                'filesarpraskebutuhan.mimes' => 'Ekstensi file tidak sesuai, silakan upload file dengan ekstensi: JPEG, JPG, PNG',
                'filesarpraskebutuhan.max' => 'Ukuran file maksimal 2MB'
            ]);

            if($validation->fails())
            {
                return response([
                    'errors' => $validation->errors()
                ]);
            }
            else
            {
                return response([
                    'success' => true,
                    'data'    => 'Success',
                    'message' => 'detail sarpras tersedia updated successfully.',
                    'detailSarprasTersedia' => $detailSarprasKebutuhan,
                ], 200);
            };

        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    public function hapusdetailsarpraskebutuhan(Request $request, $id)
    {
        $this->authorize('edit-13');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $detailSarprasKebutuhan = FileSarprasKebutuhan::find($id);

            $detailSarprasKebutuhan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $detailSarprasKebutuhan->dlt = '1';

            if($detailSarprasKebutuhan->filesarpraskebutuhan != ''  && $detailSarprasKebutuhan->filesarpraskebutuhan != null){
                $file_old = public_path().'/storage/uploaded/sarpraskebutuhan/'.$detailSarprasKebutuhan->filesarpraskebutuhan;
                unlink($file_old);
            }
            $detailSarprasKebutuhan->filesarpraskebutuhan = null;

            $detailSarprasKebutuhan->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'Akreditasi deleted successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    public function downloadfilesarpraskebutuhan($id)
    {
         $this->authorize('print-13');

         $detailSarprasKebutuhan = FileSarprasKebutuhan::find($id);
         $filename = $detailSarprasKebutuhan->filesarpraskebutuhan;
 
         $file = public_path().'/storage/uploaded/sarpraskebutuhan/'.$filename;
 
         return response()->download($file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-13');

        $user = auth('sanctum')->user();

         try {
            DB::beginTransaction();
            
            $sarpraskebutuhan = SarprasKebutuhan::find($id);

            $sarpraskebutuhan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $sarpraskebutuhan->dlt = '1';

            $sarpraskebutuhan->save();

            // if($sarpraskebutuhan->save())
            // {
            //     $detailSarprasKebutuhan = FileSarprasKebutuhan::where('sarpraskebutuhanid', $id);
            //     foreach ($detailSarprasKebutuhan as $detail) {
            //         $detail->dlt = '1';
            //         $detail->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
            //         $detail->save();
            //     }
            // }

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'Data Sarpras Kebutuhan Berhasil Dihapus.',
            ], 200);

         } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    public function getnextno()
    {
        $data = DB::table('tbtranssarpraskebutuhan')
        ->select(DB::raw('max(cast(replace(tbtranssarpraskebutuhan.nopengajuan, \'.\', \'\') as int)) + 1 as nopengajuan'))
        ->where('tbtranssarpraskebutuhan.dlt', '0')
        // ->where('tbmkota.provid', $parentid)
        ->get();
        
        $nopengajuan = 1;
        if ($data[0]->nopengajuan != null) $nopengajuan = $data[0]->nopengajuan;

        $nextno = '1';
        if (isset($nopengajuan)) {
            $nextno = $nopengajuan;
        }
        $nextno = str_pad($nextno, 4, '0', STR_PAD_LEFT);
        $nextno = $nextno . '.';

        return $nextno;
    }

    public function showDetailKebutuhanSarpras(Request $request, $id)
    {
        $this->authorize('view-13');

        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        // if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $detailsarpraskebutuhan = DB::table('tbtranssarpraskebutuhan')
                    ->join('tbmpegawai', function($join)
                    {
                        $join->on('tbmpegawai.pegawaiid', '=', 'tbtranssarpraskebutuhan.pegawaiid');
                        $join->on('tbmpegawai.dlt','=',DB::raw("'0'"));
                    })
                    ->join('tbmnamasarpras', function($join)
                    {
                        $join->on('tbmnamasarpras.namasarprasid', '=', 'tbtranssarpraskebutuhan.namasarprasid');
                        $join->on('tbmnamasarpras.dlt','=',DB::raw("'0'"));
                    })
                    ->join('tbtransfilesarpraskebutuhan', function($join)
                    {
                        $join->on('tbtransfilesarpraskebutuhan.sarpraskebutuhanid', '=', 'tbtranssarpraskebutuhan.sarpraskebutuhanid');
                        $join->on('tbtransfilesarpraskebutuhan.dlt','=',DB::raw("'0'"));
                    })
                    ->select('tbtranssarpraskebutuhan.*', 'tbtransfilesarpraskebutuhan.*')
                    ->where('tbtranssarpraskebutuhan.sarpraskebutuhanid', $id)
                    ->where('tbtranssarpraskebutuhan.dlt', 0)
                    ->orderBy('tbtranssarpraskebutuhan.sarpraskebutuhanid')
                ;

                // $count = $detailsarpraskebutuhan->count();
                $data = $detailsarpraskebutuhan->get();

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

        // }
    }

    public function pengajuan(Request $request, $id)
    {
        $this->authorize('edit-13');

        $user = auth('sanctum')->user();

         try {
            DB::beginTransaction();
            
            $sarpraskebutuhan = SarprasKebutuhan::find($id);

            $sarpraskebutuhan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $sarpraskebutuhan->status = enum::STATUS_KEBUTUHAN_SARPRAS_PENGAJUAN;

            if($sarpraskebutuhan->save())
            {
                $statuskebutuhansarpras = new StatusKebutuhanSarpras;
                $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                $statuskebutuhansarpras->sarpraskebutuhanid = $sarpraskebutuhan->sarpraskebutuhanid;
                $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_PENGAJUAN;
                $statuskebutuhansarpras->tgl = date('Y-m-d');

                $statuskebutuhansarpras->save();
            };

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'Status data Sarpras Kebutuhan berhasil diubah ke pengajuan.',
            ], 200);

         } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }
}
