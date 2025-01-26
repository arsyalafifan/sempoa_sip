<?php

namespace App\Http\Controllers\master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\master\Kelompok\CreateRequest;
use App\Http\Requests\master\Kelompok\UpdateRequest;
use App\Models\master\Kelompok;

class KelompokController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Kelompok';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-26');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $kelompok = [];
        $struk = [];

        if($request->ajax())
        {    
            $strukid = $request->strukid;
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);        
            try {
                $kelompok = DB::table('tbmkel')
                        ->join('tbmstruk', function($join)
                        {
                            $join->on('tbmstruk.strukid', '=', 'tbmkel.strukid');
                            $join->on('tbmstruk.dlt','=',DB::raw("'0'"));
                        })
                        ->select('tbmkel.*', 'tbmstruk.strukkode', 'tbmstruk.struknama')
                        ->where('tbmkel.dlt', '0')
                        ->where(function($query) use ($strukid, $search)
                        {
                            if (!is_null($strukid) && $strukid!='') $query->where('tbmkel.strukid', $strukid);
                            if (!is_null($search) && $search!='') {
                                $query->where(DB::raw('tbmstruk.strukkode || tbmkel.kelkode'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmkel.kelnama'), 'ilike', '%'.$search.'%');
                            }
                        })
                        ->orderBy(DB::raw('tbmstruk.strukkode || tbmkel.kelkode'));

                $count = $kelompok->count();
                $data = $kelompok->skip($page)->take($perpage)->get();
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
            ], 'Kelompok retrieved successfully.');  
        }

        $struk = DB::table('tbmstruk')
        ->select('tbmstruk.strukid', 'tbmstruk.strukkode', 'tbmstruk.struknama')
        ->where('tbmstruk.dlt', 0)
        ->orderBy('tbmstruk.strukkode')
        ->get();

        return view('master.kelompok.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('kelompok.create'), 'kelompok' => $kelompok, 'struk' => $struk]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-26');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
        $kelkode = "";
        $struk = DB::table('tbmstruk')->select('tbmstruk.strukid', 'tbmstruk.strukkode', 'tbmstruk.struknama')
        ->where('tbmstruk.dlt', 0)
        ->orderBy('tbmstruk.strukkode')
        ->get();
        
        return view('master.kelompok.create', [
            'page' => $this->page, 
            'child' => 'Tambah Data', 
            'masterurl' => route('kelompok.index'), 
            'struk' => $struk,
            'kelkode' => $kelkode
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
        $this->authorize('add-26');

        try
        {
            $user = auth('sanctum')->user();
            $model = new Kelompok();

            DB::beginTransaction();

            $model->strukid = $request->strukid;
            $model->kelkode = $request->kelkode;
            $model->kelnama = $request->kelnama;
            $tahun = Date('Y');
            $model->tahun = $tahun;
            
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('kelompok.index')
            ->with('success', 'Data kelompok berhasil ditambah.', ['page' => $this->page]);
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
        $this->authorize('view-26');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataKelompok = Kelompok::where('kelid', $id)->firstOrFail();

        $struk = DB::table('tbmstruk')
        ->select('tbmstruk.strukid', 'tbmstruk.strukkode', 'tbmstruk.struknama')
        ->where('tbmstruk.dlt', 0)
        ->orderBy('tbmstruk.strukkode')
        ->get();

        return view(
            'master.kelompok.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('kelompok.index'), 
                'kelompok' => $dataKelompok, 
                'struk' => $struk
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

        $this->authorize('edit-26');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $dataKelompok = Kelompok::where('kelid', $id)->firstOrFail();

        $struk = DB::table('tbmstruk')
        ->select('tbmstruk.strukid', 'tbmstruk.strukkode', 'tbmstruk.struknama')
        ->where('tbmstruk.dlt', 0)
        ->orderBy('tbmstruk.strukkode')
        ->get();

        return view(
                    'master.kelompok.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('kelompok.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('kelompok.index'), 
                        'kelompok' => $dataKelompok,
                        'struk' => $struk
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
        $this->authorize('edit-26');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $kelompok = Kelompok::where('kelid', $id)
            ->where('dlt', 0)
            ->first();
            $kelompok->kelkode = $request->kelkode;
            $kelompok->kelnama = $request->kelnama;
            $kelompok->strukid = $request->strukid;
            $tahun = Date('Y');
            $kelompok->tahun = $tahun;

            $kelompok->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $kelompok->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('kelompok.index')
                ->with('success', 'Data kelompok berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-26');

        $user = auth('sanctum')->user();

        $kelompok = Kelompok::find($id);

        $kelompok->dlt = '1';

        $kelompok->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $kelompok->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Kelompok deleted successfully.',
        ], 200);
    }

    public function getnextno($id)
    {
        $data = DB::table('tbmkel')
        ->select(DB::raw('max(cast(replace(tbmkel.kelkode, \'.\', \'\') as bigint)) + 1 as kode'))
        ->where('tbmkel.dlt', '0')
        ->where('tbmkel.strukid', $id)
        ->get();
        
        $kode = 1;
        if ($data[0]->kode != null) $kode = $data[0]->kode;

        $nextno = '1';
        if (isset($kode)) {
            $nextno = $kode;
        }
        $nextno = str_pad($nextno, 1, '0', STR_PAD_LEFT);
        $nextno = $nextno . '.';

        return $nextno;
    }
}
