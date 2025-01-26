<?php

namespace App\Models\sarpras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPaguSarprasTersedia extends Model
{
    use HasFactory;

    protected $table = 'tbtransdetailpagusarpras';
    protected $primaryKey = 'detailpagusarprasid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'detailpagusarprasid', 
        'detailsarprasid',
        'jenispagu', 
        'nilaipagu', 
        'perusahaanid', 
        'tglpelaksanaan', 
        'file', 
        'isselesai',
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
    // public function kondisisarpras()
    // {
    //     return $this->hasMany(KondisiSarpras::class, 'kondisisarprasid')->where('dlt', '0');
    // }
}
