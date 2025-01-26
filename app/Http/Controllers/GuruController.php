<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Guru;

class GuruController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Guru';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-5');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $guru = [];

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
                $guru = DB::table('tbguru')
                        // ->leftJoin('tbmsekolah', function($join)
                        // {
                        //     $join->on('tbmsekolah.sekolahid', '=', 'tbmguru.sekolahid');
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
                        ->select('tbguru.*')
                        ->where('tbguru.dlt', '0')
                        ->where(function ($query) use ($level, $status, $search) {
                                if (!is_null($level) && $level != '') $query->where('tbguru.level', $level);
                                if (!is_null($status) && $status != '') $query->where('tbguru.status', $status);

                                if (!is_null($search) && $search!='') {
                                    // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                                    $query->where(DB::raw('tbguru.kodeguru'), 'ilike', '%'.$search.'%');
                                    $query->orWhere(DB::raw('tbguru.namaguru'), 'ilike', '%'.$search.'%');
                                    // $query->where('tbguru.sekolahid', $sekolahid);
                                }
                                // if(Auth::user()->isSekolah()) $query->where('tbguru.sekolahid', auth('sanctum')->user()->sekolahid);
                        });

                $count = $guru->count();
                $data = $guru->skip($page)->take($perpage)->get();
            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'guru retrieved successfully.');  
        }
        return view(
            'guru.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('guru.create'), 
                'guru' => $guru, 
            ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $this->authorize('add-5');
        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        return view(
            'guru.create', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('guru.index'), 
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
        // $this->authorize('add-25');

        try{
            $user = auth('sanctum')->user();

            $model = new Guru();

            DB::beginTransaction();
            $tahun = Date('Y');
            $model->kodeguru = $request->kodeguru;
            $model->tgllahir = $request->tgllahir;
            $model->namaguru = $request->namaguru;
            $model->namapanggilan = $request->namapanggilan;
            $model->jeniskelamin = $request->jeniskelamin;
            $model->alamat = $request->alamat;
            $model->tempatlahir = $request->tempatlahir;
            $model->agama = $request->agama;
            $model->tgllahir = $request->tgllahir;
            $model->notelp = $request->notelp;
            $model->pendidikan = $request->pendidikan;
            $model->email = $request->email;
            $model->level = $request->level;
            $model->status = $request->status;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil ditambah.', ['page' => $this->page]);
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
        // $this->authorize('edit-25');

        Log::channel('disnaker')->info('Halaman Ubah '.$this->page);

        $guru = Guru::where('guruid', $id)->firstOrFail();

        return view('guru.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('guru.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('guru.index'), 
                        'guru' => $guru
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
        // $this->authorize('add-25');

        try{
            $user = auth('sanctum')->user();

            $model = Guru::where('guruid', $id)
            ->where('dlt', 0)
            ->first();

            DB::beginTransaction();
            $tahun = Date('Y');
            $model->kodeguru = $request->kodeguru;
            $model->tgllahir = $request->tgllahir;
            $model->namaguru = $request->namaguru;
            $model->namapanggilan = $request->namapanggilan;
            $model->jeniskelamin = $request->jeniskelamin;
            $model->alamat = $request->alamat;
            $model->tempatlahir = $request->tempatlahir;
            $model->agama = $request->agama;
            $model->tgllahir = $request->tgllahir;
            $model->notelp = $request->notelp;
            $model->pendidikan = $request->pendidikan;
            $model->email = $request->email;
            $model->level = $request->level;
            $model->status = $request->status;

            $model->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil diubah.', ['page' => $this->page]);
        }catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // $this->authorize('delete-23');

        $user = auth('sanctum')->user();

        $guru = Guru::find($id);

        $guru->dlt = '1';

        $guru->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $guru->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'guru deleted successfully.',
        ], 200);
    }

    public function getkodeguru() 
    {
        $tglnow = date('Y');
        // Memecah string tahun menjadi dua bagian
        $part1 = substr($tglnow, 0, 2); // Mendapatkan dua karakter pertama
        $part2 = substr($tglnow, 2, 2); // Mendapatkan dua karakter terakhir

        // Menukarnya
        $reversed = $part2 . $part1;

        $data = DB::table('tbguru')
        ->select(DB::raw('max(cast(tbguru.kodeguru as bigint)) + 1 as kode'))
        ->where('tbguru.dlt', '0')
        ->get();

        $kode = 1;
        if ($data[0]->kode != null) $kode = substr($data[0]->kode, 5);

        $nextno = '1';
        if (isset($kode)) {
            $nextno = $kode;
        }
        $nextno = str_pad($nextno, 5, '0', STR_PAD_LEFT);
        $nextno = $reversed.'1'.$nextno;

        return $nextno;
    }
}
