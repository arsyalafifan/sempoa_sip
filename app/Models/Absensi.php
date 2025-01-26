<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'tbabsensi';
    protected $primaryKey = 'absensiid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    protected $fillable = [
        'absensiid',
        'muridid',
        'tanggal',
        'status', // Hadir, Tidak Hadir, atau Izin
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

    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }
}
