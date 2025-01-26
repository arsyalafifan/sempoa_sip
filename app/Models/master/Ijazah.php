<?php

namespace App\Models\master;

// use App\Models\Akreditasi;

use App\Models\legalisir\Legalisir;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ijazah extends Model
{
    use HasFactory;

    protected $table = 'tbmijazah';
    protected $primaryKey = 'ijazahid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ijazahid', 
        'sekolahid', 
        'namasiswa', 
        'tempat_lahir', 
        'tgl_lahir',
        'namaortu', 
        'nis', 
        'noijazah',
        'tgl_lulus',
        'provinsiid',
        'namaprov',
        'namakab',
        'namakec',
        'namasekolah',
        'file_ijazah',
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

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class,'sekolahid');
    }
    public function legalisir()
    {
        return $this->hasMany(Legalisir::class, 'ijazahid')->where('dlt', '0');
    }
}
