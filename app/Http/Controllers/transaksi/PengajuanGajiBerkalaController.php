<?php

namespace App\Http\Controllers\transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\master\Sekolah;
use App\Models\transaksi\HistoryPengajuanGaji;
use App\Models\transaksi\PegawaiPengajuanGaji;
use App\Models\transaksi\PengajuanGajiBerkala;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengajuanGajiBerkalaController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Pengajuan Gaji Berkala';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-31');

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
            $unit = $request->unit;
            $status = $request->filter_status;
            $jenispeg = $request->filter_jenispeg;

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);
            try {

            // Menghitung total data tanpa menggunakan distinct dan pagination
            // $countQuery = DB::table('tbpengajuangajiberkala as tbpengajuangajiberkala')
            // ->leftJoin('tbpegawaipengajuangaji as tbpegawaipengajuangaji', function($join) {
            //     $join->on('tbpegawaipengajuangaji.pengajuangajiberkalaid', '=', 'tbpengajuangajiberkala.pengajuangajiberkalaid');
            // })
            // ->join('tbmpegawai as tbmpegawai', function($join) {
            //     $join->on('tbmpegawai.pegawaiid', '=', 'tbpegawaipengajuangaji.pegawaiid')
            //         ->on('tbmpegawai.dlt', '=', DB::raw("'0'"));
            // })
            // ->leftJoin('tbmsekolah as tbmsekolah', function($join) {
            //     $join->on('tbmsekolah.sekolahid', '=', 'tbmpegawai.sekolahid')
            //         ->on('tbmsekolah.dlt', '=', DB::raw("'0'"));
            // })
            // ->where('tbpengajuangajiberkala.dlt', DB::raw("'0'"))
            // ->where(function($query) {
            //     if (Auth::user()->isSekolah()) {
            //         $query->where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid);
            //     }
            // })
            // ->where(function($query) use ($kotaid, $jenis, $jenjang, $kecamatanid, $sekolahid, $unit, $status, $jenispeg, $search) {
            //     if (!is_null($kotaid) && $kotaid != '') $query->where('tbmsekolah.kotaid', $kotaid);
            //     if (!is_null($jenis) && $jenis != '') $query->where('tbmsekolah.jenis', $jenis);
            //     if (!is_null($jenjang) && $jenjang != '') $query->where('tbmsekolah.jenjang', $jenjang);
            //     if (!is_null($kecamatanid) && $kecamatanid != '') $query->where('tbmsekolah.kecamatanid', $kecamatanid);
            //     if (!is_null($sekolahid) && $sekolahid != '') $query->where('tbmsekolah.sekolahid', $sekolahid);
            //     if (!is_null($unit) && $unit != '') $query->where('tbmpegawai.unit', $unit);
            //     if (!is_null($status) && $status != '') $query->where('tbpengajuangajiberkala.status', $status);
            //     if (!is_null($jenispeg) && $jenispeg != '') $query->where('tbmpegawai.jenispeg', $jenispeg);
            //     if (!is_null($search) && $search != '') {
            //         $query->where(function($subQuery) use ($search) {
            //             $subQuery->where('tbmsekolah.namasekolah', 'ilike', '%'.$search.'%')
            //                     ->orWhere('tbmpegawai.nama', 'ilike', '%'.$search.'%')
            //                     ->orWhere('tbmpegawai.nip', 'ilike', '%'.$search.'%');
            //         });
            //     }
            // });

            // // Ambil total count
            // $count = $countQuery->count();

            // // Query utama dengan distinct dan pagination
            // $pengajuangajiberkala = $countQuery->select(
            //     'tbpengajuangajiberkala.*', 
            //     'tbpegawaipengajuangaji.pegawaipengajuangajiid', 
            //     'tbpegawaipengajuangaji.ketpegawai', 
            //     'tbpegawaipengajuangaji.ketdinas', 
            //     'tbpegawaipengajuangaji.file', 
            //     'tbmsekolah.sekolahid',
            //     'tbmsekolah.namasekolah',
            //     'tbmpegawai.pegawaiid',
            //     'tbmpegawai.nip',
            //     'tbmpegawai.nama',
            //     'tbmpegawai.jenispeg'
            // )
            // ->distinct()
            // ->skip($page)
            // ->take($perpage)
            // // ->orderBy('tbpengajuangajiberkala.tgladd', 'desc')
            // ->orderBy('tbpegawaipengajuangaji.ketpegawai', 'asc')
            // ->get();


            $subQuery = DB::table('tbpengajuangajiberkala as sub_tbpengajuangajiberkala')
                ->select('sub_tbpegawaipengajuangaji.pegawaiid', DB::raw('MAX(sub_tbpengajuangajiberkala.pengajuangajiberkalaid) as max_pengajuangajiberkalaid'))
                ->join('tbpegawaipengajuangaji as sub_tbpegawaipengajuangaji', 'sub_tbpegawaipengajuangaji.pengajuangajiberkalaid', '=', 'sub_tbpengajuangajiberkala.pengajuangajiberkalaid')
                ->groupBy('sub_tbpegawaipengajuangaji.pegawaiid');

            $countQuery = DB::table('tbpengajuangajiberkala as tbpengajuangajiberkala')
                ->leftJoin('tbpegawaipengajuangaji as tbpegawaipengajuangaji', function($join) {
                    $join->on('tbpegawaipengajuangaji.pengajuangajiberkalaid', '=', 'tbpengajuangajiberkala.pengajuangajiberkalaid');
                })
                ->join('tbmpegawai as tbmpegawai', function($join) {
                    $join->on('tbmpegawai.pegawaiid', '=', 'tbpegawaipengajuangaji.pegawaiid')
                        ->on('tbmpegawai.dlt', '=', DB::raw("'0'"));
                })
                ->leftJoin('tbmsekolah as tbmsekolah', function($join) {
                    $join->on('tbmsekolah.sekolahid', '=', 'tbmpegawai.sekolahid')
                        ->on('tbmsekolah.dlt', '=', DB::raw("'0'"));
                })
                ->joinSub($subQuery, 'sub', function($join) {
                    $join->on('tbpengajuangajiberkala.pengajuangajiberkalaid', '=', 'sub.max_pengajuangajiberkalaid');
                })
                ->where('tbpengajuangajiberkala.dlt', DB::raw("'0'"))
                ->where(function($query) {
                    if (Auth::user()->isSekolah()) {
                        $query->where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid);
                    }
                })
                ->where(function($query) use ($kotaid, $jenis, $jenjang, $kecamatanid, $sekolahid, $unit, $status, $jenispeg, $search) {
                    if (!is_null($kotaid) && $kotaid != '') $query->where('tbmsekolah.kotaid', $kotaid);
                    if (!is_null($jenis) && $jenis != '') $query->where('tbmsekolah.jenis', $jenis);
                    if (!is_null($jenjang) && $jenjang != '') $query->where('tbmsekolah.jenjang', $jenjang);
                    if (!is_null($kecamatanid) && $kecamatanid != '') $query->where('tbmsekolah.kecamatanid', $kecamatanid);
                    if (!is_null($sekolahid) && $sekolahid != '') $query->where('tbmsekolah.sekolahid', $sekolahid);
                    if (!is_null($unit) && $unit != '') $query->where('tbmpegawai.unit', $unit);
                    if (!is_null($status) && $status != '') $query->where('tbpengajuangajiberkala.status', $status);
                    if (!is_null($jenispeg) && $jenispeg != '') $query->where('tbmpegawai.jenispeg', $jenispeg);
                    if (!is_null($search) && $search != '') {
                        $query->where(function($subQuery) use ($search) {
                            $subQuery->where('tbmsekolah.namasekolah', 'ilike', '%'.$search.'%')
                                    ->orWhere('tbmpegawai.nama', 'ilike', '%'.$search.'%')
                                    ->orWhere('tbmpegawai.nip', 'ilike', '%'.$search.'%');
                        });
                    }
                });

            // Ambil total count dari subquery yang menggunakan distinct
            $count = DB::table(DB::raw("({$countQuery->toSql()}) as sub"))
                ->mergeBindings($countQuery)
                ->distinct()
                ->count('sub.max_pengajuangajiberkalaid');

            // Query utama dengan distinct dan pagination
            $pengajuangajiberkala = $countQuery->select(
                'tbpengajuangajiberkala.*', 
                'tbpegawaipengajuangaji.pegawaipengajuangajiid', 
                'tbpegawaipengajuangaji.ketpegawai', 
                'tbpegawaipengajuangaji.ketdinas', 
                'tbpegawaipengajuangaji.file', 
                'tbmsekolah.sekolahid',
                'tbmsekolah.namasekolah',
                'tbmpegawai.pegawaiid',
                'tbmpegawai.nip',
                'tbmpegawai.nama',
                'tbmpegawai.jenispeg',
                DB::raw("CASE WHEN tbpegawaipengajuangaji.ketpegawai IS NOT NULL AND tbpegawaipengajuangaji.ketpegawai != '' THEN 0 ELSE 1 END as ketpegawai_order") // Menambahkan ekspresi dalam SELECT
            )
                ->distinct()
                ->orderBy('ketpegawai_order')
                ->orderBy('tbpengajuangajiberkala.pengajuangajiberkalaid', 'desc')
                ->skip($page)
                ->take($perpage)
                ->get();

            return $this->sendResponse([
                'data' => $pengajuangajiberkala,
                'count' => $count,
            ], 'Data pengajuan gaji berkala retrieved successfully.');  

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

        $jabatan = DB::table('tbmjabatan')
            ->select('tbmjabatan.jabatanid', 'tbmjabatan.namajabatan')
            ->where('tbmjabatan.dlt', 0)
            ->orderBy('tbmjabatan.jabatanid')
            ->get()
        ;

        $userSekolah = Sekolah::where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid == null ? -1 : auth('sanctum')->user()->sekolahid)->first();

        return view(
            'transaksi.pengajuangajiberkala.index', 
            [
                'page' => $this->page, 
                // 'createbutton' => true, 
                // 'createurl' => route('sarpraskebutuhan.createBySekolahId', ['sekolahid' => ':sekolahid']), 
                'kota' => $kota,
                'sekolah' => $sekolah,
                'kecamatan' => $kecamatan,
                'jabatan' => $jabatan,
                'isSekolah' => Auth::user()->isSekolah(),
                'userSekolah' => $userSekolah
            ]
        );
    }

    public function loadPegawai(Request $request)
    {

        $this->authorize('view-31');

        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {
            
            $sekolahid = Auth::user()->isSekolah() ? auth('sanctum')->user()->sekolahid : $request->input('sekolahid');
            $unit = $request->input('unit');
            // dd($request->input('sekolahid'));
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $pegawai = DB::table('tbmpegawai')
                    ->leftJoin('tbmsekolah', function($join)
                    {
                        $join->on('tbmsekolah.sekolahid', '=', 'tbmpegawai.sekolahid');
                        $join->on('tbmsekolah.dlt', '=', DB::raw("'0'"));
                    })
                    ->leftJoin('tbpegawaipengajuangaji', function($join)
                    {
                        $join->on('tbpegawaipengajuangaji.pegawaiid', '=', 'tbmpegawai.pegawaiid');
                        $join->on('tbpegawaipengajuangaji.dlt','=', DB::raw("'0'"));
                    })
                    ->leftJoin('tbpengajuangajiberkala', function($join)
                    {
                        $join->on('tbpengajuangajiberkala.pengajuangajiberkalaid', '=', 'tbpegawaipengajuangaji.pengajuangajiberkalaid');
                        $join->on('tbpengajuangajiberkala.dlt','=', DB::raw("'0'"));
                    })
                    ->where(function($query){
                        if(Auth::user()->isSekolah()) $query->where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid);
                    })
                    ->where(function($query) use ($sekolahid)
                    {
                        if (!is_null($sekolahid) && $sekolahid!='') $query->where('tbmsekolah.sekolahid', $sekolahid);
                        // dd($sekolahid);
                    })
                    ->where(function($query) use ($unit)
                    {
                        if (!is_null($unit) && $unit!='') $query->where('tbmpegawai.unit', $unit);
                    })
                    ->select(
                        'tbmpegawai.pegawaiid',
                        'tbmpegawai.nip', 
                        'tbmpegawai.nama',
                        'tbmpegawai.jenispeg',
                        'tbmsekolah.sekolahid',
                        'tbmsekolah.namasekolah',
                        // 'tbpegawaipengajuangaji.pegawaipengajuangajiid',
                        // 'tbpengajuangajiberkala.status'
                        DB::raw('MAX(tbpegawaipengajuangaji.pegawaipengajuangajiid) as pegawaipengajuangajiid'),
                        DB::raw('MAX(tbpengajuangajiberkala.status) as status')
                    )
                    ->distinct()
                    // ->where('tbmpegawai.unit', 2)
                    ->where('tbmpegawai.dlt', DB::raw("'0'"))
                    ->groupBy('tbmpegawai.pegawaiid', 'tbmsekolah.sekolahid')
                    ->orderBy('tbmpegawai.pegawaiid')
                ;

                $count = $pegawai->count();
                // $data = $pegawai->skip($page)->take($perpage)->get();
                $data = $pegawai->get();

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

    public function loadPegawaiEdit(Request $request, $pengajuangajiberkalaid)
    {

        $this->authorize('view-31');

        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $pegawai = DB::table('tbpegawaipengajuangaji')
                    ->leftJoin('tbpengajuangajiberkala', function($join)
                    {
                        $join->on('tbpengajuangajiberkala.pengajuangajiberkalaid', '=', 'tbpegawaipengajuangaji.pengajuangajiberkalaid');
                        $join->on('tbpengajuangajiberkala.dlt','=', DB::raw("'0'"));
                    })
                    ->leftJoin('tbmpegawai', function($join)
                    {
                        $join->on('tbmpegawai.pegawaiid', '=', 'tbpegawaipengajuangaji.pegawaiid');
                        $join->on('tbmpegawai.dlt','=', DB::raw("'0'"));
                    })
                    ->leftJoin('tbmsekolah', function($join)
                    {
                        $join->on('tbmsekolah.sekolahid', '=', 'tbmpegawai.sekolahid');
                        $join->on('tbmsekolah.dlt', '=', DB::raw("'0'"));
                    })
                    ->select(
                        'tbmpegawai.pegawaiid',
                        'tbmpegawai.nip', 
                        'tbmpegawai.nama',
                        'tbmpegawai.jenispeg',
                        'tbmsekolah.sekolahid',
                        'tbmsekolah.namasekolah',
                        'tbpengajuangajiberkala.status',
                        'tbpegawaipengajuangaji.pegawaipengajuangajiid',
                        'tbpegawaipengajuangaji.file',
                    )
                    ->where('tbpegawaipengajuangaji.pengajuangajiberkalaid', $pengajuangajiberkalaid)
                    // ->where('tbmpegawai.unit', DB::raw("'2'"))
                    ->where('tbmpegawai.dlt', DB::raw("'0'"))
                    ->orderBy('tbmpegawai.pegawaiid')
                ;

                $count = $pegawai->count();
                // $data = $pegawai->skip($page)->take($perpage)->get();
                $data = $pegawai->get();

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

    public function storePengajuan(Request $request)
    {
        $this->authorize('add-31');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');

        $formData = $request->except('checkedData');
        $checkedData = json_decode($request->input('checkedData'), true);
        // dd($checkedData);

        try {
            DB::beginTransaction();

            $pengajuanModel = new PengajuanGajiBerkala;


            $pengajuanModel->tglverifikasi = $request->tglverifikasi;
            $pengajuanModel->status = $request->status;
            $pengajuanModel->keterangan = $request->keterangan;

            $pengajuanModel->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $pengajuanModel->save();

            foreach ($checkedData as $pegawai) {
                $pengajuanPegawaiModel = new PegawaiPengajuanGaji;

                $pengajuanPegawaiModel->pengajuangajiberkalaid = $pengajuanModel->pengajuangajiberkalaid;
                $pengajuanPegawaiModel->pegawaiid = $pegawai['pegawaiid'];
                $pengajuanPegawaiModel->ketdinas = $request->keterangan;

                $pengajuanPegawaiModel->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                $pengajuanPegawaiModel->save();

                $historyPengajuanModel = new HistoryPengajuanGaji;
                $historyPengajuanModel->pegawaipengajuangajiid = $pengajuanPegawaiModel->pegawaipengajuangajiid;
                $historyPengajuanModel->tglverifikasi = $pengajuanModel->tglverifikasi;
                $historyPengajuanModel->keterangan = $request->keterangan;
                $historyPengajuanModel->status = $pengajuanModel->status;

                $historyPengajuanModel->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                $historyPengajuanModel->save();

            }

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'pengajuan added successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        // Lakukan operasi penyimpanan data formulir
        // Misalnya:
        // $pengajuan = Tbpengajuangajiberkala::create([
        //     'tglpengajuan' => $formData['tglpengajuan'],
        //     'status' => $formData['status'],
        //     'keterangan' => $formData['keterangan'],
        // ]);

        // Lakukan operasi penyimpanan data pegawai yang terceklis
        // Misalnya:

        // return response()->json(['message' => 'Data berhasil disimpan']);
    }

    public function uploadFile(Request $request, $id)
    {
        $this->authorize('add-31');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');

        try {
            DB::beginTransaction();

            $model = PegawaiPengajuanGaji::find($id);

            if ($request->hasFile('file')) {
                $fileName = $tglnow.'_'.rand(1,10000).'_Pengajuan_gaji_berkala_'.$request->file('file')->getClientOriginalName();   
                $filePath = $request->file('file')->storeAs('public/uploaded/pengajuangajiberkala', $fileName);   
                // $filePath = $request->file('file')->move(storage_path('app/public/uploaded/pengajuangajiberkala'), $fileName);   
                $model->file = $fileName;
            }

            // dd($model->file);

            $model->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $model->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'file uploaded successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function downloadFile($id)
    {
        try{

            $this->authorize('print-31');
            $model = PegawaiPengajuanGaji::find($id);
            $filename = $model->file;
    
            $file = storage_path('app/public/uploaded/pengajuangajiberkala/' . $filename);

            if (!file_exists($file)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $mimeType = mime_content_type($file);
            $headers = [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . basename($file) . '"',
            ];
    
            // return response()->download($file);
            return response()->file($file, $headers);
        }
        catch (Exception $e) {
            // dd($e->getMessage());
            return $this->sendError('Error', $e->getMessage());
        }
        
    }

    public function updatePengajuan(Request $request, $pengajuangajiberkalaid)
    {
        $this->authorize('edit-31');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');
        $checkedData = json_decode($request->input('checkedData'), true);
        // dd($checkedData);

        try {
            DB::beginTransaction();

            $pengajuanModel = PengajuanGajiBerkala::find($pengajuangajiberkalaid);


            $pengajuanModel->tglverifikasi = $request->tglverifikasi;
            $pengajuanModel->status = $request->status;
            $pengajuanModel->keterangan = $request->keterangan;

            $pengajuanModel->fill(['opedit' => $user->login, 'pdedit' => $request->ip()]);

            $pengajuanModel->save();

            foreach ($checkedData as $key => $pegawai) {
                $pengajuanPegawaiModel = PegawaiPengajuanGaji::where('pengajuangajiberkalaid', $pengajuangajiberkalaid)->where('pegawaiid', $pegawai['pegawaiid'])->firstOrFail();
                $pengajuanPegawaiModel->ketdinas = $request->keterangan;


                $historyPengajuanModel = new HistoryPengajuanGaji;
                $historyPengajuanModel->pegawaipengajuangajiid = $pengajuanPegawaiModel->pegawaipengajuangajiid;
                $historyPengajuanModel->tglverifikasi = $pengajuanModel->tglverifikasi;
                $historyPengajuanModel->keterangan = $request->keterangan;
                $historyPengajuanModel->status = $pengajuanModel->status;

                $historyPengajuanModel->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

                $historyPengajuanModel->save();

            }

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'pengajuan updated successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function loadDetailPegawai(Request $request, $pegawaiid)
    {
        $this->authorize('view-31');

        if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',1);

            try {
                $pegawai = DB::table('tbmpegawai')
                    ->leftJoin('tbmdetailpegawai', function($join)
                    {
                        $join->on('tbmdetailpegawai.pegawaiid', '=', 'tbmpegawai.pegawaiid');
                        $join->on('tbmdetailpegawai.dlt','=', DB::raw("'0'"));
                    })
                    ->select(
                        'tbmdetailpegawai.*',
                        'tbmpegawai.nip',
                        'tbmpegawai.nama',
                        'tbmpegawai.tgllahir'
                    )
                    ->where('tbmpegawai.pegawaiid', $pegawaiid)
                    // ->orderBy('tbmpegawai.pegawaiid')
                ;

                $count = $pegawai->count();
                $data = $pegawai->skip($page)->take($perpage)->get();

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

    public function loadHistoryPegawai(Request $request, $pegawaipengajuangajiid)
    {
        $this->authorize('view-31');

        if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',1);

            try {
                $historypengajuan = DB::table('tbhistorypengajuangaji')
                    ->select(
                        'tbhistorypengajuangaji.*'
                    )
                    ->where('tbhistorypengajuangaji.pegawaipengajuangajiid', $pegawaipengajuangajiid)
                    ->orderBy('tbhistorypengajuangaji.tglverifikasi')
                ;

                $count = $historypengajuan->count();
                // $data = $historypengajuan->skip($page)->take($perpage)->get();
                $data = $historypengajuan->get();

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
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-31');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');

        try {
            DB::beginTransaction();

            $model = PengajuanGajiBerkala::find($id);

            $model->dlt = '1';
            $model->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $model->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'pengajuan gaji berkala deleted successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }
}
