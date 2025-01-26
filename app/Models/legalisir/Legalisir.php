<?php

namespace App\Models\legalisir;

// use App\Models\Akreditasi;
use App\Models\master\Ijazah;
use App\Models\master\Pegawai;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legalisir extends Model
{
    use HasFactory;

    protected $table = 'tbmlegalisir';
    protected $primaryKey = 'legalisirid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'legalisirid', 
        'ijazahid',
        'pegawaiid',
        'file_ijazah',
        'file_ktp',
        'tgl_pengajuan',
        'status',
        'keterangan',
        'file_legalisir',
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

    public function ijazah()
    {
        return $this->belongsTo(Ijazah::class,'ijazahid');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class,'pegawaiid');
    }

}
