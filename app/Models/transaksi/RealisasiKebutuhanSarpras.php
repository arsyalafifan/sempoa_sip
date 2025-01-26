<?php

namespace App\Models\transaksi;

use App\Models\sarpras\SarprasKebutuhan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiKebutuhanSarpras extends Model
{
    use HasFactory;

    protected $table = 'tbrealisasikebutuhansarpras';
    protected $primaryKey = 'realisasiid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'realisasiid', 
        'sarpraskebutuhanid',
        'nosp2d', 
        'tglsp2d', 
        'nilaisp2d', 
        'keterangan', 
        'file',
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
    // public function namasarpras()
    // {
    //     return $this->belongsTo(NamaSarpras::class,'namasarprasid');
    // }
    // public function kondisisarpras()
    // {
    //     return $this->hasMany(KondisiSarpras::class, 'kondisisarprasid')->where('dlt', '0');
    // }
}
