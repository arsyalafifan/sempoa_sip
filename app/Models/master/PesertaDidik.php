<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaDidik extends Model
{
    use HasFactory;

    protected $table = 'tbmpesertadidik';
    protected $primaryKey = 'pesertadidikid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pesertadidikid', 
        'sekolahid', 
        'kelas',
        'jumlahpesertadidik', 
        'jeniskelamin', 
        'tahunajaranid', 
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
    public function tahunajaran()
    {
        return $this->belongsTo(TahunAjaran::class,'tahunajaranid');
    }
}
