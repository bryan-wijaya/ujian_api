<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_ujian',
        'nik',
        'jenis_ujian',
        'tipe',
        'hasil',
        'ujian_ulang',
        'tampil',
    ];
    protected $table = 'nilai';
}
