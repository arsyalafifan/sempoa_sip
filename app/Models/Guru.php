<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'tbguru';
    protected $primaryKey = 'guruid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'guruid', 
        'kodeguru', 
        'tglmasuk', 
        'namaguru', 
        'namapanggilan',
        'jeniskelamin', 
        'alamat', 
        'tempatlahir', 
        'agama',
        'tgllahir',
        'notelp', 
        'pendidikan',
        'email',
        'level',
        'status',
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

    // public function sekolah()
    // {
    //     return $this->belongsTo(Sekolah::class,'sekolahid');
    // }
    // public function legalisir()
    // {
    //     return $this->hasMany(Legalisir::class, 'pegawaiid')->where('dlt', '0');
    // }
}
