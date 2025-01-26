<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    use HasFactory;

    protected $table = 'tbmkota';
    protected $primaryKey = 'kotaid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kotaid', 'kodekota', 'namakota', 'status',
        'opadd', 'pcadd', 'opedit', 'pcedit', 'dlt', 'jenis', 'provid'
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

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provid')->where('dlt', '0');
    }
    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class, 'kotaid')->where('dlt', '0');
    }
    public function sekolah()
    {
        return $this->hasMany(Sekolah::class, 'sekolahid')->where('dlt', '0');
    }
}
