<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanSPI extends Model
{
    use HasFactory;
    protected $table = 'laporan_spi';

    protected $fillable = [
        'nama',
        'pangkat',
        'nrp',
        'lingkup',
        'unit_dilaporkan',
        'personel_dilaporkan',
        'laporan',
        'surat_permohonan',
    ];
}