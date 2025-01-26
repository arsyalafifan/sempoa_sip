<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanGajiBerkala extends Model
{
    use HasFactory;

    protected $table = 'tbpengajuangajiberkala';
    protected $primaryKey = 'pengajuangajiberkalaid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pengajuangajiberkalaid', 
        'tglverifikasi',
        'status', 
        'keterangan', 
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

    // public function kebutuhansarpras()
    // {
    //     return $this->belongsTo(SarprasKebutuhan::class,'sarpraskebutuhanid');
    // }
}
