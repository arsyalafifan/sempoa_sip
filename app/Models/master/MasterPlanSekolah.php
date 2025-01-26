<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPlanSekolah extends Model
{
    use HasFactory;

    protected $table = 'tbmsekolahmasterplan';
    protected $primaryKey = 'masterplanid';
    const CREATED_AT = 'tgladd';
    const UPDATED_AT = 'tgledit';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'masterplanid', 
        'sekolahid', 
        'masterplan', 
        'filemasterplan', 
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
        $this->attributes['filemasterplan'] = json_encode($value);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class,'sekolahid');
    }
}
