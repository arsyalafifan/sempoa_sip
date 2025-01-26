<?php
namespace App\Models\master;

use App\Models\legalisir\Legalisir;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'tbmpegawai';
    protected $primaryKey = 'pegawaiid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pegawaiid', 
        'sekolahid', 
        'unit', 
        'nip', 
        'jabatan',
        'ketjabatan', 
        'tgllahir', 
        'nama', 
        'npwp',
        'judulsk',
        'nosk', 
        'tgl_sk',
        'file_ttd',
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
        return $this->hasMany(Legalisir::class, 'pegawaiid')->where('dlt', '0');
    }
}

