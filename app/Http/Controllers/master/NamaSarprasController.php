<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\master\NamaSarpras;

class NamaSarprasController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Nama Sarpras';
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
                $namasarpras = DB::table('tbmnamasarpras')
                    ->select('tbmnamasarpras.*')
                    ->where('tbmnamasarpras.dlt', '0')
                    ->where(function($query) use ($search)
                    {
                        // if (!is_null($provid) && $provid!='') $query->where('tbmkota.provid', $provid);
                        if (!is_null($search) && $search!='') {
                            // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                            $query->where(DB::raw('tbmnamasarpras.namasarpras'), 'ilike', '%'.$search.'%');
                            $query->orWhere(DB::raw('tbmnamasarpras.kategorisarpras'), 'ilike', '%'.$search.'%');
                        }
                    })
                    ->orderBy('tbmnamasarpras.kategorisarpras')
                ;
                $count = $namasarpras->count();
                $data = $namasarpras->skip($page)->take($perpage)->get();


            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'nama sarpras retrieved successfully.'); 
        }
        return view(
            'master.namasarpras.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('namasarpras.create'), 
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
            'master.namasarpras.create', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('namasarpras.index'), 
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
            $model = new NamaSarpras;

            DB::beginTransaction();

            $model->namasarpras = $request->namasarpras;
            $model->kategorisarpras = $request->kategorisarpras;
            
            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('namasarpras.index')
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
    public function show(NamaSarpras $namasarpras, $id)
    {
        $this->authorize('view-7');

        Log::channel('mibedil')->info('Halaman Lihat '.$this->page, ['id' => $id]);

        $datanamasarpras = $namasarpras::find($id)->firstOrFail();

        return view(
            'master.namasarpras.show', 
            [
                'page' => $this->page, 
                'child' => 'Lihat Data', 
                'masterurl' => route('namasarpras.index'), 
                'namasarpras' => $datanamasarpras
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
        $namasarpras = NamaSarpras::find($id);

        return view(
            'master.namasarpras.edit', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('namasarpras.create'), 
                'child' => 'Ubah Data', 
                'namasarpras' => $namasarpras,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NamaSarpras $NamaSarprasModel, $id)
    {
        $this->authorize('edit-7');

        $user = auth('sanctum')->user();

        try {
            DB::beginTransaction();

            $namasarpras = $NamaSarprasModel::find($id);
            $namasarpras->namasarpras = $request->namasarpras;
            $namasarpras->kategorisarpras = $request->kategorisarpras;

            $namasarpras->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $namasarpras->save();

            DB::commit();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()->route('namasarpras.index')
                ->with('success', 'Data nama sarpras berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-7');

        $user = auth('sanctum')->user();

        $namasarpras = NamaSarpras::find($id);

        $namasarpras->dlt = '1';

        $namasarpras->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

        $namasarpras->save();

        return response([
            'success' => true,
            'data'    => 'Success',
            'message' => 'Nama sarpras deleted successfully.',
        ], 200);
    }
}
