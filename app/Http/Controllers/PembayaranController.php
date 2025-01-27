<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\enumVar as enum;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\BuktiPembayaran;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->page = 'Pembayaran';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $user = auth('sanctum')->user();
        $this->authorize('view-39');
        Log::channel('mibedil')->info('Halaman '.$this->page);
        $pembayaran = [];

        if($request->ajax())
        {
            $search = $request->search;
            $pembayaran = $request->pembayaran;
            $status = $request->status;

            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $pembayaran = DB::table('tbpembayaran')
                        ->join('tbmurid', function($join)
                        {
                            $join->on('tbmurid.muridid', '=', 'tbpembayaran.muridid');
                            $join->on('tbmurid.dlt','=',DB::raw("'0'"));
                        })
                        ->select('tbpembayaran.*', 'tbmurid.namamurid')
                        ->where('tbpembayaran.dlt', '0')
                        ->where(function($query) use ($user){
                            if($user->grup != 1) $query->where('tbmurid.kodemurid', '=', $user->login);
                        })
                        ->where(function ($query) use ($search, $pembayaran, $status) {
                                if (!is_null($pembayaran) && $pembayaran != '') $query->where('tbpembayaran.category', $pembayaran);
                                if (!is_null($status) && $status != '') $query->where('tbpembayaran.status', $status);

                                if (!is_null($search) && $search!='') {
                                    // $query->where(DB::raw('CONCAT(tbmkota.kodekota, tbmkota.namakota)'), 'ilike', '%'.$search.'%');
                                    $query->where(DB::raw('tbpembayaran.namamurid'), 'ilike', '%'.$search.'%');
                                    // $query->orWhere(DB::raw('tbpembayaran.namaguru'), 'ilike', '%'.$search.'%');
                                    // $query->where('tbguru.sekolahid', $sekolahid);
                                }
                        });

                $count = $pembayaran->count();
                $data = $pembayaran->skip($page)->take($perpage)->get();
            }catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }
            return $this->sendResponse([
                'data' => $data,
                'count' => $count
            ], 'pembayaran retrieved successfully.');  
        }
        return view(
            'pembayaran.index', 
            [
                'page' => $this->page, 
                'createbutton' => true, 
                'createurl' => route('pembayaran.create'), 
                'pembayaran' => $pembayaran, 
                'isSekolah' => Auth::user()->isSekolah()
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add-39');
        Log::channel('mibedil')->info('Halaman Tambah '.$this->page);
        $user = auth('sanctum')->user();
        $murid = DB::table('tbmurid')
        ->select('tbmurid.muridid', 'tbmurid.namamurid', 'tbmurid.kodemurid')
        ->where('tbmurid.dlt', 0)
        ->where(function($query) use ($user){
            if($user->grup != 1) $query->where('tbmurid.kodemurid', '=', $user->login);
        })
        ->orderBy('tbmurid.muridid')
        ->get();
        return view(
            'pembayaran.create', 
            [
                'page' => $this->page, 
                'child' => 'Tambah Data', 
                'masterurl' => route('pembayaran.index'), 
                'murid' => $murid
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

        $this->authorize('add-39');
        try{
            $user = auth('sanctum')->user();

            $model = new Pembayaran();

            DB::beginTransaction();
            $tahun = Date('Y');
            $model->kodepembayaran = $request->kodepembayaran;
            $model->muridid = $request->muridid;
            $model->category = $request->category;
            $model->tglbayar = $request->tglbayar;
            $model->status = false;

            $model->fill(['opadd' => $user->login, 'pcadd' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('pembayaran.index')
            ->with('success', 'Data murid berhasil ditambah.', ['page' => $this->page]);
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
        $this->authorize('edit-39');
        Log::channel('mibedil')->info('Halaman Ubah '.$this->page);
        $user = auth('sanctum')->user();

        $pembayaran = Pembayaran::where('pembayaranid', $id)->firstOrFail();

        $murid = DB::table('tbmurid')
        ->select('tbmurid.muridid', 'tbmurid.kodemurid', 'tbmurid.namamurid')
        ->where('tbmurid.dlt', 0)
        ->where(function($query) use ($user){
            if($user->grup != 1) $query->where('tbmurid.kodemurid', '=', $user->login);
        })
        ->orderBy('tbmurid.kodemurid')
        ->get();

        return view('pembayaran.edit', 
                    [
                        'page' => $this->page, 
                        'createbutton' => true, 
                        'createurl' => route('pembayaran.create'), 
                        'child' => 'Ubah Data', 
                        'masterurl' => route('pembayaran.index'), 
                        'pembayaran' => $pembayaran,
                        'murid' => $murid
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
        $this->authorize('add-25');

        try{
            $user = auth('sanctum')->user();

            $model = Pembayaran::where('pembayaranid', $id)
            ->where('dlt', 0)
            ->first();

            DB::beginTransaction();
            $tahun = Date('Y');
            $model->kodepembayaran = $request->kodepembayaran;
            $model->muridid = $request->muridid;
            $model->category = $request->category;
            $model->tglbayar = $request->tglbayar;
            $model->status = $request->status;

            $model->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $model->save();

            DB::commit();

            return redirect()->route('pembayaran.index')
            ->with('success', 'pembayaran berhasil diubah.', ['page' => $this->page]);
        }catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete-39');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');

        try {
            DB::beginTransaction();

            $pembayaran = Pembayaran::find($id);

            $pembayaran->dlt = '1';
            $pembayaran->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            $pembayaran->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'pembayaran deleted successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function showbuktipembayaran(Request $request, $id) 
    {
        // $this->authorize('view-12');
        // $user = auth('sanctum')->user();
        $this->authorize('view-39');
        Log::channel('mibedil')->info('Halaman '.$this->page);

        if($request->ajax())
        {
            $data = [];
            $count = 0;
            $page = $request->get('start', 0);  
            $perpage = $request->get('length',50);

            try {
                $buktipembayaran = DB::table('tbbuktipembayaran')
                    // ->join('tbtransdetailpagusarpras', function($join) {
                    //     $join->on('tbtransdetailpagusarpras.detailsarprasid', '=', 'tbtransdesstailsarpras.detailsarprasid');
                    //     $join->on('tbtransdetailpagusarpras.dlt','=',DB::raw("'0'"));
                    // })
                    // ->join('tbmsubkeg', function() {
                    //     $join->on('tbmsubkeg.subkegid', '=', 'tbtransdetailsarpras.subkegid');
                    //     $join->on('tbmsubkeg.dlt','=',DB::raw("'0'"));
                    // })s
                    ->select('tbbuktipembayaran.*')
                    ->where('tbbuktipembayaran.pembayaranid', $id)
                    ->where('tbbuktipembayaran.dlt', '0')
                    ->orderBy('tbbuktipembayaran.buktipembayaranid')
                ;
                $count = $buktipembayaran->count();
                $data = $buktipembayaran->skip($page)->take($perpage)->get();
            } catch (QueryException $e) {
                return $this->sendError('SQL Error', $this->getQueryError($e));
            }
            catch (Exception $e) {
                return $this->sendError('Error', $e->getMessage());
            }

            return $this->sendResponse([
                'data' => $data,
                'count' => $count,
            ], 'bukti pembayaran retrieved successfully.'); 
        }
    }

    public function storebuktipembayaran(Request $request)
    {

        $this->authorize('add-39');
        $tglnow = date('Y-m-d');
        $user = auth('sanctum')->user();
        try {

            DB::beginTransaction();
            $model = new BuktiPembayaran();

            if ($request->hasFile('file')) {
                $fileName = $tglnow.'_'.rand(1,1000).'_'.$request->file('file')->getClientOriginalName();   
                $filePath = $request->file('file')->storeAs('public/uploaded/buktipembayaran', $fileName);   
                $model->buktipembayaran = $fileName;
                $model->pembayaranid = $request->pembayaranid;
            }

            $model->save();
            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'file uploaded successfully.',
                'file' => $model,
            ], 200);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()], 500);
        }
    }

    public function deletebuktipembayaran(Request $request, $id)
    {
        $this->authorize('delete-39');

        $user = auth('sanctum')->user();
        $tglnow = date('Y-m-d');

        try {
            DB::beginTransaction();

            $buktipembayaran = BuktiPembayaran::find($id);

            $buktipembayaran->dlt = '1';
            $buktipembayaran->fill(['opedit' => $user->login, 'pcedit' => $request->ip()]);

            if($buktipembayaran->buktipembayaran != ''  && $buktipembayaran->buktipembayaran != null){
                $file_old = public_path().'/storage/uploaded/buktipembayaran/'.$buktipembayaran->buktipembayaran;
                unlink($file_old);
            }
            $buktipembayaran->buktipembayaran = null;

            $buktipembayaran->save();

            DB::commit();

            return response([
                'success' => true,
                'data'    => 'Success',
                'message' => 'bukti pembayaran deleted successfully.',
            ], 200);

        } catch (QueryException $e) {
            return $this->sendError('SQL Error', $this->getQueryError($e));
        }
        catch (Exception $e) {
            return $this->sendError('Error', $e->getMessage());
        }
    }

    public function getkodepembayaran() 
    {
        $tglnow = date('Y');
        // Memecah string tahun menjadi dua bagian
        $part1 = substr($tglnow, 0, 2); // Mendapatkan dua karakter pertama
        $part2 = substr($tglnow, 2, 2); // Mendapatkan dua karakter terakhir

        // Menukarnya
        $reversed = $part2 . $part1;

        $data = DB::table('tbpembayaran')
        ->select(DB::raw('max(cast(tbpembayaran.kodepembayaran as bigint)) + 1 as kode'))
        ->where('tbpembayaran.dlt', '0')
        ->get();

        $kode = 1;
        if ($data[0]->kode != null) $kode = substr($data[0]->kode, 4);

        $nextno = '1';
        if (isset($kode)) {
            $nextno = $kode;
        }
        $nextno = str_pad($nextno, 5, '0', STR_PAD_LEFT);
        $nextno = $reversed.$nextno;

        return $nextno;
    }
}
