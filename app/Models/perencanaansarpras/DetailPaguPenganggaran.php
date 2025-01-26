<?php

namespace App\Models\perencanaansarpras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPaguPenganggaran extends Model
{
    use HasFactory;

    protected $table = 'tbdetailpagupenganggaran';
    protected $primaryKey = 'detailpaguanggaranid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'detailpaguanggaranid', 
        'detailpenganggaranid',
        'jenispagu', 
        'nilaipagu', 
        'nokontrak', 
        'nilaikontrak', 
        'perusahaanid', 
        'tgldari', 
        'tglsampai', 
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

    public function detailpenganggaran()
    {
        return $this->belongsTo(DetailPenganggaran::class,'detailpenganggaranid');
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
