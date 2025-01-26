<?php

namespace App\Models\sarpras;

use App\Models\master\NamaSarpras;
use App\Models\master\Sekolah;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SarprasTersedia extends Model
{
    use HasFactory;

    protected $table = 'tbtranssarprastersedia';
    protected $primaryKey = 'sarprastersediaid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sarprastersediaid', 
        'sekolahid',
        'jenissarpras',
        'namasarprasid', 
        'jumlahunit', 
        'dlt',
        'opadd',
        'pcadd',
        'opedit',
        'pcedit',
        'sarpraskebutuhanid',
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

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class,'sekolahid');
    }
    public function namasarpras()
    {
        return $this->belongsTo(NamaSarpras::class,'namasarprasid');
    }
    public function kondisisarpras()
    {
        return $this->hasMany(KondisiSarprasTersedia::class, 'kondisisarprasid')->where('dlt', '0');
    }
}
