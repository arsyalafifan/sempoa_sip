<?php

namespace App\Http\Controllers\master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\master\Obyek\CreateRequest;
use App\Http\Requests\master\Obyek\UpdateRequest;
use App\Models\master\Obyek;

class ObyekController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Obyek';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-22');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $obyek = [];
        $jen = [];

        if($request->ajax())
        {    
            $jenid = $request->jenid;
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);        
            try {
                $obyek = DB::table('tbmoby')
                        ->join('tbmjen', function($join)
                        {
                            $join->on('tbmjen.jenid', '=', 'tbmoby.jenid');
                            $join->on('tbmjen.dlt','=',DB::raw("'0'"));
                        })
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
                        ->select('tbmoby.*', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode as jenkode'), 'tbmjen.jennama')
                        ->where('tbmoby.dlt', '0')
                        ->where(function($query) use ($jenid, $search)
                        {
                            if (!is_null($jenid) && $jenid!='') $query->where('tbmoby.jenid', $jenid);
                            if (!is_null($search) && $search!='') {
                                $query->where(DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmoby.obynama'), 'ilike', '%'.$search.'%');
                            }
                        })
                        ->orderBy(DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode'));

                $count = $obyek->count();
                $data = $obyek->skip($page)->take($perpage)->get();
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
            ], 'Obyek retrieved successfully.');  
        }

        $jen = DB::table('tbmjen')
        ->select('tbmjen.jenid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode as jenkode'), 'tbmjen.jennama')
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
        ->where('tbmjen.dlt', 0)
        ->orderBy('tbmjen.jenkode')
        ->get();

        return view('master.obyek.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('obyek.create'), 'obyek' => $obyek, 'jen' => $jen]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-22');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
        $obykode = "";
        $jen = DB::table('tbmjen')
        ->select('tbmjen.jenid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode as jenkode'), 'tbmjen.jennama')
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
        ->where('tbmjen.dlt', 0)
        ->orderBy('tbmjen.jenkode')
        ->get();
        
        return view('master.obyek.create', [
            'page' => $this->page, 
            'child' => 'Tambah Data', 
            'masterurl' => route('obyek.index'), 
            'jen' => $jen,
            'obykode' => $obykode
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
        $this->authorize('add-22');

        try
        {
            $user = auth('sanctum')->user();
            $model = new Obyek();

            DB::beginTransaction();

            $model->jenid = $request->jenid;
            $model->obykode = $request->obykode;
            $model->obynama = $request->obynama;
            $tahun = Date('Y');
            $model->tahun = $tahun;
            
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('obyek.index')
            ->with('success', 'Data obyek berhasil ditambah.', ['page' => $this->page]);
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
        $this->authorize('view-22');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataObyek = Obyek::where('obyid', $id)->firstOrFail();

        $jen = DB::table('tbmjen')
        ->select('tbmjen.jenid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode as jenkode'), 'tbmjen.jennama')
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
        ->where('tbmjen.dlt', 0)
        ->orderBy('tbmjen.jenkode')
        ->get();

        return view(
            'master.obyek.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('obyek.index'), 
                'obyek' => $dataObyek, 
                'jen' => $jen
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

        $this->authorize('edit-22');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $dataObyek = Obyek::where('obyid', $id)->firstOrFail();

        $jen = DB::table('tbmjen')
        ->select('tbmjen.jenid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode as jenkode'), 'tbmjen.jennama')
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
        ->where('tbmjen.dlt', 0)
        ->orderBy('tbmjen.jenkode')
        ->get();

        return view(
                    'master.obyek.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('obyek.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('obyek.index'), 
                        'obyek' => $dataObyek,
                        'jen' => $jen
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
        $this->authorize('edit-22');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $obyek = Obyek::where('obyid', $id)
            ->where('dlt', 0)
            ->first();
            $obyek->obykode = $request->obykode;
            $obyek->obynama = $request->obynama;
            $obyek->jenid = $request->jenid;
            $tahun = Date('Y');
            $obyek->tahun = $tahun;

            $obyek->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $obyek->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('obyek.index')
                ->with('success', 'Data obyek berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-22');

        $user = auth('sanctum')->user();

        $obyek = Obyek::find($id);

        $obyek->dlt = '1';

        $obyek->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $obyek->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Obyek deleted successfully.',
        ], 200);
    }

    public function getnextno($id)
    {
        $data = DB::table('tbmoby')
        ->select(DB::raw('max(cast(replace(tbmoby.obykode, \'.\', \'\') as bigint)) + 1 as kode'))
        ->where('tbmoby.dlt', '0')
        ->where('tbmoby.jenid', $id)
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
