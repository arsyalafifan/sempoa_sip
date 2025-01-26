<?php

namespace App\Models\sarpras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileDetailJumlahSarpras extends Model
{
    use HasFactory;

    protected $table = 'tbtransfiledetailjumlahsarpras';
    protected $primaryKey = 'filedetailjumlahsarprasid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filedetailjumlahsarprasid', 
        'detailjumlahsarprasid',
        'file', 
        'dlt',
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

    public function detailjumlahsarpras()
    {
        return $this->belongsTo(DetailJumlahSarpras::class,'detailjumlahsarprasid');
    }
    // public function namasarpras()
    // {
    //     return $this->belongsTo(NamaSarpras::class,'namasarprasid');
    // }
    // public function filedetailjumlahsarpras()
    // {
    //     return $this->hasMany(FileDetailJumlahSarpras::class, 'filedetailjumlahsarprasid')->where('dlt', '0');
    // }
}
