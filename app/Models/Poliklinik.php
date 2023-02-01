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
    protected $hidden = [
        'laravel_through_key',
        'pivot'
     ];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'kd_poli', 'kd_poli');
    }

    public function dokter()
    {
        return $this->hasManyThrough(
            Dokter::class, 
            Jadwal::class, 
            'kd_poli', 
            'kd_dokter', 
            'kd_poli', 
            'kd_dokter'
            )->groupBy(
                'dokter.kd_dokter',
                'dokter.nm_dokter',
                'dokter.kd_sps',
                'dokter.jk',
                'dokter.alumni',
                'dokter.no_ijn_praktek',
                'dokter.status',
                'dokter.tmp_lahir',
                'dokter.tgl_lahir',
                'dokter.gol_drh',
                'dokter.agama',
                'dokter.almt_tgl',
                'dokter.no_telp',
                'dokter.stts_nikah',
                'jadwal.kd_poli'
            );
    }

    public function jenisPerawatan()
    {
        return $this->hasMany(JenisPerawatan::class, 'kd_poli', 'kd_poli');
    }
}