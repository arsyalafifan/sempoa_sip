<?php

namespace App\Http\Controllers\master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\master\Kecamatan\CreateRequest;
use App\Http\Requests\master\Kecamatan\UpdateRequest;
use App\Models\master\Kecamatan;

class KecamatanController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Kecamatan';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-2');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $kecamatan = [];
        $kota = [];

        if($request->ajax())
        {    
            $kotaid = $request->kotaid;
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);        
            try {
                $kecamatan = DB::table('tbmkecamatan')
                        ->join('tbmkota', function($join)
                        {
                            $join->on('tbmkota.kotaid', '=', 'tbmkecamatan.kotaid');
                            $join->on('tbmkota.dlt','=',DB::raw("'0'"));
                        })
                        ->select('tbmkecamatan.*', 'tbmkota.kodekota', 'tbmkota.namakota')
                        ->where('tbmkecamatan.dlt', '0')
                        ->where(function($query) use ($kotaid, $search)
                        {
                            if (!is_null($kotaid) && $kotaid!='') $query->where('tbmkecamatan.kotaid', $kotaid);
                            if (!is_null($search) && $search!='') {
                                // $query->where(DB::raw('CONCAT(tbmkecamatan.kodekec, tbmkecamatan.namakec)'), 'ilike', '%'.$search.'%');
                                $query->where(DB::raw('tbmkecamatan.kodekec'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmkecamatan.namakec'), 'ilike', '%'.$search.'%');
                            }
                        })
                        ->orderBy('tbmkecamatan.kodekec');

                $count = $kecamatan->count();
                $data = $kecamatan->skip($page)->take($perpage)->get();
            }
            catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'Kecamatan retrieved successfully.');  
        }

        $kota = DB::table('tbmkota')
        // ->join('tbmprov', function($join)
        // {
        //     $join->on('tbmprov.provid', '=', 'tbmkota.provid');
        //     $join->on('tbmprov.dlt','=',DB::raw("'0'"));
        // })
        ->select('tbmkota.kotaid', 'tbmkota.kodekota', 'tbmkota.namakota')
        ->where('tbmkota.dlt', 0)
        // ->orderBy('tbmprov.kodeprov')
        ->orderBy('tbmkota.kodekota')
        ->get();

        return view('master.kecamatan.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('kecamatan.create'), 'kecamatan' => $kecamatan, 'kota' => $kota]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-2');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
        $kodekec = $this->getnextno();
        $kota = DB::table('tbmkota')->select('tbmkota.kotaid', 'tbmkota.kodekota', 'tbmkota.namakota')
                                    ->where('tbmkota.dlt', 0)
                                    ->orderBy('tbmkota.kodekota')
                                    ->get();
        
        return view('master.kecamatan.create', [
                                                'page' => $this->page, 
                                                'child' => 'Tambah Data', 
                                                'masterurl' => route('kecamatan.index'), 
                                                'kota' => $kota,
                                                'kodekec' => $kodekec
                                            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $this->authorize('add-2');

        try
        {
            $user = auth('sanctum')->user();
            $model = new Kecamatan();

            DB::beginTransaction();

            $model->kotaid = $request->kotaid;
            $model->kodekec = $request->kodekec;
            $model->namakec = $request->namakec;
            
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('kecamatan.index')
            ->with('success', 'Data kecamatan berhasil ditambah.', ['page' => $this->page]);
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
        $this->authorize('view-2');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataKecamatan = Kecamatan::where('kecamatanid', $id)->firstOrFail();

        $kota = DB::table('tbmkota')
        ->select('tbmkota.kotaid', 'tbmkota.kodekota', 'tbmkota.namakota')
        ->where('tbmkota.dlt', 0)
        ->orderBy('tbmkota.kodekota')
        ->get();

        return view(
            'master.kecamatan.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('kecamatan.index'), 
                'kecamatan' => $dataKecamatan, 
                'kota' => $kota
            ]
        );

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $this->authorize('edit-2');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $dataKecamatan = Kecamatan::where('kecamatanid', $id)->firstOrFail();

        $kota = DB::table('tbmkota')
        ->select('tbmkota.kotaid', 'tbmkota.kodekota', 'tbmkota.namakota')
        ->where('tbmkota.dlt', 0)
        ->orderBy('tbmkota.kodekota')
        ->get();

        return view(
                    'master.kecamatan.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('kecamatan.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('kecamatan.index'), 
                        'kecamatan' => $dataKecamatan,
                        'kota' => $kota
                    ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Kecamatan $kecamatan)
    {
        $this->authorize('edit-2');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            // config('constants.grup.grup_superadmin')
            //$kecamatan = Kecamatan::find($id);
            $kecamatan->kodekec = $request->kodekec;
            $kecamatan->namakec = $request->namakec;
            $kecamatan->kotaid = $request->kotaid;

            $kecamatan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $kecamatan->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('kecamatan.index')
                ->with('success', 'Data kecamatan berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-2');

        $user = auth('sanctum')->user();

        $kecamatan = Kecamatan::find($id);

        // if (count($kota->kecamatan)>0) 
        //     return response([
        //         'success' => false,
        //         'data'    => 'Data tidak bisa dihapus karena sudah digunakan pada menu Kecamatan'
        //     ], 200);

        $kecamatan->dlt = '1';

        $kecamatan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $kecamatan->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Kecamatan deleted successfully.',
        ], 200);
    }

    public function getnextno()
    {
        $data = DB::table('tbmkecamatan')
        ->select(DB::raw('max(cast(replace(tbmkecamatan.kodekec, \'.\', \'\') as int)) + 1 as kode'))
        ->where('tbmkecamatan.dlt', '0')
        // ->where('tbmkota.provid', $parentid)
        ->get();
        
        $kode = 1;
        if ($data[0]->kode != null) $kode = $data[0]->kode;

        $nextno = '1';
        if (isset($kode)) {
            $nextno = $kode;
        }
        $nextno = str_pad($nextno, 4, '0', STR_PAD_LEFT);
        $nextno = $nextno . '.';

        return $nextno;
    }
}
