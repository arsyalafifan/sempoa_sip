<?php

namespace App\Http\Controllers\master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\master\Rincianobyek\CreateRequest;
use App\Http\Requests\master\Rincianobyek\UpdateRequest;
use App\Models\master\RincianObyek;

class RincianobyekController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Rincian Obyek';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-23');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $roby = [];
        $oby = [];

        if($request->ajax())
        {    
            $obyid = $request->obyid;
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);        
            try {
                $roby = DB::table('tbmroby')
                        ->join('tbmoby', function($join)
                        {
                            $join->on('tbmoby.obyid', '=', 'tbmroby.obyid');
                            $join->on('tbmoby.dlt','=',DB::raw("'0'"));
                        })
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
                        ->select('tbmroby.*', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode as obykode'), 'tbmoby.obynama')
                        ->where('tbmroby.dlt', '0')
                        ->where(function($query) use ($obyid, $search)
                        {
                            if (!is_null($obyid) && $obyid!='') $query->where('tbmroby.obyid', $obyid);
                            if (!is_null($search) && $search!='') {
                                $query->where(DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode || tbmroby.robykode'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmroby.robynama'), 'ilike', '%'.$search.'%');
                            }
                        })
                        ->orderBy(DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode || tbmroby.robykode'));

                $count = $roby->count();
                $data = $roby->skip($page)->take($perpage)->get();
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
            ], 'Rincian Obyek retrieved successfully.');  
        }

        $oby = DB::table('tbmoby')
        ->select('tbmoby.obyid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode as obykode'), 'tbmoby.obynama')
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
        ->where('tbmoby.dlt', 0)
        ->orderBy('tbmoby.obykode')
        ->get();

        return view('master.rincianobyek.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('rincianobyek.create'), 'roby' => $roby, 'oby' => $oby]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-23');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
        $robykode = "";
        $oby = DB::table('tbmoby')
        ->select('tbmoby.obyid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode as obykode'), 'tbmoby.obynama')
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
        ->where('tbmoby.dlt', 0)
        ->orderBy('tbmoby.obykode')
        ->get();
        
        return view('master.rincianobyek.create', [
            'page' => $this->page, 
            'child' => 'Tambah Data', 
            'masterurl' => route('rincianobyek.index'), 
            'oby' => $oby,
            'robykode' => $robykode
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
        $this->authorize('add-23');

        try
        {
            $user = auth('sanctum')->user();
            $model = new RincianObyek();

            DB::beginTransaction();

            $model->obyid = $request->obyid;
            $model->robykode = $request->robykode;
            $model->robynama = $request->robynama;
            $tahun = Date('Y');
            $model->tahun = $tahun;
            
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('rincianobyek.index')
            ->with('success', 'Data rincian obyek berhasil ditambah.', ['page' => $this->page]);
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
        $this->authorize('view-23');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataRincianObyek = RincianObyek::where('robyid', $id)->firstOrFail();

        $oby = DB::table('tbmoby')
        ->select('tbmoby.obyid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode as obykode'), 'tbmoby.obynama')
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
        ->where('tbmoby.dlt', 0)
        ->orderBy('tbmoby.obykode')
        ->get();

        return view(
            'master.rincianobyek.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('rincianobyek.index'), 
                'roby' => $dataRincianObyek, 
                'oby' => $oby
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

        $this->authorize('edit-23');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $dataRincianObyek = RincianObyek::where('robyid', $id)->firstOrFail();

        $oby = DB::table('tbmoby')
        ->select('tbmoby.obyid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode as obykode'), 'tbmoby.obynama')
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
        ->where('tbmoby.dlt', 0)
        ->orderBy('tbmoby.obykode')
        ->get();

        return view(
                    'master.rincianobyek.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('rincianobyek.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('rincianobyek.index'), 
                        'roby' => $dataRincianObyek,
                        'oby' => $oby
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
        $this->authorize('edit-23');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $roby = RincianObyek::where('robyid', $id)
            ->where('dlt', 0)
            ->first();
            $roby->robykode = $request->robykode;
            $roby->robynama = $request->robynama;
            $roby->obyid = $request->obyid;
            $tahun = Date('Y');
            $roby->tahun = $tahun;

            $roby->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $roby->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('rincianobyek.index')
                ->with('success', 'Data rincian obyek berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-23');

        $user = auth('sanctum')->user();

        $roby = RincianObyek::find($id);

        $roby->dlt = '1';

        $roby->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $roby->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Rincian Obyek deleted successfully.',
        ], 200);
    }

    public function getnextno($id)
    {
        $data = DB::table('tbmroby')
        ->select(DB::raw('max(cast(replace(tbmroby.robykode, \'.\', \'\') as bigint)) + 1 as kode'))
        ->where('tbmroby.dlt', '0')
        ->where('tbmroby.obyid', $id)
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
