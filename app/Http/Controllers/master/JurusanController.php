<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\master\Jurusan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JurusanController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Jurusan';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-10');

        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);  

            try {
                $jurusan = DB::table('tbmjurusan')
                    ->select('tbmjurusan.*')
                    ->where('tbmjurusan.dlt', '=', DB::raw("'0'"))
                    ->where(function($query) use ($search)
                    {
                        if (!is_null($search) && $search!='') {
                            $query->where(DB::raw('tbmjurusan.nojurusan || tbmjurusan.namajurusan'), 'ilike', '%'.$search.'%');
                        }
                    })->orderBy('tbmjurusan.jurusanid');

                $count = $jurusan->count();
                $data = $jurusan->skip($page)->take($perpage)->get();
                
            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'jurusan retrieved successfully.');
        }

        return view(
            'master.jurusan.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('jurusan.create'), 
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $this->authorize('add-10');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        
        return view('master.jurusan.create', [
            'page' => $this->page, 
            'child' => 'Tambah Data', 
            'masterurl' => route('jurusan.index'), 
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->authorize('add-10');

        try
        {
            $user = auth('sanctum')->user();
            $model = new Jurusan();

            DB::beginTransaction();

            $model->nojurusan = $request->nojurusan;
            $model->namajurusan = $request->namajurusan;
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('jurusan.index')
            ->with('success', 'Data jurusan berhasil ditambah.', ['page' => $this->page]);
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
        // $this->authorize('view-10');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $dataJurusan = Jurusan::where('jurusanid', $id)->firstOrFail();

        return view(
            'master.jurusan.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('jurusan.index'), 
                'jurusan' => $dataJurusan, 
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
         // $this->authorize('edit-10');

         Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

         $dataJurusan = Jurusan::where('jurusanid', $id)->firstOrFail();
 
         return view(
             'master.jurusan.edit', 
             [
                 'page' => $this->page, 
                 'createbutton' => false, 
                 'createurl' => route('jurusan.create'), 
                 'child' => 'Ubah Data', 
                 'masterurl' => route('jurusan.index'), 
                 'jurusan' => $dataJurusan,
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
         // $this->authorize('edit-10');

         $user = auth('sanctum')->user();

         try {
             DB::beginTransaction();
 
             $jurusan = Jurusan::where('jurusanid', $id)
             ->where('dlt', 0)
             ->first();
             $jurusan->nojurusan = $request->nojurusan;
             $jurusan->namajurusan = $request->namajurusan;
 
             $jurusan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
 
             $jurusan->save();
 
             DB::commit();
         }
         catch (QueryException $e) {
             return $this->sendError('SQL Error', $this->getQueryError($e));
         }
         catch (Exception $e) {
             return $this->sendError('Error', $e->getMessage());
         }
 
         return redirect()->route('jurusan.index')
                 ->with('success', 'Data jurusan berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // $this->authorize('delete-10');

        $user = auth('sanctum')->user();

        $jurusan = Jurusan::find($id);

        $jurusan->dlt = '1';

        $jurusan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $jurusan->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'jurusan deleted successfully.',
        ], 200);
    }

    public function getnextno()
    {
        $data = DB::table('tbmjurusan')
        ->select(DB::raw('max(cast(replace(tbmjurusan.nojurusan, \'.\', \'\') as int)) + 1 as kode'))
        ->where('tbmjurusan.dlt', '0')
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
