<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master\Ijazah\CreateRequest;
use App\Http\Requests\master\Ijazah\UpdateRequest;
use App\Models\master\Sekolah;
use App\Models\master\Ijazah;
use App\Models\master\Ijazahtemp;
use App\Models\legalisir\Legalisir;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class IjazahController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Ijazah';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-6');

        Log::channel('mibedil')->info('Halaman '.$this->page);

        $sekolah = [];
        $ijazah = [];
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
            $provinsiid = $request->provinsiid;

            $sekolahid = Auth::user()->isSekolah() ? auth('sanctum')->user()->sekolahid : $request->sekolahid;

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $ijazah = DB::table('tbmijazah')
                        ->leftjoin('tbmsekolah', function($join)
                        {
                            $join->on('tbmsekolah.sekolahid', '=', 'tbmijazah.sekolahid');
                            $join->on('tbmsekolah.dlt','=',DB::raw("'0'"));
                        })
                        ->leftjoin('tbmkota', function($join)
                                {
                                    $join->on('tbmkota.kotaid', '=', 'tbmsekolah.kotaid');
                                    $join->on('tbmkota.dlt','=',DB::raw("'0'"));
                                })
                        ->leftjoin('tbmkecamatan', function($join)
                                {
                                    $join->on('tbmkecamatan.kecamatanid', '=', 'tbmsekolah.kecamatanid');
                                    $join->on('tbmkecamatan.dlt','=',DB::raw("'0'"));
                                })
                        ->select('tbmijazah.*', 'tbmsekolah.namasekolah as ns')
                        ->where('tbmijazah.dlt', '0')
                        ->where(function($query){
                            if(Auth::user()->isSekolah()) $query->where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid);
                        })
                        ->where(function($query) use ($provinsiid, $sekolahid, $kotaid, $kecamatanid, $jenjang, $jenis, $search)
                        {
                            if (!is_null($provinsiid) && $provinsiid!='') $query->where('tbmijazah.provinsiid', $provinsiid);
                            if (!is_null($kotaid) && $kotaid!='') $query->where('tbmsekolah.kotaid', $kotaid);
                            if (!is_null($kecamatanid) && $kecamatanid!='') $query->where('tbmsekolah.kecamatanid', $kecamatanid);
                            if (!is_null($jenjang) && $jenjang!='') $query->where('tbmsekolah.jenjang', $jenjang);
                            if (!is_null($jenis) && $jenis!='') $query->where('tbmsekolah.jenis', $jenis);
                            if (!is_null($sekolahid) && $sekolahid!='') $query->where('tbmijazah.sekolahid', $sekolahid);

                            if (!is_null($search) && $search!='') {
                                $query->where(DB::raw('tbmijazah.namasiswa'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmijazah.nis'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmijazah.noijazah'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmijazah.namasekolah'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmsekolah.namasekolah'), 'ilike', '%'.$search.'%');
                            }
                        })
                        ->orderBy('tbmijazah.nis')
                ;

                $count = $ijazah->count();
                $data = $ijazah->skip($page)->take($perpage)->get();
            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'Ijazah retrieved successfully.');  
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

        $sekolah = DB::table('tbmsekolah')
            ->select('tbmsekolah.sekolahid', 'tbmsekolah.namasekolah')
            ->where('tbmsekolah.dlt', 0)
            ->orderBy('tbmsekolah.namasekolah')
            ->get()
        ;

        $userSekolah = Sekolah::where('tbmsekolah.sekolahid', auth('sanctum')->user()->sekolahid == null ? -1 : auth('sanctum')->user()->sekolahid)->first();

        return view(
            'master.ijazah.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' =>  route('ijazah.createWithSekolah',['provId' => ':provid','sekolahId' => ':id']) , 
                'ijazah' => $ijazah, 
                'kota' => $kota,
                'kecamatan' => $kecamatan,
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
    public function create($prov = null, $id = null)
    {   
        $this->authorize('add-6');
        if(Auth::user()->isSekolah() && $id != auth('sanctum')->user()->sekolahid) return view('errors.403');

        if (is_null($prov) && is_null($id)) {
            return redirect()->route('ijazah.index'); 
        }
        Log::channel('mibedil')->info('Halaman Tambah '.$this->page, ['id' => $id]); 
        $namasekolah = '';
        $namakec = '';
        $namakab = '';
        $namaprov = '';
        if($id != "null"){
            $sekolah = Sekolah::where('sekolahid', $id)
            ->where('dlt', 0)
            ->firstOrFail();
            $namasekolah = $sekolah->namasekolah;
            $namakec = $sekolah->kecamatan->namakec;
            $namakab = $sekolah->kota->namakota;
            $namaprov = strtoupper(enum::PROVINSI_DESC_KEPRI);

        }
        return view(
            'master.ijazah.create', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('ijazah.index'), 
                'sekolah' =>$prov == enum::PROVINSI_KEPRI ? $id : null,
                'prov' => $prov,
                'namasekolah' => $namasekolah, 
                'namaprov' => $namaprov,
                'namakab' => $namakab,
                'namakec' => $namakec,
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
        $this->authorize('edit-6');

        $user = auth('sanctum')->user();

        try{
            $user = auth('sanctum')->user();
            $model = new Ijazah();
            $tglnow = date("Y-m-d");

            DB::beginTransaction();
            $noijazah = $request->noijazah;
            $dataIjazah = Ijazahtemp::where('noijazah', $noijazah)
            ->where('dlt', 0)
            ->first();
            if($dataIjazah != null){
                return redirect()->route('ijazah.createWithSekolah',['provId' => $request->provinsiid,'sekolahId' => $request->sekolahid])->with('error', 'Tidak bisa melakukan penambahan data karena No Ijazah dalam pengajuan.');
            }
            $model->namasiswa = $request->namasiswa;
            $model->tempat_lahir = $request->tempat_lahir;
            $model->tgl_lahir = $request->tgl_lahir;
            $model->namaortu = $request->namaortu;
            $model->nis = $request->nis;
            $model->noijazah = $request->noijazah;
            $model->tgl_lulus = $request->tgl_lulus;
            $model->sekolahid = $request->sekolahid;
            $model->provinsiid = $request->provinsiid;
            $model->namaprov = isset($request->namaprov) ? $request->namaprov : null;
            $model->namakab = isset($request->namakab) ? $request->namakab : null;
            $model->namakec = isset($request->namakec) ? $request->namakec : null;
            $model->namasekolah = isset($request->namasekolah) ? $request->namasekolah : null;
            $filename = $tglnow.'_'.rand(1,1000).'_'.$request->namasiswa.'.pdf';
            
            if ($request->file('file_ijazah')) {
                $request->file('file_ijazah')->storeAs('public/uploaded/ijazah', $filename);
                $model->file_ijazah = 'uploaded/ijazah/'. $filename;
            }

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            if ($model->save()) {
                
            }

            // dd($request->all());

            DB::commit();

            return redirect()->route('ijazah.index')
            ->with('success', 'Data ijazah berhasil ditambah.', ['page' => $this->page]);
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

        $this->authorize('view-6');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataIjazah = Ijazah::where('ijazahid', $id)
        ->where('dlt', 0)
        ->first();

        return view('master.ijazah.show', ['page' => $this->page, 'child' => 'Lihat Data', 'masterurl' => route('ijazah.index'), 'ijazah' => $dataIjazah]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->authorize('edit-6');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $ijazah = Ijazah::where('ijazahid', $id)
        ->where('dlt', 0)
        ->firstOrFail();
        
        if ($ijazah->provinsiid == enum::PROVINSI_LAINNYA){
            $ijazah->provinsiid = enum::PROVINSI_LAINNYA;
        }else{
            $sekolah = Sekolah::where('sekolahid', $ijazah->sekolahid)
            ->where('dlt', 0)
            ->firstOrFail();
            $ijazah->namasekolah = $sekolah->namasekolah;
            $ijazah->namakec = $sekolah->kecamatan->namakec;
            $ijazah->namakab = $sekolah->kota->namakota;
            $ijazah->namaprov = strtoupper(enum::PROVINSI_DESC_KEPRI);
            $ijazah->provinsiid = enum::PROVINSI_KEPRI;
            
        }
       
        return view(
            'master.ijazah.edit', 
            [
                'page' => $this->page, 
                'child' => 'Ubah Data', 
                'masterurl' => route('ijazah.index'), 
                'ijazah' => $ijazah,
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
        $this->authorize('edit-6');

        $user = auth('sanctum')->user();
        $tglnow = date("Y-m-d");

        try {
            DB::beginTransaction();

            $ijazah = Ijazah::where('ijazahid', $id)
            ->where('dlt', 0)
            ->first();
            $ijazah->namasiswa = $request->namasiswa;
            $ijazah->tempat_lahir = $request->tempat_lahir;
            $ijazah->tgl_lahir = $request->tgl_lahir;
            $ijazah->namaortu = $request->namaortu;
            $ijazah->nis = $request->nis;
            $ijazah->noijazah = $request->noijazah;
            $ijazah->tgl_lulus = $request->tgl_lulus;
            $ijazah->namaprov = isset($request->namaprov) ? $request->namaprov : null;
            $ijazah->namakab = isset($request->namakab) ? $request->namakab : null;
            $ijazah->namakec = isset($request->namakec) ? $request->namakec : null;
            $ijazah->namasekolah = isset($request->namasekolah) ? $request->namasekolah : null;
            $filename = $tglnow.'_'.rand(1,1000).'_'.$request->namasiswa.'.pdf';
            
            if ($request->file('file_ijazah')) {
                $request->file('file_ijazah')->storeAs('public/uploaded/ijazah', $filename);
                $ijazah->file_ijazah = 'uploaded/ijazah/'. $filename;
            }
            // $ijazah->sekolahid = 52;

            $ijazah->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $ijazah->save();

            DB::commit();
        }catch(QueryException $e){
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('ijazah.index')
                ->with('success', 'Data berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $this->authorize('hapus-6');

        $user = auth('sanctum')->user();

        $ijazah = Ijazah::where('ijazahid', $id)
        ->where('dlt', 0)
        ->first();

        $ijazah->dlt = '1';

        $ijazah->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $ijazah->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Ijazah Berhasil Dihapus.',
        ], 200);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Ijazah::where('noijazah', '=', $query)->first();

        if (!$results) {
                // return redirect()->route('legalisir-dashboard')->with('error', 'No ijazah yang anda masukkan salah atau belum terdaftar di database, silahkan masukkan no ijazah dengan benar atau bisa daftarkan permohonan data ijazah' ); 
                return redirect()->route('legalisir-dashboard')->with('error', 'No ijazah yang anda masukkan salah atau belum terdaftar di database, silahkan masukkan no ijazah dengan benar atau bisa <a href="' . route('daftar-ijazah') . '">daftarkan permohonan data ijazah</a>' );
        }else{

            $history =  DB::table('tbmlegalisir')
            ->select(DB::raw('*'), DB::raw('tgledit + INTERVAL \'3 days\' as masaberlaku'))
            ->where('tbmlegalisir.ijazahid','=', $results->ijazahid)
            ->where('tbmlegalisir.dlt','=', 0)
            ->orderByDesc('tgladd')
            ->get();

            // tanggal dan waktu hari ini 
            $now = date("Y-m-d h:i:s");

            return view('layananspakat.legalisir_ijazah', ['history' => $history,'results' => $results, 'query' => $query, 'now' => $now]);
        }
    }
}
