<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'tbmkeg';
    protected $primaryKey = 'kegid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kegid', 'progid', 'kegkode', 'kegnama', 'tahun',
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

    public function program()
    {
        return $this->belongsTo(Program::class,'progid');
    }
    public function subkegiatan()
    {
        return $this->hasMany(Subkegiatan::class, 'kegid')->where('dlt', '0');
    }
}
