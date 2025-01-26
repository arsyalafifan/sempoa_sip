<?php

namespace App\Models\master;

// use App\Models\Akreditasi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'tbmsekolah';
    protected $primaryKey = 'sekolahid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sekolahid', 
        'kotaid', 
        'kecamatanid', 
        'npsn', 
        'namasekolah',
        'jenjang', 
        'jenis', 
        'lintang', 
        'bujur', 
        'kurikulum',
        'predikat',
        'alamat',
        'opadd',
        'pcadd',
        'opedit',
        'pcedit',
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

    public function kota()
    {
        return $this->belongsTo(Kota::class,'kotaid');
    }
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class,'kecamatanid');
    }
    public function akreditasi()
    {
        return $this->hasMany(Akreditasi::class, 'sekolahid')->where('dlt', '0');
    }
    public function ijazah()
    {
        return $this->hasMany(Ijazah::class, 'sekolahid')->where('dlt', '0');
    }
    public function ijazahtemp()
    {
        return $this->hasMany(Ijazahtemp::class, 'sekolahid')->where('dlt', '0');
    }
}
