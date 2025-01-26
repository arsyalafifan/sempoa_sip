<?php

namespace App\Http\Controllers\master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\master\Subkegiatan\CreateRequest;
use App\Http\Requests\master\Subkegiatan\UpdateRequest;
use App\Models\master\Subkegiatan;

class SubkegiatanController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Sub Kegiatan';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-11');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $subkeg = [];
        $kegiatan = [];

        if($request->ajax())
        {    
            $kegid = $request->kegid;
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);        
            try {
                $subkeg = DB::table('tbmsubkeg')
                        ->join('tbmkeg', function($join)
                        {
                            $join->on('tbmkeg.kegid', '=', 'tbmsubkeg.kegid');
                            $join->on('tbmkeg.dlt','=',DB::raw("'0'"));
                        })
                        ->join('tbmprog', function($join)
                        {
                            $join->on('tbmprog.progid', '=', 'tbmkeg.progid');
                            $join->on('tbmprog.dlt','=',DB::raw("'0'"));
                        })
                        ->select('tbmsubkeg.*', DB::raw('tbmprog.progkode || tbmkeg.kegkode as kegkode'), 'tbmkeg.kegnama')
                        ->where('tbmsubkeg.dlt', '0')
                        ->where(function($query) use ($kegid, $search)
                        {
                            if (!is_null($kegid) && $kegid!='') $query->where('tbmsubkeg.kegid', $kegid);
                            if (!is_null($search) && $search!='') {
                                $query->where(DB::raw('tbmprog.progkode || tbmkeg.kegkode || tbmsubkeg.subkegkode'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmsubkeg.subkegnama'), 'ilike', '%'.$search.'%');
                            }
                        })
                        ->orderBy('tbmsubkeg.subkegkode');

                $count = $subkeg->count();
                $data = $subkeg->skip($page)->take($perpage)->get();
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
            ], 'Sub Kegiatan retrieved successfully.');  
        }

        $kegiatan = DB::table('tbmkeg')
        ->select('tbmkeg.kegid', DB::raw('tbmprog.progkode || tbmkeg.kegkode as kegkode'), 'tbmkeg.kegnama')
        ->join('tbmprog', function($join)
            {
                $join->on('tbmprog.progid', '=', 'tbmkeg.progid');
                $join->on('tbmprog.dlt','=',DB::raw("'0'"));
            })
        ->where('tbmkeg.dlt', 0)
        ->orderBy(DB::raw('tbmprog.progkode || tbmkeg.kegkode'))
        ->get();

        return view('master.subkegiatan.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('subkegiatan.create'), 'subkegiatan' => $subkeg, 'kegiatan' => $kegiatan]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-11');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
        $subkegkode = "";
        $kegiatan = DB::table('tbmkeg')->select('tbmkeg.kegid', DB::raw('tbmprog.progkode || tbmkeg.kegkode as kegkode'), 'tbmkeg.kegnama')
        ->join('tbmprog', function($join)
            {
                $join->on('tbmprog.progid', '=', 'tbmkeg.progid');
                $join->on('tbmprog.dlt','=',DB::raw("'0'"));
            })
        ->where('tbmkeg.dlt', 0)
        ->orderBy(DB::raw('tbmprog.progkode || tbmkeg.kegkode'))
        ->get();
        
        return view('master.subkegiatan.create', [
            'page' => $this->page, 
            'child' => 'Tambah Data', 
            'masterurl' => route('subkegiatan.index'), 
            'kegiatan' => $kegiatan,
            'subkegkode' => $subkegkode
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
        $this->authorize('add-11');

        try
        {
            $user = auth('sanctum')->user();
            $model = new Subkegiatan();

            DB::beginTransaction();

            $model->kegid = $request->kegid;
            $model->subkegkode = $request->subkegkode;
            $model->subkegnama = $request->subkegnama;
            $tahun = Date('Y');
            $model->tahun = $tahun;
            
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('subkegiatan.index')
            ->with('success', 'Data Sub kegiatan berhasil ditambah.', ['page' => $this->page]);
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
        $this->authorize('view-11');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataSubKegiatan = Subkegiatan::where('subkegid', $id)->firstOrFail();

        $kegiatan = DB::table('tbmkeg')
        ->select('tbmkeg.kegid', DB::raw('tbmprog.progkode || tbmkeg.kegkode as kegkode'), 'tbmkeg.kegnama')
        ->join('tbmprog', function($join)
        {
            $join->on('tbmprog.progid', '=', 'tbmkeg.progid');
            $join->on('tbmprog.dlt','=',DB::raw("'0'"));
        })
        ->where('tbmkeg.dlt', 0)
        ->orderBy(DB::raw('tbmprog.progkode || tbmkeg.kegkode'))
        ->get();

        return view(
            'master.subkegiatan.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('subkegiatan.index'), 
                'subkegiatan' => $dataSubKegiatan, 
                'kegiatan' => $kegiatan
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

        $this->authorize('edit-11');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $dataSubKegiatan = Subkegiatan::where('subkegid', $id)->firstOrFail();

        $kegiatan = DB::table('tbmkeg')
        ->select('tbmkeg.kegid', DB::raw('tbmprog.progkode || tbmkeg.kegkode as kegkode'), 'tbmkeg.kegnama')
        ->join('tbmprog', function($join)
        {
            $join->on('tbmprog.progid', '=', 'tbmkeg.progid');
            $join->on('tbmprog.dlt','=',DB::raw("'0'"));
        })
        ->where('tbmkeg.dlt', 0)
        ->orderBy(DB::raw('tbmprog.progkode || tbmkeg.kegkode'))
        ->get();

        return view(
                    'master.subkegiatan.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('subkegiatan.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('subkegiatan.index'), 
                        'subkegiatan' => $dataSubKegiatan,
                        'kegiatan' => $kegiatan
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
        $this->authorize('edit-11');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $subkeg = Subkegiatan::where('subkegid', $id)
            ->where('dlt', 0)
            ->first();
            $subkeg->subkegkode = $request->subkegkode;
            $subkeg->subkegnama = $request->subkegnama;
            $subkeg->kegid = $request->kegid;
            $tahun = Date('Y');
            $subkeg->tahun = $tahun;

            $subkeg->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $subkeg->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('subkegiatan.index')
                ->with('success', 'Data Sub kegiatan berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-11');

        $user = auth('sanctum')->user();

        $subkeg = Subkegiatan::find($id);

        $subkeg->dlt = '1';

        $subkeg->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $subkeg->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Kegiatan deleted successfully.',
        ], 200);
    }

    public function getnextno($id)
    {
        $data = DB::table('tbmsubkeg')
        ->select(DB::raw('max(cast(replace(tbmsubkeg.subkegkode, \'.\', \'\') as int)) + 1 as kode'))
        ->where('tbmsubkeg.dlt', '0')
        ->where('tbmsubkeg.kegid', $id)
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
