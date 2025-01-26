<?php

namespace App\Models\verifikasi;

use App\Models\sarpras\SarprasKebutuhan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusKebutuhanSarpras extends Model
{
    use HasFactory;

    protected $table = 'tbstatuskebutuhansarpras';
    protected $primaryKey = 'statuskebutuhansarprasid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'statuskebutuhansarprasid', 
        'sarpraskebutuhanid',
        'status',
        'tgl',
        'keterangan',
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

    public function kebutuhansarpras()
    {
        return $this->belongsTo(SarprasKebutuhan::class,'sarpraskebutuhanid');
    }
    // public function namasarpras()
    // {
    //     return $this->belongsTo(NamaSarpras::class,'namasarprasid');
    // }
    // public function filesarpraskebutuhan()
    // {
    //     return $this->hasMany(FileSarprasKebutuhan::class, 'filesarpraskebutuhanid')->where('dlt', '0');
    // }
}
