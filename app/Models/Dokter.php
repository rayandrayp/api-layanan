<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;
    protected $table = 'dokter';
    protected $primaryKey = 'kd_dokter';
    // hide tgl_lahir, gol_drh, agama, almt_tgl, no_telp, stts_nikah fields
    protected $hidden = ['tgl_lahir', 'gol_drh', 'agama', 'almt_tgl', 'no_telp', 'stts_nikah','tmp_lahir'];

    public function spesialis()
    {
        return $this->belongsTo(Spesialis::class,'kd_sps','kd_sps');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class,'kd_dokter','kd_dokter');
    }
}