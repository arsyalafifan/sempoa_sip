<?php

namespace App\Models\master;

// use App\Models\master\Sekolah;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akreditasi extends Model
{
    use HasFactory;

    protected $table = 'tbmsekolahakreditasi';
    protected $primaryKey = 'akreditasiid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'akreditasiid', 
        'sekolahid', 
        'akreditasi', 
        'fileakreditasi', 
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

    public function setFilenamesAttribute($value)
    {
        $this->attributes['fileakreditasi'] = json_encode($value);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class,'sekolahid');
    }
}
