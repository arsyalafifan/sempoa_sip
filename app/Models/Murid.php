<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    use HasFactory;

    protected $table = 'tbmurid';
    protected $primaryKey = 'muridid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'muridid', 
        'kodemurid', 
        'namamurid', 
        'jeniskelamin', 
        'alamat',
        'agama', 
        'tempatlahir', 
        'tgllahir', 
        'notelp',
        'namasekolah',
        'namaortu', 
        'tglmasuk',
        'emailortu',
        'level',
        'status',
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

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'muridid', 'muridid');
    }
}
