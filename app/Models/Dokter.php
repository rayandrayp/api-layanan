<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;
    protected $table = 'dokter';

    public function spesialis()
    {
        return $this->belongsTo(Spesialis::class,'kd_sps');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class,'kd_dokter');
    }
}