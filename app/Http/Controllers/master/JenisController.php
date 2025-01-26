<?php

namespace App\Http\Controllers\master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\master\Jenis\CreateRequest;
use App\Http\Requests\master\Jenis\UpdateRequest;
use App\Models\master\Jenis;

class JenisController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Jenis';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-21');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $jenis = [];
        $kel = [];

        if($request->ajax())
        {    
            $kelid = $request->kelid;
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);        
            try {
                $jenis = DB::table('tbmjen')
                        ->join('tbmkel', function($join)
                        {
                            $join->on('tbmkel.kelid', '=', 'tbmjen.kelid');
                            $join->on('tbmkel.dlt','=',DB::raw("'0'"));
                        })
                        ->join('tbmstruk', function($join)
                        {
                            $join->on('tbmstruk.strukid', '=', 'tbmkel.strukid');
                            $join->on('tbmstruk.dlt','=',DB::raw("'0'"));
                        })
                        ->select('tbmjen.*', DB::raw('tbmstruk.strukkode || tbmkel.kelkode as kelkode'), 'tbmkel.kelnama')
                        ->where('tbmjen.dlt', '0')
                        ->where(function($query) use ($kelid, $search)
                        {
                            if (!is_null($kelid) && $kelid!='') $query->where('tbmjen.kelid', $kelid);
                            if (!is_null($search) && $search!='') {
                                $query->where(DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmjen.jennama'), 'ilike', '%'.$search.'%');
                            }
                        })
                        ->orderBy(DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode'));

                $count = $jenis->count();
                $data = $jenis->skip($page)->take($perpage)->get();
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
            ], 'Jenis retrieved successfully.');  
        }

        $kel = DB::table('tbmkel')
        ->select('tbmkel.kelid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode as kelkode'), 'tbmkel.kelnama')
        ->join('tbmstruk', function($join)
            {
                $join->on('tbmstruk.strukid', '=', 'tbmkel.strukid');
                $join->on('tbmstruk.dlt','=',DB::raw("'0'"));
            })
        ->where('tbmkel.dlt', 0)
        ->orderBy('tbmkel.kelkode')
        ->get();

        return view('master.jenis.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('jenis.create'), 'jenis' => $jenis, 'kel' => $kel]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-21');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
        $jenkode = "";
        $kel = DB::table('tbmkel')
        ->select('tbmkel.kelid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode as kelkode'), 'tbmkel.kelnama')
        ->join('tbmstruk', function($join)
            {
                $join->on('tbmstruk.strukid', '=', 'tbmkel.strukid');
                $join->on('tbmstruk.dlt','=',DB::raw("'0'"));
            })
        ->where('tbmkel.dlt', 0)
        ->orderBy('tbmkel.kelkode')
        ->get();
        
        return view('master.jenis.create', [
            'page' => $this->page, 
            'child' => 'Tambah Data', 
            'masterurl' => route('jenis.index'), 
            'kel' => $kel,
            'jenkode' => $jenkode
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
        $this->authorize('add-21');

        try
        {
            $user = auth('sanctum')->user();
            $model = new Jenis();

            DB::beginTransaction();

            $model->kelid = $request->kelid;
            $model->jenkode = $request->jenkode;
            $model->jennama = $request->jennama;
            $model->dasarhukum = isset($request->dasarhukum) ? $request->dasarhukum : null;
            $tahun = Date('Y');
            $model->tahun = $tahun;
            
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('jenis.index')
            ->with('success', 'Data jenis berhasil ditambah.', ['page' => $this->page]);
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
        $this->authorize('view-21');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataJenis = Jenis::where('jenid', $id)->firstOrFail();

        $kel = DB::table('tbmkel')
        ->select('tbmkel.kelid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode as kelkode'), 'tbmkel.kelnama')
        ->join('tbmstruk', function($join)
            {
                $join->on('tbmstruk.strukid', '=', 'tbmkel.strukid');
                $join->on('tbmstruk.dlt','=',DB::raw("'0'"));
            })
        ->where('tbmkel.dlt', 0)
        ->orderBy('tbmkel.kelkode')
        ->get();

        return view(
            'master.jenis.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('jenis.index'), 
                'jenis' => $dataJenis, 
                'kel' => $kel
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

        $this->authorize('edit-21');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $dataJenis = Jenis::where('jenid', $id)->firstOrFail();

        $kel = DB::table('tbmkel')
        ->select('tbmkel.kelid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode as kelkode'), 'tbmkel.kelnama')
        ->join('tbmstruk', function($join)
            {
                $join->on('tbmstruk.strukid', '=', 'tbmkel.strukid');
                $join->on('tbmstruk.dlt','=',DB::raw("'0'"));
            })
        ->where('tbmkel.dlt', 0)
        ->orderBy('tbmkel.kelkode')
        ->get();

        return view(
                    'master.jenis.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('jenis.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('jenis.index'), 
                        'jenis' => $dataJenis,
                        'kel' => $kel
                    ]);
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
        $this->authorize('edit-21');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $jenis = Jenis::where('jenid', $id)
            ->where('dlt', 0)
            ->first();
            $jenis->jenkode = $request->jenkode;
            $jenis->jennama = $request->jennama;
            $jenis->dasarhukum = isset($request->dasarhukum) ? $request->dasarhukum : null;
            $jenis->kelid = $request->kelid;
            $tahun = Date('Y');
            $jenis->tahun = $tahun;

            $jenis->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $jenis->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('jenis.index')
                ->with('success', 'Data jenis berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-21');

        $user = auth('sanctum')->user();

        $jenis = Jenis::find($id);

        $jenis->dlt = '1';

        $jenis->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $jenis->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Jenis deleted successfully.',
        ], 200);
    }

    public function getnextno($id)
    {
        $data = DB::table('tbmjen')
        ->select(DB::raw('max(cast(replace(tbmjen.jenkode, \'.\', \'\') as bigint)) + 1 as kode'))
        ->where('tbmjen.dlt', '0')
        ->where('tbmjen.kelid', $id)
        ->get();
        
        $kode = 1;
        if ($data[0]->kode != null) $kode = $data[0]->kode;

        $nextno = '1';
        if (isset($kode)) {
            $nextno = $kode;
        }
        $nextno = str_pad($nextno, 2, '0', STR_PAD_LEFT);
        $nextno = $nextno . '.';

        return $nextno;
    }
}
