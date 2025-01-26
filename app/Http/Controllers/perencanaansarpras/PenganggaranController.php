<?php

namespace App\Http\Controllers\perencanaansarpras;

use App\enumVar as enum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Controllers\HelperController as HelperController;
use App\Models\master\Sekolah;
use App\Models\perencanaansarpras\DetailPaguPenganggaran;
use App\Models\perencanaansarpras\DetailPenganggaran;
use App\Models\sarpras\SarprasKebutuhan;
use App\Models\verifikasi\StatusKebutuhanSarpras;
use Symfony\Component\CssSelector\Node\FunctionNode;

class PenganggaranController extends BaseController
{

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Penganggaran';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-19');

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
                        'tbtranssarpraskebutuhan.status >= '.enum::STATUS_KEBUTUHAN_SARPRAS_DISETUJUI.''
                    )
                    ->where(function($query){
                        if(Auth::user()->isSekolah()) $query->where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid);
                    })
                    ->where('tbtranssarpraskebutuhan.dlt', DB::raw("'0'"))
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
            'perencanaansarpras.penganggaran.index', 
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
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-19');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $model = DetailPenganggaran::find($id);

            $model->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $model->dlt = '1';

            $model->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail penganggaran deleted successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }


    public function detailanggaran(Request $request, $sarpraskebutuhanid)
    {
        $this->authorize('view-19');

        if($request->ajax())
        {
            $data = [];
            $totalPagu = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $detailpenganggaran = DB::table('tbdetailpenganggaran')
                    ->leftJoin('tbmsubkeg', function($join){
                        $join->on('tbmsubkeg.subkegid', '=', 'tbdetailpenganggaran.subkegid');
                    })
                    ->select('tbdetailpenganggaran.*', 'tbmsubkeg.subkegnama')
                    ->where('tbdetailpenganggaran.sarpraskebutuhanid', $sarpraskebutuhanid)
                    ->where('tbdetailpenganggaran.dlt', 0)
                    ->orderBy('tbdetailpenganggaran.detailpenganggaranid')
                ;

                // dd($detailpenganggaran->get());

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

                return $this->sendResponse([
                    'data' => $data,
                    'count' => $count,
                    'totalPagu' => $totalPagu,
                ], 'Detail penganggaran retrieved successfully.');  

            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
        }
    }

    public function storeDetailPenganggaran(Request $request, $id)
    {
        $this->authorize('add-19');

        $user = auth('sanctum')->user();

        try {

            $model = new DetailPenganggaran();
 
            DB::beginTransaction();

            $model->subkegid = $request->subkegid;
            $model->sumberdana = $request->sumberdana;
            $model->sarpraskebutuhanid = $request->sarpraskebutuhanid;
            $model->jumlah = $request->jumlah;
            $model->satuan = $request->satuan;
            $model->subdetailkegiatan = $request->subdetailkeg;
            $model->jenispenganggaran = $request->jenispenganggaran;
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            if($model->save())
            {
                // Save multiple and file 
                $tglnow = date('Y-m-d');

                foreach ($request->jenispagu as $key => $value) {

                    $modelDetailPaguSarpras = new DetailPaguPenganggaran();

                    $dataJenisPagu = $request->jenispagu[$key];
                    $dataNilaiPagu = str_replace(',', '', $request->nilaipagu[$key]);

                    $modelDetailPaguSarpras->jenispagu = $dataJenisPagu;
                    $modelDetailPaguSarpras->nilaipagu = $dataNilaiPagu;
                    $modelDetailPaguSarpras->detailpenganggaranid = $model->detailpenganggaranid;

                    $modelDetailPaguSarpras->save();

                }
            }
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail penganggaran sarpras added successfully.',
            ], 200);

            // return redirect()->route('sarprastersedia.index')
            // ->with('success', 'Data detail sarpras berhasil ditambah.', ['page' => $this->page]);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function updateDetailPenganggaran(Request $request, $id)
    {
        $this->authorize('edit-19');

        $user = auth('sanctum')->user();

        try {

            // $model = DetailPenganggaran::where('detailpenganggaranid', $id);
            $model = DetailPenganggaran::find($id);
 
            DB::beginTransaction();

            $model->subkegid = $request->subkegid;
            $model->sumberdana = $request->sumberdana;
            // $model->sarpraskebutuhanid = $request->sarpraskebutuhanid;
            $model->jumlah = $request->jumlah;
            $model->satuan = $request->satuan;
            $model->subdetailkegiatan = $request->subdetailkeg;
            $model->jenispenganggaran = $request->jenispenganggaran;
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

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

    public function showDetailPenganggaran(Request $request, $id)
    {

        $this->authorize('view-19');

        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $detailpenganggaransarpras = DB::table('tbdetailpagupenganggaran')
                    ->leftJoin('tbmperusahaan', function($join){
                        $join->on('tbmperusahaan.perusahaanid', '=', 'tbdetailpagupenganggaran.perusahaanid');
                        $join->on('tbmperusahaan.dlt', '=', DB::raw("'0'"));
                    })
                    ->select('tbdetailpagupenganggaran.*', 'tbmperusahaan.nama')
                    ->where('tbdetailpagupenganggaran.detailpenganggaranid', $id)
                    ->where('tbdetailpagupenganggaran.dlt', 0)
                    ->orderBy('tbdetailpagupenganggaran.jenispagu')
                ;

                $querySum = DB::table('tbdetailpagupenganggaran')
                    ->select(DB::raw('SUM(nilaipagu) as countpagu'), DB::raw('SUM(nilaikontrak) as countkontrak'))
                    ->where('tbdetailpagupenganggaran.detailpenganggaranid', $id)
                    ->where('tbdetailpagupenganggaran.dlt', 0)
                ;

                // $querySumKontrak = DB::table('tbdetailpagupenganggaran')
                //     ->select(DB::raw('SUM(nilaikontrak) as countkontrak'))
                //     ->where('tbdetailpagupenganggaran.detailpenganggaranid', $id)
                //     ->where('tbdetailpagupenganggaran.dlt', 0)
                // ;

                $count = $detailpenganggaransarpras->count();
                $data = $detailpenganggaransarpras->skip($page)->take($perpage)->get();
                $sumRupiah = $querySum->get();
                // $sumKontrak = $querySumKontrak->get();

                return $this->sendResponse([
                    'data' => $data,
                    'count' => $count,
                    'sumPagu' => $sumRupiah,
                    'terbilangNilaiPagu' => $this->bacaTerbilang($sumRupiah[0]->countpagu),
                    'terbilangNilaiKontrak' => $this->bacaTerbilang($sumRupiah[0]->countkontrak)
                ], 'Data retrieved successfully.'); 

            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

        }
    }

    public function storeDetailPaguPenganggaran(Request $request, $id)
    {
        $this->authorize('add-19');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $modelDetailPaguSarpras = new DetailPaguPenganggaran;


            $modelDetailPaguSarpras->jenispagu = $request->jenispagu;
            $modelDetailPaguSarpras->nilaipagu = str_replace('.', '', $request->nilaipagu);
            $modelDetailPaguSarpras->detailpenganggaranid = $id;
            $modelDetailPaguSarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $modelDetailPaguSarpras->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail pagu penganggaran sarpras added successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function updateDetailPaguPenganggaran(Request $request, $id)
    {
        $this->authorize('edit-19');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $modelDetailPaguSarpras = DetailPaguPenganggaran::find($id);

            $modelDetailPaguSarpras->jenispagu = $request->jenispagu;
            $modelDetailPaguSarpras->nilaipagu = str_replace('.', '', $request->nilaipagu);
            $modelDetailPaguSarpras->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $modelDetailPaguSarpras->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail pagu penganggaran sarpras updated successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function deleteDetailPaguPenganggaran(Request $request, $id)
    {
        $this->authorize('delete-19');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $modelDetailPaguSarpras = DetailPaguPenganggaran::find($id);

            $modelDetailPaguSarpras->dlt = 1;
            $modelDetailPaguSarpras->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $modelDetailPaguSarpras->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'detail pagu penganggaran sarpras updated successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function prosestender(Request $request, $id)
    {
        $this->authorize('edit-19');
        $user = auth('sanctum')->user();

        $detailpenganggaransarpras = DetailPenganggaran::where('sarpraskebutuhanid', $id);
        // dd($detailpenganggaransarpras->count());

        try {
            DB::beginTransaction();

            $sarpraskebutuhan = SarprasKebutuhan::where('sarpraskebutuhanid', $id)
                ->where('dlt', 0)
                ->firstOrFail()
            ;

            if($sarpraskebutuhan->status != enum::STATUS_KEBUTUHAN_SARPRAS_DISETUJUI)
            {
                return response([
                    'success' => false,
                    'data' => 'error',
                    'message' => 'Tidak dapat melakukan proses tender'
                ]);
            }
            else
            {
                if ($detailpenganggaransarpras->count() > 0) {
                    $sarpraskebutuhan->status = enum::STATUS_KEBUTUHAN_SARPRAS_PROSES_TENDER;
                    $sarpraskebutuhan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

                    if($sarpraskebutuhan->save())
                    {
                        $statuskebutuhansarpras = new StatusKebutuhanSarpras();
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
                        'message' => 'Berhasil mengubah status menjadi proses tender.',
                    ], 200);
                }else{
                    return response([
                        'success' => false,
                        'data'    => 'failed',
                        'message' => 'Detail penganggaran belum diisi.',
                    ], 200);
                }
            }
        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function batalprosestender(Request $request, $id)
    {
        $this->authorize('edit-19');
        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $sarpraskebutuhan = SarprasKebutuhan::where('sarpraskebutuhanid', $id)
                ->where('dlt', 0)
                ->firstOrFail()
            ;

            if($sarpraskebutuhan->status != enum::STATUS_KEBUTUHAN_SARPRAS_PROSES_TENDER)
            {
                return response([
                    'success' => false,
                    'data' => 'error',
                    'message' => 'Tidak dapat membatalkan proses tender'
                ]);
            }else{
                $sarpraskebutuhan->status = enum::STATUS_KEBUTUHAN_SARPRAS_DISETUJUI;
                $sarpraskebutuhan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

                if($sarpraskebutuhan->save())
                {
                    $statuskebutuhansarpras = new StatusKebutuhanSarpras();
                    $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                    $statuskebutuhansarpras->sarpraskebutuhanid = $sarpraskebutuhan->sarpraskebutuhanid;
                    $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_DISETUJUI;
                    $statuskebutuhansarpras->tgl = date('Y-m-d');

                    $statuskebutuhansarpras->save();
                }

                DB::commit();

                return response([
                    'success' => true,
                    'data'    => 'Success',
                    'message' => 'Berhasil mengubah status menjadi pengajuan kebutuhan sarpras disetujui.',
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
