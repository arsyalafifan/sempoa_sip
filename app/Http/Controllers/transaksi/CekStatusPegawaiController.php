<?php

namespace App\Http\Controllers\transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\master\Pegawai;
use App\Models\transaksi\HistoryPengajuanGaji;
use App\Models\transaksi\PegawaiPengajuanGaji;

class CekStatusPegawaiController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jabatan = DB::table('tbmjabatan')
            ->select('tbmjabatan.jabatanid', 'tbmjabatan.namajabatan')
            ->where('tbmjabatan.dlt', 0)
            ->orderBy('tbmjabatan.jabatanid')
            ->get()
        ;
        $subQuery = DB::table('tbpengajuangajiberkala as sub_tbpengajuangajiberkala')
            ->select('sub_tbpegawaipengajuangaji.pegawaiid', DB::raw('MAX(sub_tbpengajuangajiberkala.pengajuangajiberkalaid) as max_pengajuangajiberkalaid'))
            ->join('tbpegawaipengajuangaji as sub_tbpegawaipengajuangaji', 'sub_tbpegawaipengajuangaji.pengajuangajiberkalaid', '=', 'sub_tbpengajuangajiberkala.pengajuangajiberkalaid')
            ->groupBy('sub_tbpegawaipengajuangaji.pegawaiid');

        // Menghitung total data tanpa menggunakan distinct dan pagination
        $statuspengajuan = DB::table('tbpengajuangajiberkala as tbpengajuangajiberkala')
        ->select('tbpengajuangajiberkala.status as mainstatus', 'sub.max_pengajuangajiberkalaid as max')
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
        // ->distinct()
        ;

        // Ambil total count
        $countUsulBaru = $statuspengajuan;

        $countUsulBaru = clone $statuspengajuan;
        // $countUsulBaru = $countUsulBaru->where('tbpengajuangajiberkala.status', '=', '1')->count();
        $countUsulBaru = DB::table(DB::raw("({$countUsulBaru->toSql()}) as subsql"))
            ->where('subsql.mainstatus', '=', '1')
            ->mergeBindings($countUsulBaru)
            ->distinct()
            ->count('subsql.max');
        
        $countBV = clone $statuspengajuan;
        $countBV = DB::table(DB::raw("({$countBV->toSql()}) as subsql"))
        ->where('subsql.mainstatus', '=', '2')
        ->mergeBindings($countBV)
        ->distinct()
        ->count('subsql.max');
        
        $countTMS = clone $statuspengajuan;
        $countTMS = DB::table(DB::raw("({$countTMS->toSql()}) as subsql"))
        ->where('subsql.mainstatus', '=', '3')
        ->mergeBindings($countTMS)
        ->distinct()
        ->count('subsql.max');
        
        $countMS = clone $statuspengajuan;
        $countMS = DB::table(DB::raw("({$countMS->toSql()}) as subsql"))
        ->where('subsql.mainstatus', '=', '4')
        ->mergeBindings($countMS)
        ->distinct()
        ->count('subsql.max');
        
        $countTurunStatus = clone $statuspengajuan;
        $countTurunStatus = DB::table(DB::raw("({$countTurunStatus->toSql()}) as subsql"))
        ->where('subsql.mainstatus', '=', '5')
        ->mergeBindings($countTurunStatus)
        ->distinct()
        ->count('subsql.max');
        
        $countProsesBKD = clone $statuspengajuan;
        $countProsesBKD = DB::table(DB::raw("({$countProsesBKD->toSql()}) as subsql"))
        ->where('subsql.mainstatus', '=', '6')
        ->mergeBindings($countProsesBKD)
        ->distinct()
        ->count('subsql.max');
        
        $countSelesai = clone $statuspengajuan;
        $countSelesai = DB::table(DB::raw("({$countSelesai->toSql()}) as subsql"))
        ->where('subsql.mainstatus', '=', '7')
        ->mergeBindings($countSelesai)
        ->distinct()
        ->count('subsql.max');
        // dd($countSelesai);

        $chartData = [
            'labels' => ['Usul Baru', 'BV', 'TMS', 'MS', 'Turun Status', 'Proses BKD', 'Selesai'],
            'data' => [$countUsulBaru, $countBV, $countTMS, $countMS, $countTurunStatus, $countProsesBKD, $countSelesai],
        ];

        return view(
            'transaksi.caristatuspegawai.index',
            [
                'jabatan' => $jabatan,
                'chartData' => $chartData,
            ]
        );
    }

    public function loadCekStatusDetailPegawai(Request $request, $nip)
    {
        // $this->authorize('view-31');

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
                        'tbmpegawai.tgllahir',
                        'tbmpegawai.pegawaiid'
                    )
                    ->where('tbmpegawai.nip', $nip)
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

    public function loadCekHistoryPegawai(Request $request, $pegawaiid)
    {
        // $this->authorize('view-31');

        if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',1);

            try {
                $historypengajuan = DB::table('tbmpegawai')
                ->select(
                        'tbhistorypengajuangaji.keterangan', 
                        'tbhistorypengajuangaji.tglverifikasi', 
                        'tbmpegawai.*', 
                        'tbhistorypengajuangaji.status', 
                        'tbpegawaipengajuangaji.pegawaipengajuangajiid',
                        'tbpegawaipengajuangaji.file'
                    )
                ->join('tbpegawaipengajuangaji', 'tbpegawaipengajuangaji.pegawaiid', '=', 'tbmpegawai.pegawaiid')
                ->join('tbhistorypengajuangaji', 'tbhistorypengajuangaji.pegawaipengajuangajiid', '=', 'tbpegawaipengajuangaji.pegawaipengajuangajiid')
                ->where('tbmpegawai.pegawaiid', $pegawaiid)
                ->orderBy('tbhistorypengajuangaji.historypengajuangajiid', 'desc');

                $count = $historypengajuan->count();
                $data = $historypengajuan->skip($page)->take($perpage)->get();
                // $data = $historypengajuan->get();
                // dd($data);

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

    public function storeHistory(Request $request)
    {
        // $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');
        // dd($checkedData);

        try {
            DB::beginTransaction();

            $pegawaiPengajuanGajiModel = PegawaiPengajuanGaji::find($request->current_pegawaipengajuangajiid);
            $pegawaiPengajuanGajiModel->ketpegawai = $request->keterangan;

            $pegawaiPengajuanGajiModel->save();

            // $historyPengajuanModel = new HistoryPengajuanGaji;
            // $historyPengajuanModel->pegawaipengajuangajiid = $request->current_pegawaipengajuangajiid;
            // $historyPengajuanModel->tglverifikasi = $tglnow;
            // $historyPengajuanModel->keterangan = $request->keterangan;
            // $historyPengajuanModel->status = $request->current_status;

            // $historyPengajuanModel->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            // $historyPengajuanModel->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'history added successfully.',
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
         $model = PegawaiPengajuanGaji::find($id);
         $filename = $model->file;
 
         $file = public_path().'/storage/uploaded/pengajuangajiberkala/'.$filename;
 
         return response()->download($file);
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
}
