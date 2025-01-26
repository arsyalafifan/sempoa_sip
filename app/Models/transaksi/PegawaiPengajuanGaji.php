<?php

namespace App\Models\transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiPengajuanGaji extends Model
{
    use HasFactory;

    protected $table = 'tbpegawaipengajuangaji';
    protected $primaryKey = 'pegawaipengajuangajiid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pegawaipengajuangajiid', 
        'pengajuangajiberkalaid',
        'pegawaiid', 
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
}
