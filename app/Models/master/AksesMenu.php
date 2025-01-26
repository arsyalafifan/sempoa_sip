<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AksesMenu extends Model
{
    use HasFactory;

    protected $table = 'tbmaksesmenu';
    protected $primaryKey = 'aksesmenuid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'menuid', 
        'aksesid', 
        'tambah', 
        'ubah', 
        'hapus', 
        'lihat', 
        'cetak', 
        'opadd', 
        'pcadd', 
        'opedit', 
        'pcedit', 
        'dlt'
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
