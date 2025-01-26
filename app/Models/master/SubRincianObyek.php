<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubRincianObyek extends Model
{
    use HasFactory;

    protected $table = 'tbmsroby';
    protected $primaryKey = 'srobyid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'srobyid', 'robyid', 'srobykode', 'srobynama','tahun','isskpd','isssh',
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

    public function roby()
    {
        return $this->belongsTo(RincainObyek::class,'robyid');
    }
}
