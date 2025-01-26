<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\master\Perusahaan;
use Svg\Tag\Rect;

class PerusahaanController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Perusahaan';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-8');

        Log::channel('mibedil')->info('Halaman '.$this->page);

        if ($request->ajax()) {

            $search = $request->search;

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $perusahaan = DB::table('tbmperusahaan')
                    ->select('tbmperusahaan.*')
                    ->where('tbmperusahaan.dlt', '0')
                    ->where(function($query) use ($search){
                        if (!is_null($search) && $search!='') {
                            // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                            $query->where(DB::raw('tbmperusahaan.nama'), 'ilike', '%'.$search.'%');
                            $query->orWhere(DB::raw('tbmperusahaan.jenis'), 'ilike', '%'.$search.'%');
                            $query->orWhere(DB::raw('tbmperusahaan.alamat'), 'ilike', '%'.$search.'%');
                        }
                    })
                ;

                $count = $perusahaan->count();
                $data = $perusahaan->skip($page)->take($perpage)->get();
            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'Perusahaan retrieved successfully.');  
        }

        return view(
            'master.perusahaan.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route(
                    'perusahaan.create'
                ), 
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
        $this->authorize('add-8');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
 
        return view(
            'master.perusahaan.create', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('perusahaan.index'), 
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
         $this->authorize('edit-8');

         $user = auth('sanctum')->user();

         try{
             $model = new Perusahaan;
 
             DB::beginTransaction();
 
             $model->nama = $request->nama;
             $model->telp = $request->telp;
             $model->jenis = $request->jenis;
             $model->npwp = $request->npwp;
             $model->namapimpinan = $request->namapimpinan;
             $model->alamat = $request->alamat;
 
             $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
 
            $model->save();

             DB::commit();
 
             return redirect()
                ->route('perusahaan.index')
                ->with('success', 'Data perusahaan berhasil ditambah.', ['page' => $this->page]);
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
        $this->authorize('view-8');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $perusahaan = Perusahaan::where('perusahaanid', $id)->firstOrFail();

        return view(
            'master.perusahaan.show', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('perusahaan.index'), 
                'perusahaan' => $perusahaan,
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
        $this->authorize('edit-8');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $perusahaan = Perusahaan::where('perusahaanid', $id)->firstOrFail();

        return view(
            'master.perusahaan.edit', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('perusahaan.index'), 
                'perusahaan' => $perusahaan,
            ]
        );
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
        $this->authorize('edit-8');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $model = Perusahaan::find($id);

            $model->nama = $request->nama;
            $model->telp = $request->telp;
            $model->jenis = $request->jenis;
            $model->npwp = $request->npwp;
            $model->namapimpinan = $request->namapimpinan;
            $model->alamat = $request->alamat;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

        }catch(QueryException $e){
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
        return redirect()
                ->route('perusahaan.index')
                ->with('success', 'Data perusahaan berhasil diubah.', ['page' => $this->page])
        ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $this->authorize('delete-8');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $model = Perusahaan::find($id);

            $model->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $model->dlt = '1';

            $model->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'perusahaan deleted successfully.',
            ], 200);

        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }
}
