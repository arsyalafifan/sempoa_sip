<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prov extends Model
{
    use HasFactory;

    protected $table = 'tbmprov';
    protected $primaryKey = 'provid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provid', 'kodeprov', 'namaprov', 'status',
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

    public function kota()
    {
        return $this->hasMany(Kota::class, 'provid')->where('dlt', '0');
    }
}
