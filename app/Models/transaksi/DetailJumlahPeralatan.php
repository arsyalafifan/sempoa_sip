<?php

namespace App\Models\transaksi;

use App\Models\master\JenisPeralatan;
use App\Models\perencanaansarpras\DetailPenganggaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailJumlahPeralatan extends Model
{
    use HasFactory;

    protected $table = 'tbdetailjumlahperalatan';
    protected $primaryKey = 'detailjumlahperalatanid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'detailjumlahperalatanid', 
        'detailpenganggaranid',
        'jenisperalatanid', 
        'jumlah', 
        'satuan', 
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

    public function detailpenganggaran()
    {
        return $this->belongsTo(DetailPenganggaran::class,'detailpenganggaranid');
    }
    public function jenisperalatan()
    {
        return $this->belongsTo(JenisPeralatan::class, 'jenisperalatanid');
    }
    public function filedetailjumlahperalatan()
    {
        return $this->hasMany(FileDetailJumlahPeralatan::class,'detailjumlahperalatanid');
    }
}
