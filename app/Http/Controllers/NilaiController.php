<?php

namespace App\Http\Controllers;

use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Nilai;

class NilaiController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Murid';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view-38');
        $user = auth('sanctum')->user();
        $nilai = DB::table('tbnilai')
        ->select('tbnilai.*', 'tbmurid.muridid', 'tbmurid.namamurid')
        ->join('tbmurid', function($join) {
            $join->on('tbmurid.muridid', '=', 'tbnilai.muridid');
            $join->on('tbmurid.dlt','=',DB::raw("'0'"));
        })
        ->where('tbnilai.dlt','=', 0)
        ->where(function($query) use ($user){
            if($user->grup != 1) $query->where('tbmurid.kodemurid', '=', $user->login);
        })
        ->get();
        $murids = Murid::where('dlt', '=', 0)
        ->where(function($query) use ($user){
            if($user->grup != 1) $query->where('kodemurid', '=', $user->login);
        })
        ->get();
        return view(
            'nilai.index', 
            [
                'page' => $this->page, 
                'createbutton' => false, 
                'createurl' => route('nilai.create'), 
                'murids' => $murids, 
                'nilai' => $nilai,
                'isSekolah' => Auth::user()->isSekolah()
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('add-38');
        $request->validate([
            'muridid' => 'required|exists:tbmurid,muridid',
            'mapel' => 'required|string|max:255',
            'nilai' => 'required|integer|min:0|max:100',
            'keterangan' => 'nullable|string',
        ]);

        Nilai::create($request->all());
        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add-38');
        try{
            $user = auth('sanctum')->user();

            $model = new Nilai();

            DB::beginTransaction();
            $model->muridid = $request->muridid;
            $model->nilai = $request->nilai;
            $model->mapel = $request->mapel;
            $model->tanggal = $request->tanggal;
            $model->keterangan = $request->keterangan;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->back()->with('success', 'Data nilai berhasil disimpan.');
        }catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    public function shownilai(Request $request) 
    {
        // $this->authorize('view-12');
        $user = auth('sanctum')->user();
        $this->authorize('view-38');
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if($request->ajax())
        {
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $nilai = DB::table('tbnilai')
                    ->select('tbnilai.*', 'tbmurid.muridid', 'tbmurid.namamurid')
                    ->join('tbmurid', function($join) {
                        $join->on('tbmurid.muridid', '=', 'tbnilai.muridid');
                        $join->on('tbmurid.dlt','=',DB::raw("'0'"));
                    })
                    ->where(function($query) use ($user){
                        if($user->grup != 1) $query->where('tbmurid.kodemurid', '=', $user->login);
                    })
                    ->where('tbnilai.dlt', '0')
                    ->orderBy('tbnilai.nilaiid')
                ;
                $count = $nilai->count();
                $data = $nilai->skip($page)->take($perpage)->get();
            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count,
            ], 'nilai retrieved successfully.'); 
        }
    }

    public function deletenilai(Request $request, $id)
    {
        $this->authorize('delete-38');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');

        try {
            DB::beginTransaction();

            $nilai = Nilai::find($id);

            $nilai->dlt = '1';
            $nilai->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $nilai->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'nilai deleted successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
