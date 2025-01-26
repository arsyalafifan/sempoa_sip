<?php

namespace App\Http\Controllers\master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\master\Subrincianobyek\CreateRequest;
use App\Http\Requests\master\Subrincianobyek\UpdateRequest;
use App\Models\master\SubRincianObyek;

class SubrincianobyekController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Sub Rincian Obyek';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-24');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $sroby = [];
        $roby = [];

        if($request->ajax())
        {    
            $robyid = $request->robyid;
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);        
            try {
                $sroby = DB::table('tbmsroby')
                        ->join('tbmroby', function($join)
                        {
                            $join->on('tbmroby.robyid', '=', 'tbmsroby.robyid');
                            $join->on('tbmroby.dlt','=',DB::raw("'0'"));
                        })
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
                        ->select('tbmsroby.*', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode || tbmroby.robykode as robykode'), 'tbmroby.robynama')
                        ->where('tbmsroby.dlt', '0')
                        ->where(function($query) use ($robyid, $search)
                        {
                            if (!is_null($robyid) && $robyid!='') $query->where('tbmroby.robyid', $robyid);
                            if (!is_null($search) && $search!='') {
                                $query->where(DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode || tbmroby.robykode || tbmsroby.srobykode'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmsroby.srobynama'), 'ilike', '%'.$search.'%');
                            }
                        })
                        ->orderBy(DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode || tbmroby.robykode || tbmsroby.srobykode'));
                $count = $sroby->count();
                $data = $sroby->skip($page)->take($perpage)->get();
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
            ], 'Sub Rincian Obyek retrieved successfully.');  
        }

        $roby = DB::table('tbmroby')
        ->select('tbmroby.robyid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode || tbmroby.robykode as robykode'), 'tbmroby.robynama')
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
        ->where('tbmroby.dlt', 0)
        ->orderBy('tbmroby.robykode')
        ->get();

        return view('master.subrincianobyek.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('subrincianobyek.create'), 'sroby' => $sroby, 'roby' => $roby]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-24');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
        $srobykode = "";
        $roby = DB::table('tbmroby')
        ->select('tbmroby.robyid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode || tbmroby.robykode as robykode'), 'tbmroby.robynama')
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
        ->where('tbmroby.dlt', 0)
        ->orderBy('tbmroby.robykode')
        ->get();
        
        return view('master.subrincianobyek.create', [
            'page' => $this->page, 
            'child' => 'Tambah Data', 
            'masterurl' => route('subrincianobyek.index'), 
            'roby' => $roby,
            'srobykode' => $srobykode
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
        $this->authorize('add-24');

        try
        {
            $user = auth('sanctum')->user();
            $model = new SubRincianObyek();

            DB::beginTransaction();

            $model->robyid = $request->robyid;
            $model->srobykode = $request->srobykode;
            $model->srobynama = $request->srobynama;
            $model->isskpd = isset($request->isskpd) ? 1 : 0;
            //jika isssh = 0 maka tanpa ssh
            //jika isssh = 1 maka menggunakan ssh
            $model->isssh = isset($request->isssh) ? 0 : 1;
            
            $tahun = Date('Y');
            $model->tahun = $tahun;
            
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('subrincianobyek.index')
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
        $this->authorize('view-24');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataSubRincianObyek = SubRincianObyek::where('srobyid', $id)->firstOrFail();

        $roby = DB::table('tbmroby')
        ->select('tbmroby.robyid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode || tbmroby.robykode as robykode'), 'tbmroby.robynama')
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
        ->where('tbmroby.dlt', 0)
        ->orderBy('tbmroby.robykode')
        ->get();

        return view(
            'master.subrincianobyek.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('subrincianobyek.index'), 
                'sroby' => $dataSubRincianObyek, 
                'roby' => $roby
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

        $this->authorize('edit-24');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $dataSubRincianObyek = SubRincianObyek::where('srobyid', $id)->firstOrFail();

        $roby = DB::table('tbmroby')
        ->select('tbmroby.robyid', DB::raw('tbmstruk.strukkode || tbmkel.kelkode || tbmjen.jenkode || tbmoby.obykode || tbmroby.robykode as robykode'), 'tbmroby.robynama')
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
        ->where('tbmroby.dlt', 0)
        ->orderBy('tbmroby.robykode')
        ->get();

        return view(
                    'master.subrincianobyek.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('subrincianobyek.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('subrincianobyek.index'), 
                        'sroby' => $dataSubRincianObyek,
                        'roby' => $roby
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
        $this->authorize('edit-24');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $sroby = SubRincianObyek::where('srobyid', $id)
            ->where('dlt', 0)
            ->first();
            $sroby->srobykode = $request->srobykode;
            $sroby->srobynama = $request->srobynama;
            $sroby->robyid = $request->robyid;
            $sroby->isskpd = $request->isskpd == 1 ? true : false;
            //jika isssh = 0 maka tanpa ssh
            //jika isssh = 1 maka menggunakan ssh
            $sroby->isssh = $request->isssh == 1 ? false : true;

            $tahun = Date('Y');
            $sroby->tahun = $tahun;

            $sroby->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $sroby->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('subrincianobyek.index')
                ->with('success', 'Data sub rincian obyek berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-24');

        $user = auth('sanctum')->user();

        $sroby = SubRincianObyek::find($id);

        $sroby->dlt = '1';

        $sroby->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $sroby->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Sub Rincian Obyek deleted successfully.',
        ], 200);
    }

    public function getnextno($id)
    {
        $data = DB::table('tbmsroby')
        ->select(DB::raw('max(cast(replace(tbmsroby.srobykode, \'.\', \'\') as bigint)) + 1 as kode'))
        ->where('tbmsroby.dlt', '0')
        ->where('tbmsroby.robyid', $id)
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
