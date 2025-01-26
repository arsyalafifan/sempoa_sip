<?php

namespace App\Models\sarpras;

use App\Models\master\NamaSarpras;
use App\Models\master\Sekolah;
use App\Models\perencanaansarpras\DetailPenganggaran;
use App\Models\transaksi\RealisasiKebutuhanSarpras;
use App\Models\verifikasi\StatusKebutuhanSarpras;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SarprasKebutuhan extends Model
{
    use HasFactory;
    protected $table = 'tbtranssarpraskebutuhan';
    protected $primaryKey = 'sarpraskebutuhanid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sarpraskebutuhanid', 
        'sekolahid',
        'nopengajuan',
        'tglpengajuan',
        'pegawaiid', 
        'jenissarpras',
        'namasarprasid', 
        'jumlah', 
        'analisakebsarpras', 
        'dlt',
        'opadd',
        'pcadd',
        'opedit',
        'pcedit',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'opadd',
        'pcadd',
        'opedit',
        'pcedit',
        'dlt',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tgladd' => 'datetime',
        'tgledit' => 'datetime',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class,'sekolahid');
    }
    public function namasarpras()
    {
        return $this->belongsTo(NamaSarpras::class,'namasarprasid');
    }
    public function filesarpraskebutuhan()
    {
        return $this->hasMany(FileSarprasKebutuhan::class, 'filesarpraskebutuhanid')->where('dlt', '0');
    }
    public function statuskebutuhansarpras()
    {
        return $this->hasMany(StatusKebutuhanSarpras::class, 'sarpraskebutuhanid');
    }
    public function detailpenganggaran()
    {
        return $this->hasMany(DetailPenganggaran::class, 'sarpraskebutuhanid');
    }
    public function realisasi()
    {
        return $this->hasMany(RealisasiKebutuhanSarpras::class, 'sarpraskebutuhanid');
    }
}
