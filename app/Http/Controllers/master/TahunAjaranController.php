<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\master\TahunAjaran;
use App\enumVar as enum;
use App\Http\Requests\master\TahunAjaran\CreateRequest;
use App\Http\Requests\master\TahunAjaran\UpdateRequest;

class TahunAjaranController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Tahun Ajaran';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-3');

        Log::channel('mibedil')->info('Halaman '.$this->page);
        $tahunajaran = [];

        if($request->ajax())
        {
            $search = $request->search;
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $tahunajaran = 
                    DB::table('tbmtahunajaran')
                    ->select('tbmtahunajaran.*')
                    ->where('tbmtahunajaran.dlt', '0')
                    ->where(function($query) use ($search)
                    {
                        if (!is_null($search) && $search!='') {
                            $query->where(DB::raw('tbmtahunajaran.daritahun'), 'ilike', '%'.$search.'%');
                            $query->orWhere(DB::raw('tbmtahunajaran.sampaitahun'), 'ilike', '%'.$search.'%');
                        }
                    })
                    ->orderBy('tbmtahunajaran.daritahun')
                ;
                
                $count = $tahunajaran->count();
                $data = $tahunajaran->skip($page)->take($perpage)->get();
            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            } catch (Exception $e){
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'Tahun ajaran retrieved successfully');
        }
        return view(
            'master.tahunajaran.index',
            [
                'page' => $this->page,
                'createbutton' => true,
                'createurl' => route('tahunajaran.create'),
                'tahunajaran' => $tahunajaran
            ]
            )
        ;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-3');

        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);

        $tahunajaran = new TahunAjaran();

        $listtahun = $this->getTahun();
        $listbulan = $this->getbulan();

        return view(
                    'master.tahunajaran.create', 
                    [
                        'page' => $this->page, 
                        'child' => 'Tambah Data', 
                        'masterurl' => route('tahunajaran.index'), 
                        'tahunajaran' => $tahunajaran, 
                        'listtahun' => $listtahun,
                        'listbulan' => $listbulan,
                        'isshow' => false
                    ]
                );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $this->authorize('add-3');

        try {
            $user = auth('sanctum')->user();

            $model = new TahunAjaran();

            DB::beginTransaction();

            $model->daritahun = $request->daritahun;
            $model->daribulan = $request->daribulan;
            $model->sampaitahun = $request->sampaitahun;
            $model->sampaibulan = $request->sampaibulan;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);
            
            $model->save();
            DB::commit();
                
            return redirect()->route('tahunajaran.index')
                ->with('success', 'Data tahun ajaran berhasil ditambah.', ['page' => $this->page]);

        } catch (\Throwable $th) {
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('edit-3');

        Log::channel('mibedil')->info('Halaman Ubah '.$this->page, ['id' => $id]);

        $tahunajaran = TahunAjaran::where('tahunajaranid', $id)
                                    ->firstOrFail();
        $listtahun = $this->getTahun();
        $listbulan = $this->getbulan();

        return view(
                'master.tahunajaran.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('tahunajaran.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('tahunajaran.index'), 
                        'tahunajaran' => $tahunajaran, 
                        'listtahun' => $listtahun,
                        'listbulan' => $listbulan,
                        'isshow' => false
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
    public function update(UpdateRequest $request, TahunAjaran $tahunAjaranModel, $id)
    {

        $this->authorize('edit-3');

        $user = auth('sanctum')->user();
        try {

            DB::beginTransaction();

            $tahunAjaran = $tahunAjaranModel::find($id);

            $tahunAjaran->daritahun = $request->daritahun;
            $tahunAjaran->daribulan = $request->daribulan;
            $tahunAjaran->sampaitahun = $request->sampaitahun;
            $tahunAjaran->sampaibulan = $request->sampaibulan;

            $tahunAjaran->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $tahunAjaran->save();

            DB::commit();
        }catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
        return redirect()->route('tahunajaran.index')
                ->with('success', 'Data tahun ajaran berhasil diubah.', ['page' => $this->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
         $this->authorize('delete-3');

         $user = auth('sanctum')->user();

         $tahunajaran = TahunAjaran::find($id);
 
         // if (count($kota->tahunajaran)>0) 
         //     return response([
         //         'success' => false,
         //         'data'    => 'Data tidak bisa dihapus karena sudah digunakan pada menu tahunajaran'
         //     ], 200);
 
         $tahunajaran->dlt = '1';
 
         $tahunajaran->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);
 
         $tahunajaran->save();
 
         return response([
             'success' => true,
             'data'    => 'Success',
             'message' => 'Tahun Ajaran deleted successfully.',
         ], 200);
    }

    private function getTahun(){
        $datas = new enum;
        $datatahunids = $datas->listTahun();
        // $databulannames = $datas->listbulan("name");
        $listbulan = [];
        for($i = 0; $i<count($datatahunids); $i++) {
            $listtahun[] = [ 
                "tahun" => $datatahunids[$i],
                // "bulanvw" => $databulannames[$i]
            ];
        }
        return $listtahun;
    }

    private function getbulan(){
        $datas = new enum;
        $databulanids = $datas->listbulan();
        $databulannames = $datas->listbulan("name");
        $listbulan = [];
        for($i = 0; $i<count($databulanids); $i++) {
            $listbulan[] = [ 
                "bulan" => $databulanids[$i],
                "bulanvw" => $databulannames[$i]
            ];
        }
        return $listbulan;
    }
}
