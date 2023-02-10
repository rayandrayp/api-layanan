<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPerawatan extends Model
{
    use HasFactory;
    protected $table = 'jns_perawatan';
    protected $primaryKey = 'kd_jenis_prw';
    public $incrementing = false;

    public function poliklinik()
    {
        return $this->belongsTo(Poliklinik::class,'kd_poli','kd_poli');
    }
}