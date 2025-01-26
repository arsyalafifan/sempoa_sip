<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\master\InstansiController;
use App\Http\Controllers\master\KecamatanController;
use App\Http\Controllers\master\KotaController;
use App\Http\Controllers\master\NamaSarprasController;
use App\Http\Controllers\master\SekolahController;
use App\Http\Controllers\master\TahunAjaranController;
use App\Http\Controllers\sarpras\SarprasTersediaController;
use App\Http\Controllers\master\IjazahController;
use App\Http\Controllers\master\IjazahtempController;
use App\Http\Controllers\legalisir\LegalisirController;
use App\Http\Controllers\sarpras\SarprasKebutuhanController;
use App\Http\Controllers\master\PegawaiController;
use App\Http\Controllers\master\ProgramController;
use App\Http\Controllers\master\KegiatanController;
use App\Http\Controllers\master\SubkegiatanController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\master\AksesController;
use App\Http\Controllers\master\PerusahaanController;
use App\Http\Controllers\master\UserController;
use App\Http\Controllers\perencanaansarpras\PenganggaranController;
use App\Http\Controllers\verifikasi\VerifikasiKebutuhanSarprasController;
use App\Models\sarpras\SarprasKebutuhan;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\master\StrukController;
use App\Http\Controllers\master\KelompokController;
use App\Http\Controllers\master\JenisController;
use App\Http\Controllers\master\JenisperalatanController;
use App\Http\Controllers\master\JurusanController;
use App\Http\Controllers\master\KelasController;
use App\Http\Controllers\master\ObyekController;
use App\Http\Controllers\master\ProvinsiController;
use App\Http\Controllers\master\RincianobyekController;
use App\Http\Controllers\master\RombelController;
use App\Http\Controllers\master\SubrincianobyekController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\transaksi\ProgresfisikController;
use App\Http\Controllers\perencanaansarpras\TenderController;
use App\Http\Controllers\transaksi\PengajuanGajiBerkalaController;
use App\Http\Controllers\transaksi\CekStatusPegawaiController;
use App\Http\Controllers\transaksi\RealisasiKebutuhanSarprasController;

// use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     // Log::channel('disnaker')->info('Halaman Awal');
//     return view('landing.index');
//     // return redirect()->route('dashboard');
// })->name('index');

Auth::routes();

