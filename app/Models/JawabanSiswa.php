<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_soal',
        'id_ujian',
        'nik',
        'jawaban',
        'nomor_soal',
        'pilihan_jawaban',
        'jawaban_asli',
        'ja',
        'jb',
        'jc',
        'jd',
        'je',
    ];
    protected $table = 'jawaban_siswa';
}
