<?php

namespace App\Http\Controllers\verifikasi;

use App\enumVar as enum;
use App\Http\Controllers\Controller;
use App\Models\master\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\sarpras\SarprasKebutuhan;
use App\Models\verifikasi\StatusKebutuhanSarpras;

class VerifikasiKebutuhanSarprasController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Verifikasi Kebutuhan Sarpras';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-18');

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
                    ->where('tbtranssarpraskebutuhan.dlt', 0)
                    ->where('tbtranssarpraskebutuhan.status', '<>', DB::raw("'1'"))
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

        $userSekolah = Sekolah::where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid == null ? -1 : auth('sanctum')->user()->sekolahid)->first();

        return view(
            'verifikasi.kebutuhansarpras.index', 
            [
                'page' => $this->page, 
                // 'createbutton' => true, 
                // 'createurl' => route('sarpraskebutuhan.createBySekolahId', ['sekolahid' => ':sekolahid']), 
                'kota' => $kota,
                'sekolah' => $sekolah,
                'kecamatan' => $kecamatan,
                // 'kebutuhansarpras' => $sarpraskebutuhan,
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

    public function history(Request $request, $sarpraskebutuhanid)
    {
        $this->authorize('view-18');

        if($request->ajax())
        {
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $statussarpraskebutuhan = DB::table('tbstatuskebutuhansarpras')
                    ->select('tbstatuskebutuhansarpras.*')
                    ->where('tbstatuskebutuhansarpras.sarpraskebutuhanid', $sarpraskebutuhanid)
                ;

                $count = $statussarpraskebutuhan->count();
                $data = $statussarpraskebutuhan->skip($page)->take($perpage)->get();

            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count,
            ], 'Status Sarpras kebutuhan retrieved successfully.');  
        }
    }

    public function setuju(Request $request, $id)
    {
        $this->authorize('edit-18');
        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $sarpraskebutuhan = SarprasKebutuhan::where('sarpraskebutuhanid', $id)
                ->where('dlt', 0)
                ->firstOrFail()
            ;

            $sarpraskebutuhan->status = enum::STATUS_KEBUTUHAN_SARPRAS_DISETUJUI;
            $sarpraskebutuhan->jumlahsetuju = $request->jumlahsetuju;
            $sarpraskebutuhan->satuansetuju = $request->satuan;
            // $sarpraskebutuhan->keterangan = $request->keterangan;
            $sarpraskebutuhan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            if($sarpraskebutuhan->save())
            {
                $statuskebutuhansarpras = new StatusKebutuhanSarpras;
                $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                $statuskebutuhansarpras->sarpraskebutuhanid = $sarpraskebutuhan->sarpraskebutuhanid;
                $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_DISETUJUI;
                $statuskebutuhansarpras->tgl = date('Y-m-d');
                $statuskebutuhansarpras->jumlahsetuju = $request->jumlahsetuju;
                $statuskebutuhansarpras->satuan = $request->satuan;

                $statuskebutuhansarpras->save();
            }

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'Berhasil mengubah status menjadi disetujui.',
            ], 200);
        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        $this->authorize('edit-18');
        $user = auth('sanctum')->user();

        try {

            DB::beginTransaction();

            $sarpraskebutuhan = SarprasKebutuhan::where('sarpraskebutuhanid', $id)
                ->where('dlt', 0)
                ->firstOrFail()
            ;

            $sarpraskebutuhan->status = enum::STATUS_KEBUTUHAN_SARPRAS_DITOLAK;
            // $sarpraskebutuhan->keterangan = $request->keterangan;
            $sarpraskebutuhan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            if($sarpraskebutuhan->save())
            {
                $statuskebutuhansarpras = new StatusKebutuhanSarpras;
                $statuskebutuhansarpras->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
                $statuskebutuhansarpras->sarpraskebutuhanid = $sarpraskebutuhan->sarpraskebutuhanid;
                $statuskebutuhansarpras->status = enum::STATUS_KEBUTUHAN_SARPRAS_DITOLAK;
                $statuskebutuhansarpras->tgl = date('Y-m-d');
                $statuskebutuhansarpras->keterangan = $request->keterangan;

                $statuskebutuhansarpras->save();
            }

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'Berhasil mengubah status menjadi ditolak.',
            ], 200);

            // return redirect()->route('verifikasikebutuhansarpras.index')->with('success', 'Data Permintaan Legalisir Berhasil Di tolak');

        } catch(\Throwable $th)
        {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    public function showFotoKebutuhanSarpras(Request $request, $id)
    {
        $this->authorize('view-18');
        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if($request->ajax())
        {
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $fotokebutuhansarpras = DB::table('tbtransfilesarpraskebutuhan')
                    ->select('tbtransfilesarpraskebutuhan.*')
                    ->where('tbtransfilesarpraskebutuhan.sarpraskebutuhanid', $id)
                    ->where('tbtransfilesarpraskebutuhan.dlt', '0')
                    ->orderBy('tbtransfilesarpraskebutuhan.filesarpraskebutuhanid')
                ;
                $count = $fotokebutuhansarpras->count();
                $data = $fotokebutuhansarpras->skip($page)->take($perpage)->get();
            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count,
            ], 'foto kebutuhan sarpras retrieved successfully.'); 
        }
    }
}
