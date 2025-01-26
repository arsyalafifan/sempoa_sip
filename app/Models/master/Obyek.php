<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obyek extends Model
{
    use HasFactory;

    protected $table = 'tbmoby';
    protected $primaryKey = 'obyid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'obyid', 'jenid', 'obykode', 'obynama','tahun',
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

    public function jen()
    {
        return $this->belongsTo(Jenis::class,'jenid');
    }
    public function roby()
    {
        return $this->hasMany(Obyek::class, 'obyid')->where('dlt', '0');
    }
}
