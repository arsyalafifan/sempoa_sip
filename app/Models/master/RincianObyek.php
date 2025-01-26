<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianObyek extends Model
{
    use HasFactory;

    protected $table = 'tbmroby';
    protected $primaryKey = 'robyid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'robyid', 'obyid', 'robykode', 'robynama','tahun',
        'opadd', 'pcadd', 'opedit', 'pcedit', 'dlt'
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

    public function oby()
    {
        return $this->belongsTo(Obyek::class,'obyid');
    }
    public function sroby()
    {
        return $this->hasMany(RincianObyek::class, 'robyid')->where('dlt', '0');
    }
}
