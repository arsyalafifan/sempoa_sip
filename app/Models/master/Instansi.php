<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    use HasFactory;

    protected $table = 'tbminstansi';
    protected $primaryKey = 'instansiid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'namainstansi', 
        'alamat', 
        'telp', 
        'fax', 
        'kodepos', 
        'email', 
        'jenisinstansi', 
        'jenisdaerah', 
        'kepda', 
        'namakepda',
        'nipkepda', 
        'namasekda', 
        'nipsekda', 
        'provinsi', 
        'ibukota',
        'logo', 
        'picpemda', 
        'namakadis', 
        'nipkadis', 
        'tahun', 
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
