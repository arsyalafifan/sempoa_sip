<?php

namespace App\Models\transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileDetailJumlahPeralatan extends Model
{
    use HasFactory;

    protected $table = 'tbfiledetailjumlahperalatan';
    protected $primaryKey = 'filedetailjumlahperalatanid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filedetailjumlahperalatanid', 
        'detailjumlahperalatanid',
        'file', 
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

    public function detailjumlahperalatan()
    {
        return $this->belongsTo(DetailJumlahPeralatan::class,'detailjumlahperalatanid');
    }
    
    public function filedetailjumlahperalatan()
    {
        return $this->hasMany(FileDetailJumlahPeralatan::class,'detailjumlahperalatanid');
    }
}
