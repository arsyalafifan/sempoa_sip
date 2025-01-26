<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Murid;
use App\Models\Absensi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController as BaseController;

class AbsensiController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Absensi';
    }
    public function index()
    {
        $this->authorize('view-37');
        $user = auth('sanctum')->user();
        // dd($user->login);
        $murids = DB::table('tbmurid')->select('tbmurid.*')->where('tbmurid.dlt', '=', 0)
            ->where(function($query) use ($user){
                if($user->grup != 1) $query->where('tbmurid.kodemurid', '=', $user->login);
            })
            ->get();
        return view(
            'absensi.index', 
            [
                'page' => $this->page, 
                'createbutton' => false, 
                'createurl' => route('absensi.create'), 
                'murids' => $murids, 
                'isSekolah' => Auth::user()->isSekolah()
            ]);
    }

    public function store(Request $request)
    {
        $this->authorize('add-37');
        try{
            $user = auth('sanctum')->user();

            $model = new Absensi();

            DB::beginTransaction();
            $model->muridid = $request->muridid;
            $model->tanggal = $request->tanggal;
            $model->status = $request->status;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->back()->with('success', 'Data absensi berhasil disimpan.');
        }catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    public function showabsensi(Request $request) 
    {
        $this->authorize('view-37');
        // $this->authorize('view-12');
        $user = auth('sanctum')->user();
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if($request->ajax())
        {
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $absensi = DB::table('tbabsensi')
                    ->select('tbabsensi.*', 'tbmurid.muridid', 'tbmurid.namamurid')
                    ->join('tbmurid', function($join) {
                        $join->on('tbmurid.muridid', '=', 'tbabsensi.muridid');
                        $join->on('tbmurid.dlt','=',DB::raw("'0'"));
                    })
                    ->where('tbabsensi.dlt', '0')
                    ->where(function($query) use ($user){
                        if($user->grup != 1) $query->where('tbmurid.kodemurid', '=', $user->login);
                    })
                    ->orderBy('tbabsensi.absensiid')
                ;
                $count = $absensi->count();
                $data = $absensi->skip($page)->take($perpage)->get();
            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count,
            ], 'absensi retrieved successfully.'); 
        }
    }

    public function deleteabsensi(Request $request, $id)
    {
        $this->authorize('delete-37');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');

        try {
            DB::beginTransaction();

            $absensi = Absensi::find($id);

            $absensi->dlt = '1';
            $absensi->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $absensi->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'absensi deleted successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }
}
