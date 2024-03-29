<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $table = 'jadwal_web';

    public function dokter()
    {
        return $this->belongsTo(Dokter::class,'kd_dokter','kd_dokter')->where('status', '1');
    }

    public function poliklinik()
    {
        return $this->belongsTo(Poliklinik::class,'kd_poli','kd_poli');
    }
}