<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Murid;
use Illuminate\Support\Facades\Auth;

class MuridController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Murid';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        //  dd($user->grup);
         Log::channel('mibedil')->info('Halaman '.$this->page);
         $murid = [];
 
         if($request->ajax())
         {
             $search = $request->search;
             $level = $request->level;
             $status = $request->status;
 
             $data = [];
             $count = 0;
             $page = $request->get('start', 0);  
             $perpage = $request->get('length',50);
 
             try {
                 $murid = DB::table('tbmurid')
                         // ->leftJoin('tbmsekolah', function($join)
                         // {
                         //     $join->on('tbmsekolah.sekolahid', '=', 'tbmmurid.sekolahid');
                         //     $join->on('tbmsekolah.dlt','=',DB::raw("'0'"));
                         // })
                         // ->leftJoin('tbmkota', function($join)
                         // {
                         //     $join->on('tbmkota.kotaid', '=', 'tbmsekolah.kotaid');
                         //     $join->on('tbmkota.dlt','=',DB::raw("'0'"));
                         // })
                         // ->leftJoin('tbmkecamatan', function($join)
                         // {
                         //     $join->on('tbmkecamatan.kecamatanid', '=', 'tbmsekolah.kecamatanid');
                         //     $join->on('tbmkecamatan.dlt','=',DB::raw("'0'"));
                         // })
                         ->select('tbmurid.*')
                         ->where('tbmurid.dlt', '0')
                         ->where(function($query) use ($user){
                            if($user->grup != 1) $query->where('tbmurid.kodemurid', '=', $user->login);
                        })
                         ->where(function ($query) use ($level, $status, $search) {
                                 if (!is_null($level) && $level != '') $query->where('tbmurid.level', $level);
                                 if (!is_null($status) && $status != '') $query->where('tbmurid.status', $status);
 
                                 if (!is_null($search) && $search!='') {
                                     // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                                     $query->where(DB::raw('tbmurid.kodemurid'), 'ilike', '%'.$search.'%');
                                     $query->orWhere(DB::raw('tbmurid.namamurid'), 'ilike', '%'.$search.'%');
                                     // $query->where('tbmurid.sekolahid', $sekolahid);
                                 }
                                 // if(Auth::user()->isSekolah()) $query->where('tbmurid.sekolahid', auth('sanctum')->user()->sekolahid);
                         });
 
                 $count = $murid->count();
                 $data = $murid->skip($page)->take($perpage)->get();
             }catch (QueryException $e) {
                 return $this->sendError('SQL Error', $this->getQueryError($e));
             }
             catch (Exception $e) {
                 return $this->sendError('Error', $e->getMessage());
             }
             return $this->sendResponse([
                 'data' => $data,
                 'count' => $count
             ], 'murid retrieved successfully.');  
         }
         return view(
             'murid.index', 
             [
                 'page' => $this->page, 
                 'createbutton' => Auth::user()->isSekolah() ? false : true, 
                 'createurl' => route('murid.create'), 
                 'murid' => $murid, 
                 'isSekolah' => Auth::user()->isSekolah()
             ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        return view(
            'murid.create', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('murid.index'), 
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $user = auth('sanctum')->user();

            $model = new Murid();

            DB::beginTransaction();
            $tahun = Date('Y');
            $model->kodemurid = $request->kodemurid;
            $model->namamurid = $request->namamurid;
            $model->jeniskelamin = $request->jeniskelamin;
            $model->alamat = $request->alamat;
            $model->agama = $request->agama;
            $model->tempatlahir = $request->tempatlahir;
            $model->tgllahir = $request->tgllahir;
            $model->notelp = $request->notelp;
            $model->namasekolah = $request->namasekolah;
            $model->namaortu = $request->namaortu;
            $model->tglmasuk = $request->tglmasuk;
            $model->emailortu = $request->emailortu;
            $model->level = $request->level;
            $model->status = $request->status;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('murid.index')
            ->with('success', 'Data murid berhasil ditambah.', ['page' => $this->page]);
        }catch (\Throwable $th) {
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
    public function edit($id)
    {
        Log::channel('disnaker')->info('Halaman Ubah '.$this->page);

        $murid = Murid::where('muridid', $id)->firstOrFail();

        return view('murid.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('murid.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('murid.index'), 
                        'murid' => $murid
                    ]);
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

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $murid = Murid::where('muridid', $id)
            ->where('dlt', 0)
            ->first();
            $murid->kodemurid = $request->kodemurid;
            $murid->namamurid = $request->namamurid;
            $murid->jeniskelamin = $request->jeniskelamin;
            $murid->alamat = $request->alamat;
            $murid->agama = $request->agama;
            $murid->tempatlahir = $request->tempatlahir;
            $murid->tgllahir = $request->tgllahir;
            $murid->notelp = $request->notelp;
            $murid->namasekolah = $request->namasekolah;
            $murid->namaortu = $request->namaortu;
            $murid->tglmasuk = $request->tglmasuk;
            $murid->emailortu = $request->emailortu;
            $murid->level = $request->level;
            $murid->status = $request->status;

            $murid->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $murid->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('murid.index')
                ->with('success', 'Data murid berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $user = auth('sanctum')->user();

        $murid = Murid::find($id);

        $murid->dlt = '1';

        $murid->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $murid->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Murid deleted successfully.',
        ], 200);
    }

    public function getkodemurid() 
    {
        $tglnow = date('Y');
        // Memecah string tahun menjadi dua bagian
        $part1 = substr($tglnow, 0, 2); // Mendapatkan dua karakter pertama
        $part2 = substr($tglnow, 2, 2); // Mendapatkan dua karakter terakhir

        // Menukarnya
        $reversed = $part2 . $part1;

        $data = DB::table('tbmurid')
        ->select(DB::raw('max(cast(tbmurid.kodemurid as bigint)) + 1 as kode'))
        ->where('tbmurid.dlt', '0')
        ->get();

        $kode = 1;
        if ($data[0]->kode != null) $kode = substr($data[0]->kode, 4);

        $nextno = '1';
        if (isset($kode)) {
            $nextno = $kode;
        }
        $nextno = str_pad($nextno, 5, '0', STR_PAD_LEFT);
        $nextno = $reversed.$nextno;

        return $nextno;
    }
}
