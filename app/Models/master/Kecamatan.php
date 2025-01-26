<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'tbmkecamatan';
    protected $primaryKey = 'kecamatanid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kecamatanid', 'kotaid', 'kodekec', 'namakec', 'status',
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
        return $this->belongsTo(Kota::class,'kotaid');
    }
    public function sekolah()
    {
        return $this->hasMany(Sekolah::class, 'sekolahid')->where('dlt', '0');
    }
}
