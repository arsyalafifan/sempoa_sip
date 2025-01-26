<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SertifikatLahan extends Model
{
    use HasFactory;

    protected $table = 'tbmsekolahsertifikatlahan';
    protected $primaryKey = 'sertifikatlahanid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sertifikatlahanid', 
        'sekolahid', 
        'sertifikatlahan', 
        'filesertifikatlahan', 
        'dlt',
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

    public function setFilenamesAttribute($value)
    {
        $this->attributes['filesertifikatlahan'] = json_encode($value);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class,'sekolahid');
    }
}
