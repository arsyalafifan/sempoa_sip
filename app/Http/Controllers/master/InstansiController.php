<?php

namespace App\Http\Controllers\master;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\master\Instansi\InstansiFormRequest;
use App\Models\master\Instansi;

class InstansiController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Instansi';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view-17');

        Log::channel('mibedil')->info('Halaman '.$this->page);

        $instansi = Instansi::where('dlt', '0')->first();

        if(is_null($instansi)) {
            $instansi = new Instansi;
        }

        // $prov = DB::table('tbmprov')
        // ->select('tbmprov.provid', 'tbmprov.kodeprov', 'tbmprov.namaprov')
        // ->where('tbmprov.dlt', 0)
        // ->orderBy('tbmprov.kodeprov')
        // ->get();

        $kota = [];

        return view(
            'master.instansi.index', 
            [
                'page' => $this->page, 
                'createbutton' => false, 
                'instansi' => $instansi, 
                'kota' => $kota
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(InstansiFormRequest $request)
    {
        $this->authorize('edit-17');

        $fileName = null;
        if($request->file('logo')) {
            $fileName = time().'_'.$request->logo->getClientOriginalName();
            $filePath = $request->file('logo')->move(public_path('storage/uploaded/instansi'), $fileName);
            // $filePath = $request->file('logo')->store('public/uploaded/sarpraskebutuhan');

        }

        $user = auth('sanctum')->user();

        $instansi = null;

        try{
            DB::beginTransaction();

            if($request->instansiid == null || $request->instansiid == "") $instansi = new Instansi;

            else $instansi = Instansi::find($request->instansiid);

            $instansi->namainstansi = $request->namainstansi;
            $instansi->alamat = $request->alamat;
            $instansi->fax = $request->fax;
            $instansi->telp = $request->telp;
            $instansi->kodepos = $request->kodepos;
            $instansi->email = $request->email;
            $instansi->jenisinstansi = $request->jenisinstansi;
            $instansi->jenisdaerah = $request->jenisdaerah;
            $instansi->kepda = $request->kepda;
            $instansi->namakepda = $request->namakepda;
            $instansi->provinsi = $request->provinsi;
            $instansi->ibukota = $request->ibukota;

            if(!is_null($fileName)) $instansi->logo = $fileName;

            $instansi->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $instansi->save();

            DB::commit();
        }catch(QueryException $e){
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }catch(Exception $e){
            return $this->sendError('Error', $e->getMessage());
        }

        return redirect()
                ->route('instansi.index')
                ->with(['success' => 'Data berhasil diubah.', 'instansi' => $instansi]);
    }
    public function getkota($provid) {
        try {
            $kota = DB::table('tbmkota')
                    ->join('tbmprov', function($join)
                    {
                        $join->on('tbmprov.provid', '=', 'tbmkota.provid');
                        $join->on('tbmprov.dlt','=',DB::raw("'0'"));
                    })
                    ->select('tbmkota.*', 'tbmprov.kodeprov', 'tbmprov.namaprov')
                    ->where('tbmkota.provid', $provid)
                    ->where('tbmkota.dlt', 0)
                    ->get();
        }
        catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    
        return $this->sendResponse($kota, 'Kota retrieved successfully.');
    }
}
