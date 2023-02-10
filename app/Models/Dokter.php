<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;
    protected $table = 'dokter';
    protected $primaryKey = 'kd_dokter';
    protected $hidden = ['tgl_lahir', 'gol_drh', 'agama', 'almt_tgl', 'no_telp', 'stts_nikah','tmp_lahir','laravel_through_key'];
    public $incrementing = false;

    public function spesialis()
    {
        return $this->belongsTo(Spesialis::class,'kd_sps','kd_sps');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class,'kd_dokter','kd_dokter');
    }

    public function poliklinik()
    {
        return $this->belongsToMany(Poliklinik::class, Jadwal::class, 'kd_dokter', 'kd_poli', 'kd_dokter', 'kd_poli')
        ->groupBy(
            'poliklinik.kd_poli',
            'poliklinik.nm_poli',
            'poliklinik.registrasi',
            'poliklinik.registrasilama',
            'poliklinik.status',
            'jadwal.kd_poli',
            'jadwal.kd_dokter'
        );
    }
}