// Route::controller(LoginController::class)->group(function(){
//     Route::get('login', 'showLoginForm')->name('login');
//     Route::post('login', 'actionLogin')->name('login.sarpras'); 
//     Route::post('logout', 'logout')->name('logout');
// });

    // Route::get('caristatuspegawai', 
    //     [PengajuanGajiBerkalaController::class, ]
    // )->name('caristatuspegawai.index');

    Route::resource('caristatuspegawai', CekStatusPegawaiController::class);

    Route::get('pengajuangajiberkala/downloadfilepengajuan/{pegawaipengajuangajiid}', [CekStatusPegawaiController::class,'downloadFile'])->name('pengajuangajiberkala.downloadFilePengajuan');
    Route::get('caristatuspegawai/loadcekstatusdetailpegawai/{nip}', [CekStatusPegawaiController::class,'loadCekStatusDetailPegawai'])->name('caristatuspegawai.loadCekStatusDetailPegawai');
    Route::get('caristatuspegawai/loadcekhistorypegawai/{pegawaiid}', [CekStatusPegawaiController::class,'loadCekHistoryPegawai'])->name('caristatuspegawai.loadCekHistoryPegawai');
    Route::post('caristatuspegawai/storehistory', [CekStatusPegawaiController::class,'storeHistory'])->name('caristatuspegawai.storeHistory');

    // View Pencarian No ijazah
    Route::get('legalisir-dashboard', function() {
        return view('layananspakat.legalisir_ijazah');
    })->name('legalisir-dashboard');
    // Route::get('daftar-ijazah', function() {
    //     return view('layananspakat.daftar_ijazah');
    // })->name('daftar-ijazah');
    Route::get('daftar-ijazah', [IjazahtempController::class, 'daftarijazah'])->name('daftar-ijazah');
    Route::post('daftar-ijazah/pengajuan', [IjazahtempController::class, 'store'])->name('daftar-ijazah.storepengajuan');

    // Route::resource('ijazahtemp', IjazahtempController::class);
    // Route::get('/ijazahtemp/create/{sekolahId}',[IjazahtempController::class, 'create'])->name('ijazahtemp.createWithSekolah');

    // Pencarian No ijazah 
    Route::get('legalisir-dashboard/search', [IjazahController::class, 'search'])->name('ijazah.search');
    // Pengajuan Legalisir Ijazah 
    Route::post('legalisir-dashboard/pengajuan', [LegalisirController::class, 'store'])->name('legalisir.storepengajuan');

    // Test Perintah Symblink
    // Route::get('/test', function() {
    //     Artisan::call('storage:link');
    // });

    // filter sekolah
    Route::get('/h/getsekolahbykota', [HelperController::class, 'getSekolahByKotaJenisJenjangKecamatan'])->name('helper.getSekolahByKota');
    // filter kecamatan
    Route::get('/h/getkecamatan/{kotaid}', [HelperController::class, 'getkecamatan'])->name('helper.getkecamatan');
    // get data sekolah
    Route::get('/h/getdatasekolah/{sekolahid}', [HelperController::class, 'getDataSekolah'])->name('helper.getdatasekolah');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function() {
        return view('dashboard');
    })->name('/');

    // Route::resource('instansi', InstansiController::class);
    Route::get('instansi', [InstansiController::class, 'index'])->name('instansi.index');
    Route::post('instansi', [InstansiController::class, 'save'])->name('instansi.save');
    Route::get('instansi/kota/{parentid}', [InstansiController::class, 'getkota'])->name('instansi.kota');

    // Provinsi
    Route::resource('prov', ProvinsiController::class);
    Route::get('prov/nextno', [ProvinsiController::class, 'getnextno'])->name('prov.nextno');

    // Kota
    Route::resource('kota', KotaController::class);
    Route::get('kota/nextno/{parentid}', [KotaController::class, 'getnextno'])->name('kota.nextno');

    // Kecamatan
    Route::resource('kecamatan', KecamatanController::class);
    Route::get('kecamatan/nextno/{parentid}', [KecamatanController::class, 'getnextno'])->name('kecamatan.nextno');

    // Tahun Ajaran
    Route::resource('tahunajaran', TahunAjaranController::class);

    // Sekolah
    Route::resource('sekolah', SekolahController::class);

    Route::post('/akreditasi/store', [SekolahController::class, 'storeakreditasi'])->name('sekolah.storeakreditasi');
    Route::post('/akreditasi/update/{akreditasiid}', [SekolahController::class, 'updateakreditasi'])->name('sekolah.updateakreditasi');
    Route::post('/akreditasi/{akreditasiid}', [SekolahController::class, 'hapusakreditasi'])->name('sekolah.hapusakreditasi');
    Route::get('/downloadakreditasifile/{filename}', [SekolahController::class, 'downloadakreditasifile'])->name('sekolah.downloadakreditasifile');

    Route::post('/sertifikatlahan/store', [SekolahController::class, 'storesertifikatlahan'])->name('sekolah.storesertifikatlahan');
    Route::post('/sertifikatlahan/update/{sertifikatlahanid}', [SekolahController::class, 'updatesertifikatlahan'])->name('sekolah.updatesertifikatlahan');
    Route::post('/sertifikatlahan/{sertifikatlahanid}', [SekolahController::class, 'hapussertifikatlahan'])->name('sekolah.hapussertifikatlahan');
    Route::get('/downloadsertifikatlahanfile/{sertifikatlahanid}', [SekolahController::class, 'downloadsertifikatlahanfile'])->name('sekolah.downloadsertifikatlahanfile');

    Route::post('/masterplan/store', [SekolahController::class, 'storemasterplan'])->name('sekolah.storemasterplan');
    Route::post('/masterplan/update/{masterplanid}', [SekolahController::class, 'updatemasterplan'])->name('sekolah.updatemasterplan');
    Route::post('/masterplan/{masterplanid}', [SekolahController::class, 'hapusmasterplan'])->name('sekolah.hapusmasterplan');
    Route::get('/downloadmasterplanfile/{masterplanid}', [SekolahController::class, 'downloadmasterplanfile'])->name('sekolah.downloadmasterplanfile');

    Route::post('/jumlahguru/store', [SekolahController::class, 'storejumlahguru'])->name('sekolah.storejumlahguru');
    Route::post('/jumlahguru/update/{jumlahguruid}', [SekolahController::class, 'updatejumlahguru'])->name('sekolah.updatejumlahguru');
    Route::post('/jumlahguru/{jumlahguruid}', [SekolahController::class, 'hapusjumlahguru'])->name('sekolah.hapusjumlahguru');

    Route::post('/pesertadidik/store', [SekolahController::class, 'storepesertadidik'])->name('sekolah.storepesertadidik');
    Route::post('/pesertadidik/update/{pesertadidikid}', [SekolahController::class, 'updatepesertadidik'])->name('sekolah.updatepesertadidik');
    Route::post('/pesertadidik/{pesertadidikid}', [SekolahController::class, 'hapuspesertadidik'])->name('sekolah.hapuspesertadidik');


    Route::post('/jumlahrombel/store', [SekolahController::class, 'storejumlahrombel'])->name('sekolah.storejumlahrombel');
    Route::post('/jumlahrombel/update/{jumlahrombelid}', [SekolahController::class, 'updatejumlahrombel'])->name('sekolah.updatejumlahrombel');
    Route::post('/jumlahrombel/{jumlahrombelid}', [SekolahController::class, 'hapusjumlahrombel'])->name('sekolah.hapusjumlahrombel');
    
    // Nama Sarpras
    Route::resource('namasarpras', NamaSarprasController::class);

    // Jenis Peralatan
    Route::resource('jenisperalatan', JenisperalatanController::class);
    
    // Perusahaan
    Route::resource('perusahaan', PerusahaanController::class);
    
    // Sarpras Tersedia
    Route::resource('sarprastersedia', SarprasTersediaController::class);
    Route::get('/sarprastersedia/create/{sekolahid}',[SarprasTersediaController::class, 'create'])->name('sarprastersedia.createBySekolahId');
    Route::post('/sarprastersedia/storejenissarpras',[SarprasTersediaController::class, 'storejenissarpras'])->name('sarprastersedia.storejenissarpras');
    Route::post('/sarprastersedia/updatejenissarpras/{sarprastersediaid}',[SarprasTersediaController::class, 'updatejenissarpras'])->name('sarprastersedia.updatejenissarpras');


    Route::get('/sarprastersedia/showdetailsarpras/{detailsarprasid}',[SarprasTersediaController::class, 'showdetailpagusarpras'])->name('sarprastersedia.showDetailPaguSarpras');
    Route::get('/sarprastersedia/createdetailsarpras/{sarprastersediaid}',[SarprasTersediaController::class, 'createdetailsarpras'])->name('sarprastersedia.createDetailSarpras');
    Route::post('/sarprastersedia/storedetailsarpras',[SarprasTersediaController::class, 'storedetailsarpras'])->name('sarprastersedia.storeDetailSarpras');
    Route::get('/sarprastersedia/editdetailsarpras/{detailsarprasid}',[SarprasTersediaController::class, 'editdetailsarpras'])->name('sarprastersedia.editDetailSarpras');
    Route::post('/sarprastersedia/updatedetailsarpras/{detailsarprasid}',[SarprasTersediaController::class, 'updatedetailsarpras'])->name('sarprastersedia.updateDetailSarpras');
    Route::post('/sarprastersedia/destroydetailsarpras/{detailsarprasid}',[SarprasTersediaController::class, 'destroydetailsarpras'])->name('sarprastersedia.destroyDetailSarpras');
    Route::post('/sarprastersedia/storedetailpagu',[SarprasTersediaController::class, 'storedetailpagu'])->name('sarprastersedia.storeDetailPagu');
    Route::post('/sarprastersedia/updatedetailpagu/{detailpagusarprasid}',[SarprasTersediaController::class, 'updatedetailpagu'])->name('sarprastersedia.updateDetailPagu');
    Route::post('/sarprastersedia/destroydetailpagu/{detailpagusarprasid}',[SarprasTersediaController::class, 'destroydetailpagu'])->name('sarprastersedia.destroyDetailPagu');
    Route::get('/sarprastersedia/downloadfiledetailpagu/{detailpagusarprasid}',[SarprasTersediaController::class, 'downloadfiledetailpagu'])->name('sarprastersedia.downloadFileDetailPagu');

    Route::get('/sarprastersedia/createdetailjumlahsarpras/{detailsarprasid}',[SarprasTersediaController::class, 'createdetailjumlahsarpras'])->name('sarprastersedia.createDetailJumlahSarpras');
    Route::post('/sarprastersedia/storedetailjumlahsarpras',[SarprasTersediaController::class, 'storedetailjumlahsarpras'])->name('sarprastersedia.storeDetailJumlahSarpras');
    Route::post('/sarprastersedia/updatedetailjumlahsarpras/{detailjumlahsarprasid}',[SarprasTersediaController::class, 'updatedetailjumlahsarpras'])->name('sarprastersedia.updateDetailJumlahSarpras');
    Route::get('/sarprastersedia/showfotojumlahsarpras/{detailjumlahsarprasid}',[SarprasTersediaController::class, 'showfotojumlahsarpras'])->name('sarprastersedia.showFotoDetailJumlahSarpras');
    Route::get('/sarprastersedia/downloadfiledetailjumlahsarpras/{filedetailjumlahsarprasid}', [SarprasTersediaController::class, 'downloadfiledetailjumlahsarpras'])->name('sarprastersedia.downloadFileDetailJumlahSarpras');
    Route::post('/sarprastersedia/destroydetailjumlahsarpras/{detailjumlahsarprasid}',[SarprasTersediaController::class, 'destroydetailjumlahsarpras'])->name('sarprastersedia.destroyDetailJumlahSarpras');
    Route::post('/sarprastersedia/storefiledetailjumlahsarpras/{detailjumlahsarprasid}',[SarprasTersediaController::class, 'storefiledetailjumlahsarpras'])->name('sarprastersedia.storeFileDetailJumlahSarpras');
    Route::post('/sarprastersedia/updatefiledetailjumlahsarpras/{filedetailjumlahsarprasid}',[SarprasTersediaController::class, 'updatefiledetailjumlahsarpras'])->name('sarprastersedia.updateFileDetailJumlahSarpras');
    Route::post('/sarprastersedia/destroyfiledetailjumlahsarpras/{filedetailjumlahsarprasid}',[SarprasTersediaController::class, 'destroyfiledetailjumlahsarpras'])->name('sarprastersedia.destroyFileDetailJumlahSarpras');

    

    Route::get('sarprastersedia/detailsarpras/{id}', [SarprasTersediaController::class,'loadDetailSarpras'])->name('sarprastersedia.loadDetailSarpras');
    Route::get('sarprastersedia/detailjumlahsarpras/{id}', [SarprasTersediaController::class,'loadDetailJumlahSarpras'])->name('sarprastersedia.loadDetailJumlahSarpras');

    Route::get('/sarprastersedia/namasarpras/{parentid}', [SarprasTersediaController::class, 'getNamaSarpras'])->name('sarprastersedia.getNamaSarpras');
    Route::get('/sarprastersedia/sekolah/{kotaid}/{jenis}/{jenjang}/{kecamatanid}', [SarprasTersediaController::class, 'getSekolah'])->name('sarprastersedia.getSekolah');

    Route::post('/kondisisarprastersedia/store', [SekolahController::class, 'storekondisisarprastersedia'])->name('sekolah.storekondisisarprastersedia');
    Route::post('/kondisisarprastersedia/update/{sarprastersediaid}', [SekolahController::class, 'updatekondisisarprastersedia'])->name('sekolah.updatekondisisarprastersedia');
    Route::post('/kondisisarprastersedia/{sarprastersediaid}', [SekolahController::class, 'hapuskondisisarprastersedia'])->name('sekolah.hapuskondisisarprastersedia');
    Route::get('/downloadkondisisarprastersediafile/{sarprastersediaid}', [SekolahController::class, 'downloadkondisisarprastersediafile'])->name('sekolah.downloadkondisisarprastersediafile');

    // Sarpras Kebutuhan
    Route::get('/sarpraskebutuhan/nextno',[SarprasKebutuhanController::class, 'getnextno'])->name('sarpraskebutuhan.nextno');
    Route::resource('sarpraskebutuhan', SarprasKebutuhanController::class);
    Route::get('/sarpraskebutuhan/sekolah/{parentid}', [SarprasKebutuhanController::class, 'getSekolah'])->name('sarpraskebutuhan.getSekolah');
    // Route::get('/sarpraskebutuhan/index', [SarprasKebutuhanController::class, 'index'])->name('sarpraskebutuhan.index1');
    Route::get('/sarpraskebutuhan/namasarpras/{parentid}', [SarprasKebutuhanController::class, 'getNamaSarpras'])->name('sarpraskebutuhan.getNamaSarpras');
    Route::get('/sarpraskebutuhan/create/{sekolahid}',[SarprasKebutuhanController::class, 'create'])->name('sarpraskebutuhan.createBySekolahId');
    Route::get('/sarpraskebutuhan/show/{sarpraskebutuhanid}',[SarprasKebutuhanController::class, 'showDetailKebutuhanSarpras'])->name('sarpraskebutuhan.showDetailKebutuhanSarpras');

    Route::post('/filesarpraskebutuhan/store', [SarprasKebutuhanController::class, 'storedetailsarpraskebutuhan'])->name('sarpraskebutuhan.storedetailsarpraskebutuhan');
    Route::post('/filesarpraskebutuhan/update/{parentid}', [SarprasKebutuhanController::class, 'updatedetailsarpraskebutuhan'])->name('sarpraskebutuhan.updatedetailsarpraskebutuhan');
    Route::post('/filesarpraskebutuhan/{parentid}', [SarprasKebutuhanController::class, 'hapusdetailsarpraskebutuhan'])->name('sarpraskebutuhan.hapusdetailsarpraskebutuhan');
    Route::get('/downloadfilesarpraskebutuhan/{parentid}', [SarprasKebutuhanController::class, 'downloadfilesarpraskebutuhan'])->name('sarpraskebutuhan.downloadfilesarpraskebutuhan');

    Route::post('/sarpraskebutuhan/pengajuan/{sarpraskebutuhanid}', [SarprasKebutuhanController::class, 'pengajuan'])->name('sarpraskebutuhan.pengajuan');


    // Master Ijazah
    Route::resource('ijazah', IjazahController::class);
    Route::get('/ijazah/create/{provId}/{sekolahId}',[IjazahController::class, 'create'])->name('ijazah.createWithSekolah');

     // Master Pegawai
    Route::resource('pegawai', PegawaiController::class);
    Route::get('/pegawai/create/{sekolahId}',[PegawaiController::class, 'create'])->name('pegawai.createWithSekolah');
    Route::get('/showdetailpegawai/{id}', [PegawaiController::class, 'showdetailpegawai'])->name('pegawai.showdetailpegawai');
    Route::post('/storedetailpegawai', [PegawaiController::class, 'storedetailpegawai'])->name('pegawai.storedetailpegawai');
    Route::post('/updatedetailpegawai/{id}', [PegawaiController::class, 'updatedetailpegawai'])->name('pegawai.updatedetailpegawai');
    Route::post('/destroydetailpegawai/{id}', [PegawaiController::class, 'destroydetailpegawai'])->name('pegawai.destroydetailpegawai');
    
    // Helper
    Route::get('/h/getkota/{provinsiid}', [HelperController::class, 'getkota'])->name('helper.getkota');
    Route::get('/h/getsekolah/{kecamatanid}', [HelperController::class, 'getsekolah'])->name('helper.getsekolah');
    Route::get('/h/getsekolahjenjang2/{jenjang}', [HelperController::class, 'getsekolahjenjang2'])->name('helper.getsekolahjenjang2');
    Route::get('/h/getsekolahjenjang/{kecamatanid}/{jenjang}', [HelperController::class, 'getsekolahjenjang'])->name('helper.getsekolahjenjang');
    Route::get('/h/getsekolahkotajenjang/{kotaid}/{jenjang}', [HelperController::class, 'getSekolahKotaJenjang1'])->name('helper.getSekolahKotaJenjang1');
    Route::get('/h/getsekolahkotajenjangjenis/{kotaid}/{jenjang}/{jenis}', [HelperController::class, 'getSekolahKotaJenjangJenis'])->name('helper.getSekolahKotaJenjangJenis');

    Route::get('/h/getsekolahjenisjenjang/{jenis}/{jenjang}', [HelperController::class, 'getsekolahjenisjenjang'])->name('helper.getsekolahjenisjenjang');
    Route::get('/h/getsekolahjeniskecamatan/{jenis}/{kecamatanid}', [HelperController::class, 'getsekolahjeniskecamatan'])->name('helper.getsekolahjeniskecamatan');
    Route::get('/h/getsekolahjenis/{jenis}/{jenjang}/{kecamatanid}', [HelperController::class, 'getsekolahjenis'])->name('helper.getsekolahjenis');
    Route::get('/h/getsekolahjenis2/{jenis}', [HelperController::class, 'getsekolahjenis2'])->name('helper.getsekolahjenis2');
    Route::get('/h/getsekolahkota/{kotaid}', [HelperController::class, 'getsekolahkota'])->name('helper.getsekolahkota');
    Route::get('/h/getsekolahprovinsi/{provinsiid}', [HelperController::class, 'getsekolahprovinsi'])->name('helper.getsekolahprovinsi');
    
    Route::get('/h/getsekolahbykotakecamatan', [HelperController::class, 'getSekolahByKotaJenisJenjangKecamatan'])->name('helper.getSekolahByKotaKecamatan');

    Route::get('h/getallperusahaan', [HelperController::class, 'getAllPerusahaan'])->name('helper.getAllPerusahaan');

    Route::get('h/getallperusahaan', [HelperController::class, 'getAllPerusahaan'])->name('helper.getAllPerusahaan');

    // Legalisir ijazah
    Route::resource('legalisir', LegalisirController::class);
    Route::post('legalisir/tolak/{id}/{ijazahid}', [LegalisirController::class,'tolak'])->name('legalisir.tolak');
    Route::post('legalisir/setuju/{id}/{ijazahid}', [LegalisirController::class,'setuju'])->name('legalisir.setuju');
    Route::get('legalisir/history/{legalisirid}/{ijazahid}', [LegalisirController::class,'history'])->name('legalisir.history');
    Route::get('legalisir/pengajuan/{id}', [LegalisirController::class,'viewpengajuan'])->name('legalisir.viewpengajuan');
    Route::get('legalisir/pengajuan2/{id}', [LegalisirController::class,'viewpengajuan2'])->name('legalisir.viewpengajuan2');

    // Program
    Route::resource('program', ProgramController::class);
    Route::get('program/nextno/{parentid}', [ProgramController::class, 'getnextno'])->name('program.nextno');

     // Kegiatan
     Route::resource('kegiatan', KegiatanController::class);
     Route::get('kegiatan/nextno/{id}', [KegiatanController::class,'getnextno'])->name('kegiatan.nextno');

     // Sub Kegiatan
     Route::resource('subkegiatan', SubkegiatanController::class);
     Route::get('subkegiatan/nextno/{id}', [SubkegiatanController::class,'getnextno'])->name('subkegiatan.nextno');

     // Struktur
    Route::resource('struk', StrukController::class);
    Route::get('struk/nextno/{parentid}', [StrukController::class, 'getnextno'])->name('struk.nextno');

    // Kelompok
    Route::resource('kelompok', KelompokController::class);
    Route::get('kelompok/nextno/{parentid}', [KelompokController::class, 'getnextno'])->name('kelompok.nextno');

     // Jenis
    Route::resource('jenis', JenisController::class);
    Route::get('jenis/nextno/{parentid}', [JenisController::class, 'getnextno'])->name('jenis.nextno');

    // Obyek
    Route::resource('obyek', ObyekController::class);
    Route::get('obyek/nextno/{parentid}', [ObyekController::class, 'getnextno'])->name('obyek.nextno');

    // Rincian Obyek
    Route::resource('rincianobyek', RincianobyekController::class);
    Route::get('rincianobyek/nextno/{parentid}', [RincianobyekController::class, 'getnextno'])->name('rincianobyek.nextno');

    // Sub Rincian Obyek
    Route::resource('subrincianobyek', SubrincianobyekController::class);
    Route::get('subrincianobyek/nextno/{parentid}', [SubrincianobyekController::class, 'getnextno'])->name('subrincianobyek.nextno');

     // Akses
     Route::resource('akses', AksesController::class);
     Route::get('akses/getnextno', [AksesController::class, 'getnextno'])->name('akses.nextno');

     //User 
     Route::put('user/resetpassword/{id}', [UserController::class, 'resetpassword'])->name('user.resetpassword');
     Route::get('user/password', [UserController::class, 'password'])->name('user.password');
     Route::post('user/password', [UserController::class, 'storeubahpassword'])->name('user.password.save');
     Route::resource('user', UserController::class);

     // Verifikasi Kebutuhan Sarpras
     Route::resource('verifikasikebutuhansarpras', VerifikasiKebutuhanSarprasController::class);
     Route::get('verifikasikebutuhansarpras/showfotokebutuhansarpras/{id}', [VerifikasiKebutuhanSarprasController::class, 'showFotoKebutuhanSarpras'])->name('verifikasikebutuhansarpras.showFotoKebutuhanSarpras');

     Route::get('verifikasikebutuhansarpras/history/{sarpraskebutuhanid}', [VerifikasiKebutuhanSarprasController::class,'history'])->name('verifikasikebutuhansarpras.history');
     Route::post('verifikasikebutuhansarpras/setuju/{sarpraskebutuhanid}', [VerifikasiKebutuhanSarprasController::class,'setuju'])->name('verifikasikebutuhansarpras.setuju');
     Route::post('verifikasikebutuhansarpras/tolak/{sarpraskebutuhanid}', [VerifikasiKebutuhanSarprasController::class,'tolak'])->name('verifikasikebutuhansarpras.tolak');

     // Perencanaan Sarpras: Penganggaran
     Route::resource('penganggaran', PenganggaranController::class);
     Route::get('penganggaran/detailanggaran/{sarpraskebutuhanid}', [PenganggaranController::class,'detailanggaran'])->name('penganggaran.detailanggaran');
     Route::post('penganggaran/storedetailpenganggaran/{sarpraskebutuhanid}', [PenganggaranController::class,'storeDetailPenganggaran'])->name('penganggaran.storeDetailPenganggaran');
     Route::post('penganggaran/updatedetailpenganggaran/{sarpraskebutuhanid}', [PenganggaranController::class,'updateDetailPenganggaran'])->name('penganggaran.updateDetailPenganggaran');
     Route::get('penganggaran/showdetailpenganggaran/{sarpraskebutuhanid}', [PenganggaranController::class,'showDetailPenganggaran'])->name('penganggaran.showDetailPenganggaran');
     Route::post('penganggaran/prosestender/{sarpraskebutuhanid}', [PenganggaranController::class,'prosestender'])->name('penganggaran.prosestender');
     Route::post('penganggaran/batalprosestender/{sarpraskebutuhanid}', [PenganggaranController::class,'batalprosestender'])->name('penganggaran.batalprosestender');
     Route::post('penganggaran/storedetailpagupenganggaran/{id}', [PenganggaranController::class,'storeDetailPaguPenganggaran'])->name('penganggaran.storeDetailPaguPenganggaran');
     Route::post('penganggaran/updatedetailpagupenganggaran/{id}', [PenganggaranController::class,'updateDetailPaguPenganggaran'])->name('penganggaran.updateDetailPaguPenganggaran');
     Route::post('penganggaran/deletedetailpagupenganggaran/{id}', [PenganggaranController::class,'deleteDetailPaguPenganggaran'])->name('penganggaran.deleteDetailPaguPenganggaran');


     // Perencanaan Sarpras: Tender
     Route::resource('tender', TenderController::class);
     Route::get('tender/detailanggaran/{sarpraskebutuhanid}', [TenderController::class,'detailanggaran'])->name('tender.detailanggaran');
     Route::post('tender/storedetailpenganggaran/{id}', [TenderController::class,'storeDetailPenganggaran'])->name('tender.storeDetailPenganggaran');
     Route::post('tender/progrespembangunan/{sarpraskebutuhanid}', [TenderController::class,'progrespembangunan'])->name('tender.progrespembangunan');
     Route::post('tender/batalprogrespembangunan/{sarpraskebutuhanid}', [TenderController::class,'batalprogrespembangunan'])->name('tender.batalprogrespembangunan');


     // Transaksi: Progres Fisik
     Route::resource('progresfisik', ProgresfisikController::class);
     Route::get('progresfisik/detaillaporan/{detailpaguanggaranid}', [ProgresfisikController::class, 'loadDetailLaporan'])->name('progresfisik.loadDetailLaporan');
     Route::get('progresfisik/detailjenisperalatan/{detailpenganggaranid}', [ProgresfisikController::class, 'loadDetailJenisPeralatan'])->name('progresfisik.loadDetailJenisPeralatan');
     Route::get('progresfisik/loadfotojenisperalatan/{detailjumlahperalatanid}', [ProgresfisikController::class, 'loadFotoJenisPeralatan'])->name('progresfisik.loadFotoJenisPeralatan');
     Route::post('progresfisik/storeDetailLaporan', [ProgresfisikController::class,'storeDetailLaporan'])->name('progresfisik.storeDetailLaporan');
     Route::post('progresfisik/updateDetailLaporan/{detaillaporanid}', [ProgresfisikController::class,'updateDetailLaporan'])->name('progresfisik.updateDetailLaporan');
    //  Route::get('progresfisik/selesai/{detailpaguanggaranid}', [ProgresfisikController::class,'selesai'])->name('progresfisik.selesai');
     Route::post('progresfisik/selesai/{detailpaguanggaranid}', [ProgresfisikController::class,'selesai'])->name('progresfisik.selesai');
    //  Route::post('progresfisik/deleteDetailLaporan/{detaillaporanid}', [ProgresfisikController::class,'deleteDetailLaporan'])->name('progresfisik.deleteDetailLaporan');
     Route::post('progresfisik/storedetailjumlahperalatan/{detailpaguanggaranid}', [ProgresfisikController::class, 'storedetailjumlahperalatan'])->name('progresfisik.storeDetailJumlahPeralatan');
     Route::post('progresfisik/updatedetailjumlahperalatan/{detailjumlahperalatanid}', [ProgresfisikController::class, 'updatedetailjumlahperalatan'])->name('progresfisik.updateDetailJumlahPeralatan');
     Route::post('progresfisik/deletedetailjumlahperalatan/{detailjumlahperalatanid}', [ProgresfisikController::class, 'deletedetailjumlahperalatan'])->name('progresfisik.deleteDetailJumlahPeralatan');
     Route::post('progresfisik/storedetailfotoperalatan/{detailjumlahperalatanid}', [ProgresfisikController::class, 'storedetailfotoperalatan'])->name('progresfisik.storeDetailFotoPeralatan');
     Route::post('progresfisik/updatedetailfotoperalatan/{filedetailjumlahperalatanid}', [ProgresfisikController::class, 'updatedetailfotoperalatan'])->name('progresfisik.updateDetailFotoPeralatan');
     Route::post('progresfisik/deletedetailfotoperalatan/{filedetailjumlahperalatanid}', [ProgresfisikController::class, 'deletedetailfotoperalatan'])->name('progresfisik.deleteDetailFotoPeralatan');

     // Transaksi: Realisasi
     Route::resource('realisasi', RealisasiKebutuhanSarprasController::class);
     Route::get('realisasi/loadrealisasi/{sarpraskebutuhanid}', [RealisasiKebutuhanSarprasController::class,'loadRealisasi'])->name('realisasi.loadRealisasi');
     Route::post('realisasi/storerealisasi/{sarpraskebutuhanid}', [RealisasiKebutuhanSarprasController::class,'storeRealisasi'])->name('realisasi.storeRealisasi');
     Route::post('realisasi/updaterealisasi/{realisasiid}', [RealisasiKebutuhanSarprasController::class,'updateRealisasi'])->name('realisasi.updateRealisasi');
     Route::post('realisasi/deleterealisasi/{realisasiid}', [RealisasiKebutuhanSarprasController::class,'deleteRealisasi'])->name('realisasi.deleteRealisasi');

     // Transaksi: Pengajuan Gaji Berkala
     Route::get('pengajuangajiberkala/downloadfile/{pegawaipengajuangajiid}', [PengajuanGajiBerkalaController::class,'downloadFile'])->name('pengajuangajiberkala.downloadFile');
     Route::post('pengajuangajiberkala/uploadFile/{pegawaipengajuangajiid}', [PengajuanGajiBerkalaController::class,'uploadFile'])->name('pengajuangajiberkala.uploadFile');
     Route::post('pengajuangajiberkala/storepengajuan', [PengajuanGajiBerkalaController::class,'storePengajuan'])->name('pengajuangajiberkala.storePengajuan');
     Route::post('pengajuangajiberkala/updatepengajuan/{pengajuangajiberkalaid}', [PengajuanGajiBerkalaController::class,'updatePengajuan'])->name('pengajuangajiberkala.updatePengajuan');
     Route::get('pengajuangajiberkala/loadpegawai', [PengajuanGajiBerkalaController::class,'loadPegawai'])->name('pengajuangajiberkala.loadPegawai');
     Route::get('pengajuangajiberkala/loadpegawaiedit/{pengajuangajiberkalaid}', [PengajuanGajiBerkalaController::class,'loadPegawaiEdit'])->name('pengajuangajiberkala.loadPegawaiEdit');
     Route::get('pengajuangajiberkala/loaddetailpegawai/{pegawaiid}', [PengajuanGajiBerkalaController::class,'loadDetailPegawai'])->name('pengajuangajiberkala.loadDetailPegawai');
     Route::get('pengajuangajiberkala/loadhistory/{pegawaipengajuangajiid}', [PengajuanGajiBerkalaController::class,'loadHistoryPegawai'])->name('pengajuangajiberkala.loadHistoryPegawai');
     Route::resource('pengajuangajiberkala', PengajuanGajiBerkalaController::class);


    // Master Kelas
     Route::get('kelas/nextno', [KelasController::class, 'getnextno'])->name('kelas.nextno');
     Route::resource('kelas', KelasController::class);
     
     // Master Rombel
     Route::get('rombel/nextno', [RombelController::class, 'getnextno'])->name('rombel.nextno');
     Route::resource('rombel', RombelController::class);
     
     // Master Jurusan
     Route::get('jurusan/nextno', [JurusanController::class, 'getnextno'])->name('jurusan.nextno');
     Route::resource('jurusan', JurusanController::class);
     
     // Guru
     Route::get('guru/nextno', [GuruController::class, 'getkodeguru'])->name('guru.nextno');
     Route::resource('guru', GuruController::class);
     
     // Murid
     Route::get('murid/nextno', [MuridController::class, 'getkodemurid'])->name('murid.nextno');
     Route::resource('murid', MuridController::class);

     // Pembayaran
     Route::get('pembayaran/nextno', [PembayaranController::class, 'getkodepembayaran'])->name('pembayaran.nextno');
     Route::get('pembayaran/showbuktipembayaran/{id}', [PembayaranController::class, 'showbuktipembayaran'])->name('pembayaran.showbuktipembayaran');
     Route::post('pembayaran/deletebuktipembayaran/{id}', [PembayaranController::class, 'deletebuktipembayaran'])->name('pembayaran.deletebuktipembayaran');
     Route::post('pembayaran/storebuktipembayaran', [PembayaranController::class, 'storebuktipembayaran'])->name('pembayaran.storebuktipembayaran');
     Route::resource('pembayaran', PembayaranController::class);
     
     // Absensi
     Route::post('absensi/deleteabsensi/{absensiid}', [AbsensiController::class, 'deleteabsensi'])->name('absensi.deleteabsensi');
     Route::get('absensi/showabsensi', [AbsensiController::class, 'showabsensi'])->name('absensi.showabsensi');
     Route::resource('absensi', AbsensiController::class);

     // Nilai 
     Route::post('nilai/deletenilai/{nilaiid}', [NilaiController::class, 'deletenilai'])->name('nilai.deletenilai');
     Route::get('nilai/shownilai', [NilaiController::class, 'shownilai'])->name('nilai.shownilai');
     Route::resource('nilai', NilaiController::class);
});
    

