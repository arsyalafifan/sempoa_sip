<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master\Program\CreateRequest;
use App\Http\Requests\master\Program\UpdateRequest;
use App\Models\master\Program;

class ProgramController extends BaseController
{

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Program';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-9');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $prog = [];
        // $prov = [];

        if($request->ajax())
        {    
            // $provid = $request->provid;
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);        
            try {

                $prog = DB::table('tbmprog')
                ->select('tbmprog.*')
                ->where('tbmprog.dlt', '0')
                ->where(function($query) use ($search)
                {
                    if (!is_null($search) && $search!='') {
                        $query->where(DB::raw('tbmprog.progkode'), 'ilike', '%'.$search.'%');
                        $query->orWhere(DB::raw('tbmprog.prognama'), 'ilike', '%'.$search.'%');
                    }
                })
                ->orderBy('tbmprog.progkode');

                $count = $prog->count();
                $data = $prog->skip($page)->take($perpage)->get();
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
            ], 'Program retrieved successfully.');  
        }

        return view('master.program.index', ['page' => $this->page, 'createbutton' => true, 'createurl' => route('program.create'), 'program' => $prog]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-9');

        $progkode = $this->getnextno();

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);

        return view('master.program.create', 
                    [
                        'page' => $this->page, 'child' => 'Tambah Data', 'masterurl' => route('program.index'), 'progkode' => $progkode
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

        $this->authorize('add-9');

        try{
            $user = auth('sanctum')->user();

            $model = new Program;

            DB::beginTransaction();
            $tahun = Date('Y');
            $model->progkode = $request->progkode;
            $model->prognama = $request->prognama;
            $model->tahun = $tahun;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('program.index')
            ->with('success', 'Data program berhasil ditambah.', ['page' => $this->page]);
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
    public function show(Program $prog, $id)
    {
        $this->authorize('view-9');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataProgram = $prog->where('progid', $id)->firstOrFail();

        return view('master.program.show', ['page' => $this->page, 'child' => 'Lihat Data', 'masterurl' => route('program.index'), 'program' => $dataProgram]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Program $ProgramModel, $id)
    {
        $this->authorize('edit-9');

        Log::channel('disnaker')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $prog = $ProgramModel->where('progid', $id)->firstOrFail();

        return view('master.program.edit', 
                    [
                        'page' => $this->page, 'createbutton' => true, 'createurl' => route('program.create'), 'child' => 'Ubah Data', 'masterurl' => route('program.index'), 'program' => $prog
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
        $this->authorize('edit-9');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $prog = Program::where('progid', $id)
            ->where('dlt', 0)
            ->first();
            $prog->progkode = $request->progkode;
            $prog->prognama = $request->prognama;
            $tahun = Date('Y');
            $prog->tahun = $tahun;

            $prog->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $prog->save();

            DB::commit();
        }catch(QueryException $e){
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('program.index')
                ->with('success', 'Data berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-9');

        $user = auth('sanctum')->user();

        $prog = Program::find($id);

        $prog->dlt = '1';

        $prog->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $prog->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Program deleted successfully.',
        ], 200);

    }

    public function getnextno()
    {
        $data = DB::table('tbmprog')
        ->select(DB::raw('max(cast(replace(tbmprog.progkode, \'.\', \'\') as int)) + 1 as kode'))
        ->where('tbmprog.dlt', '0')
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
