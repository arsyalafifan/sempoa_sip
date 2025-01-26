<?php

namespace App\Http\Controllers\master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\master\Kegiatan\CreateRequest;
use App\Http\Requests\master\Kegiatan\UpdateRequest;
use App\Models\master\Kegiatan;

class KegiatanController extends BaseController
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Kegiatan';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-10');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $kegiatan = [];
        $program = [];

        if($request->ajax())
        {    
            $progid = $request->progid;
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);        
            try {
                $kegiatan = DB::table('tbmkeg')
                        ->join('tbmprog', function($join)
                        {
                            $join->on('tbmprog.progid', '=', 'tbmkeg.progid');
                            $join->on('tbmprog.dlt','=',DB::raw("'0'"));
                        })
                        ->select('tbmkeg.*', 'tbmprog.progkode', 'tbmprog.prognama')
                        ->where('tbmkeg.dlt', '0')
                        ->where(function($query) use ($progid, $search)
                        {
                            if (!is_null($progid) && $progid!='') $query->where('tbmkeg.progid', $progid);
                            if (!is_null($search) && $search!='') {
                                $query->where(DB::raw('tbmprog.progkode || tbmkeg.kegkode'), 'ilike', '%'.$search.'%');
                                $query->orWhere(DB::raw('tbmkeg.kegnama'), 'ilike', '%'.$search.'%');
                            }
                        })
                        ->orderBy('tbmkeg.kegkode');

                $count = $kegiatan->count();
                $data = $kegiatan->skip($page)->take($perpage)->get();
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
            ], 'Kegiatan retrieved successfully.');  
        }

        $program = DB::table('tbmprog')
        ->select('tbmprog.progid', 'tbmprog.progkode', 'tbmprog.prognama')
        ->where('tbmprog.dlt', 0)
        ->orderBy('tbmprog.progkode')
        ->get();

        return view('master.kegiatan.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('kegiatan.create'), 'kegiatan' => $kegiatan, 'program' => $program]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-10');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
        $kegkode = "";
        $program = DB::table('tbmprog')->select('tbmprog.progid', 'tbmprog.progkode', 'tbmprog.prognama')
        ->where('tbmprog.dlt', 0)
        ->orderBy('tbmprog.progkode')
        ->get();
        
        return view('master.kegiatan.create', [
            'page' => $this->page, 
            'child' => 'Tambah Data', 
            'masterurl' => route('kegiatan.index'), 
            'program' => $program,
            'kegkode' => $kegkode
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
        $this->authorize('add-10');

        try
        {
            $user = auth('sanctum')->user();
            $model = new Kegiatan();

            DB::beginTransaction();

            $model->progid = $request->progid;
            $model->kegkode = $request->kegkode;
            $model->kegnama = $request->kegnama;
            $tahun = Date('Y');
            $model->tahun = $tahun;
            
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('kegiatan.index')
            ->with('success', 'Data kegiatan berhasil ditambah.', ['page' => $this->page]);
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
        $this->authorize('view-10');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataKegiatan = Kegiatan::where('kegid', $id)->firstOrFail();

        $program = DB::table('tbmprog')
        ->select('tbmprog.progid', 'tbmprog.progkode', 'tbmprog.prognama')
        ->where('tbmprog.dlt', 0)
        ->orderBy('tbmprog.progkode')
        ->get();

        return view(
            'master.kegiatan.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('kegiatan.index'), 
                'kegiatan' => $dataKegiatan, 
                'program' => $program
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

        $this->authorize('edit-10');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $dataKegiatan = Kegiatan::where('kegid', $id)->firstOrFail();

        $program = DB::table('tbmprog')
        ->select('tbmprog.progid', 'tbmprog.progkode', 'tbmprog.prognama')
        ->where('tbmprog.dlt', 0)
        ->orderBy('tbmprog.progkode')
        ->get();

        return view(
                    'master.kegiatan.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('kegiatan.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('kegiatan.index'), 
                        'kegiatan' => $dataKegiatan,
                        'program' => $program
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
        $this->authorize('edit-10');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $kegiatan = Kegiatan::where('kegid', $id)
            ->where('dlt', 0)
            ->first();
            $kegiatan->kegkode = $request->kegkode;
            $kegiatan->kegnama = $request->kegnama;
            $kegiatan->progid = $request->progid;
            $tahun = Date('Y');
            $kegiatan->tahun = $tahun;

            $kegiatan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $kegiatan->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('kegiatan.index')
                ->with('success', 'Data kegiatan berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-10');

        $user = auth('sanctum')->user();

        $kegiatan = Kegiatan::find($id);

        $kegiatan->dlt = '1';

        $kegiatan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $kegiatan->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Kegiatan deleted successfully.',
        ], 200);
    }

    public function getnextno($id)
    {
        $data = DB::table('tbmkeg')
        ->select(DB::raw('max(cast(replace(tbmkeg.kegkode, \'.\', \'\') as int)) + 1 as kode'))
        ->where('tbmkeg.dlt', '0')
        ->where('tbmkeg.progid', $id)
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
