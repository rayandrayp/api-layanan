<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poliklinik extends Model
{
    use HasFactory;
    protected $table = 'poliklinik';
    protected $primaryKey = 'kd_poli';
    protected $keyType = 'string';
    public $incrementing = false;

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'kd_poli', 'kd_poli');
    }

    public function dokter()
    {
        // return $this->hasManyThrough(Dokter::class, Jadwal::class, 'kd_poli', 'kd_dokter', 'kd_poli', 'kd_dokter');
        // hasManyThrough group by dokter
        return $this->hasManyThrough(Dokter::class, Jadwal::class, 'kd_poli', 'kd_dokter', 'kd_poli', 'kd_dokter')->groupBy('dokter.kd_dokter','poliklinik.kd_poli');
    }
}