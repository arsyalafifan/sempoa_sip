<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master\Ijazahtemp\CreateRequest;
use App\Http\Requests\master\Ijazahtemp\UpdateRequest;
use App\Models\master\Sekolah;
use App\Models\master\Ijazahtemp;
// use App\Models\master\Ijazah;
use App\Models\legalisir\Legalisir;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class IjazahtempController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Ijazahtemp';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id=null)
    {   
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        
        try{
            $model = new Ijazahtemp();

            DB::beginTransaction();
            $noijazah = $request->noijazah;
            $dataIjazah = Ijazahtemp::where('noijazah', $noijazah)
            ->where('dlt', 0)
            ->where('status', '!=', 2)
            ->first();
            if($dataIjazah != null){
                return redirect()->route('daftar-ijazah')->with('error', 'Tidak bisa melakukan pendaftaran karena 
                No Ijazah sedang dalam pengajuan.');
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
            $tglnow = date("Y-m-d");
            $model->tgl_pengajuan = $tglnow;
            $model->status = 0;
            $model->keterangan = "Permohonan pendaftraan ijazah";
            $filename = $tglnow.'_'.rand(1,1000).'_'.$request->namasiswa.'.pdf';
            
            if ($request->file('file_ijazah')) {
                $request->file('file_ijazah')->storeAs('public/uploaded/ijazah', $filename);
                $model->file_ijazah = 'uploaded/ijazah/'. $filename;
            }
            if ($request->file('file_ktp')) {
                $request->file('file_ktp')->storeAs('public/uploaded/ktp', $filename);
                $model->file_ktp = 'uploaded/ktp/'. $filename;
            }

            $model->opadd = $request->namasiswa;
            $model->pcadd = $request->ip();

            $model->fill(['opadd' => $request->namasiswa, 'pcadd' => $request->ip()]);

            if ($model->save()) {
                
            }

            // dd($request->all());

            DB::commit();
            return redirect()->route('daftar-ijazah')->with('success', 'Perdaftaran Ijazah Berhasil.');
            
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

        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
       
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
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        
    }

    public function search(Request $request)
    {
        
    }

    public function daftarijazah(Request $request)
    {
        // $this->authorize('view-6');

        Log::channel('mibedil')->info('Halaman '.$this->page);

        $sekolah = [];
        $ijazah = [];
        $kota = [];
        $kecamatan = [];
        $tahunajaran = [];

        if($request->ajax())
        {
            // $search = $request->search;
            $kotaid = $request->kotaid;
            $kecamatanid = $request->kecamatanid;
            $jenjang = $request->jenjang;
            $jenis = $request->jenis;
            $sekolahid = $request->sekolahid;

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $ijazah = DB::table('tbmijazah')
                        ->join('tbmsekolah', function($join)
                        {
                            $join->on('tbmsekolah.sekolahid', '=', 'tbmijazah.sekolahid');
                            $join->on('tbmsekolah.dlt','=',DB::raw("'0'"));
                        })
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
                        ->select('tbmijazah.*', 'tbmsekolah.namasekolah')
                        ->where('tbmijazah.dlt', '0')
                        ->where(function($query) use ($sekolahid, $kotaid, $kecamatanid, $jenjang, $jenis)
                        {
                            if (!is_null($kotaid) && $kotaid!='') $query->where('tbmsekolah.kotaid', $kotaid);
                            if (!is_null($kecamatanid) && $kecamatanid!='') $query->where('tbmsekolah.kecamatanid', $kecamatanid);
                            if (!is_null($jenjang) && $jenjang!='') $query->where('tbmsekolah.jenjang', $jenjang);
                            if (!is_null($jenis) && $jenis!='') $query->where('tbmsekolah.jenis', $jenis);
                            if (!is_null($sekolahid) && $sekolahid!='') $query->where('tbmijazah.sekolahid', $sekolahid);
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

        return view(
            'layananspakat.daftar_ijazah', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' =>  route('ijazah.createWithSekolah',['provId' => ':provid','sekolahId' => ':id']) , 
                'ijazah' => $ijazah, 
                'kota' => $kota,
                'kecamatan' => $kecamatan,
                'sekolah' => $sekolah,
            ]);
    }
}
