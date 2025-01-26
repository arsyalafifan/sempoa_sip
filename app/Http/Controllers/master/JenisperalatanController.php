<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\master\JenisPeralatan;

class JenisperalatanController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Jenis Peralatan';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-7');

        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50); 

            try {
                $jenisperalatan = DB::table('tbmjenisperalatan')
                    ->select('tbmjenisperalatan.jenisperalatanid', 'tbmjenisperalatan.nama')
                    ->where('tbmjenisperalatan.dlt', '0')
                    ->where(function($query) use ($search)
                    {
                        // if (!is_null($provid) && $provid!='') $query->where('tbmkota.provid', $provid);
                        if (!is_null($search) && $search!='') {
                            // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                            $query->where(DB::raw('tbmjenisperalatan.nama'), 'ilike', '%'.$search.'%');
                        }
                    })
                    ->orderBy('tbmjenisperalatan.jenisperalatanid')
                ;
                $count = $jenisperalatan->count();
                $data = $jenisperalatan->skip($page)->take($perpage)->get();


            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'jenis peralatan retrieved successfully.'); 
        }
        return view(
            'master.jenisperalatan.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('jenisperalatan.create'), 
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
        $this->authorize('add-7');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);

        return view(
            'master.jenisperalatan.create', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('jenisperalatan.index'), 
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add-7');

        try
        {
            $user = auth('sanctum')->user();
            $model = new JenisPeralatan;

            DB::beginTransaction();

            $model->nama = $request->nama;
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('jenisperalatan.index')
            ->with('success', 'Data nama sarpras berhasil ditambah.', ['page' => $this->page]);
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
    public function show($id, JenisPeralatan $model)
    {
        $this->authorize('view-7');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $jenisperalatan = $model::find($id);

        return view(
            'master.jenisperalatan.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('jenisperalatan.index'), 
                'jenisperalatan' => $jenisperalatan
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
        $this->authorize('edit-7');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);
        $jenisperalatan = JenisPeralatan::find($id);

        return view(
            'master.jenisperalatan.edit', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('jenisperalatan.create'), 
                'child' => 'Ubah Data', 
                'jenisperalatan' => $jenisperalatan,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JenisPeralatan $model, $id)
    {
        $this->authorize('edit-7');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $jenisperalatan = $model::find($id);
            $jenisperalatan->nama = $request->nama;

            $jenisperalatan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $jenisperalatan->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()
                ->route('jenisperalatan.index')
                ->with(
                        'success', 
                        'Data jenis peralatan berhasil diubah.', 
                        ['page' => $this->page]
                    );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request, JenisPeralatan $model)
    {
        $this->authorize('delete-7');

        $user = auth('sanctum')->user();

        $jenisperalatan = $model::find($id);

        $jenisperalatan->dlt = '1';

        $jenisperalatan->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $jenisperalatan->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Jenis Peralatan deleted successfully.',
        ], 200);
    }
}
