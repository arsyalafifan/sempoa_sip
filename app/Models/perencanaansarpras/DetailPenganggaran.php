<?php

namespace App\Models\perencanaansarpras;

use App\Models\master\Subkegiatan;
use App\Models\sarpras\SarprasKebutuhan;
use App\Models\transaksi\DetailJumlahPeralatan;
use App\Models\verifikasi\StatusKebutuhanSarpras;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenganggaran extends Model
{
    use HasFactory;

    protected $table = 'tbdetailpenganggaran';
    protected $primaryKey = 'detailpenganggaranid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'detailpenganggaranid', 
        'sarpraskebutuhanid',
        'statuskebutuhansarprasid',
        'subkegid', 
        'sumberdana', 
        'subdetailkegiatan', 
        'jumlah', 
        'satuan', 
        'jenispenganggaran', 
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

    public function kebutuhansarpras()
    {
        return $this->belongsTo(SarprasKebutuhan::class,'sarpraskebutuhanid');
    }
    public function statuskebutuhansarpras()
    {
        return $this->belongsTo(StatusKebutuhanSarpras::class, 'statuskebutuhansarprasid');
    }
    public function sugbegid()
    {
        return $this->belongsTo(Subkegiatan::class, 'subkegid');
    }
    public function detailpenganggaran()
    {
        return $this->hasMany(DetailPaguPenganggaran::class, 'detailpenganggaranid');
    }
    public function detailjumlahperalatan()
    {
        return $this->hasMany(DetailJumlahPeralatan::class, 'detailjumlahperalatanid');
    }
    // public function namasarpras()
    // {
    //     return $this->belongsTo(NamaSarpras::class,'namasarprasid');
    // }
    // public function kondisisarpras()
    // {
    //     return $this->hasMany(KondisiSarpras::class, 'kondisisarprasid')->where('dlt', '0');
    // }
}
