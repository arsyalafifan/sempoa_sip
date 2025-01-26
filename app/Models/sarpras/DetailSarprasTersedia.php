<?php

namespace App\Models\sarpras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSarprasTersedia extends Model
{
    use HasFactory;

    protected $table = 'tbtransdetailsarpras';
    protected $primaryKey = 'detailsarprasid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'detailsarprasid', 
        'sarprastersediaid',
        'subkegid', 
        'sumberdana',
        'thperolehan',
        'koderekening',
        'subdetailkegiatan',
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

    public function sarprastersedia()
    {
        return $this->belongsTo(SarprasTersedia::class,'sarprastersediaid');
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
