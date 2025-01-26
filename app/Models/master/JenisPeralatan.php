<?php

namespace App\Models\master;

use App\Models\transaksi\DetailJumlahPeralatan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPeralatan extends Model
{
    use HasFactory;

    protected $table = 'tbmjenisperalatan';
    protected $primaryKey = 'jenisperalatanid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jenisperalatanid', 
        'nama', 
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

    public function detailjumlahperalatan()
    {
        return $this->hasMany(DetailJumlahPeralatan::class, 'detailjumlahperalatanid')->where('dlt', '0');
    }
}
