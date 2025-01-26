<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'tbnilai';
    protected $primaryKey = 'nilaiid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    protected $fillable = [
        'nilaiid',
        'muridid',
        'mapel', // Mata pelajaran
        'nilai', // Nilai angka
        'tanggal',
        'keterangan', // Opsional: baik, cukup, kurang
    ];

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

    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }
}
