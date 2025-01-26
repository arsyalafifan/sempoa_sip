<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPegawai extends Model
{
    use HasFactory;

    protected $table = 'tbmdetailpegawai';
    protected $primaryKey = 'detailpegawaiid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'detailpegawaiid', 
        'pegawaiid', 
        'tahun', 
        'golpegawaiid', 
        'jenisjab',
        'eselon', 
        'jabatanid', 
        'golruangberkalaid', 
        'msgajiberkalathn',
        'msgajiberkalabln',
        'tmtberkala', 
        'opadd',
        'pcadd',
        'opedit',
        'pcedit',
        'dlt',
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

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class,'pegawaiid');
    }
}
