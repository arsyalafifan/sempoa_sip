<?php

namespace App\Models\sarpras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailJumlahSarpras extends Model
{
    use HasFactory;

    protected $table = 'tbtransdetailjumlahsarpras';
    protected $primaryKey = 'detailjumlahsarprasid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'detailjumlahsarprasid', 
        'detailsarprasid',
        'kondisi', 
        'jumlah', 
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

    public function detailsarpras()
    {
        return $this->belongsTo(DetailSarprasTersedia::class,'detailsarprasid');
    }
    // public function namasarpras()
    // {
    //     return $this->belongsTo(NamaSarpras::class,'namasarprasid');
    // }
    public function filedetailjumlahsarpras()
    {
        return $this->hasMany(FileDetailJumlahSarpras::class, 'filedetailjumlahsarprasid')->where('dlt', '0');
    }
}
