<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'no_absen',
        'nik',
        'password',
        'nama',
        'unit',
        'kelas',
        'jenis_kelamin',
        'role',
        'last_status',
        'last_active',
        'last_ujian ',
    ];
    protected $table = 'user';
}
