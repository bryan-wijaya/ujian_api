<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentController extends ApiController
{
    //
    public function login(Request $request)
    {
        $post = $request->all();
        $nik = $post['nik'];
        $password = $post['password'];
        try {
            $students = DB::table('user')
                ->where('user.nik', '=', $nik)
                ->where('user.password', '=', md5($password))
                ->select('user.*')
                ->get();
            if(count($students) != 0){
                return $this->successResponse($students);
            }else {
                return $this->errorResponse('Cannot find the user.', 400);
            }
        } catch (Exception $e) {
            return $this->errorResponse('Something has been wrong.', 400);
        }
    }

    public function getAllUser()
    {
        $data = Student::all();
        return $this->successResponse($data);
    }

    public function getSoalByIdUjian($idUjian)
    {
        $ujian = DB::table('ujian')
        ->where('id','=',$idUjian)
        ->select('*')
        ->first();
        $tipe = "";
        $jenis = "";
        if($ujian != null){
            $tipe = $ujian->tipe;
            $jenis = $ujian->jenis;
        }else{
            return $this->errorResponse('Something has been wrong.', 400);
        }
        if($tipe == 'Tunggal'){
            if($jenis == "Pilihan Ganda"){
                $soalpg = DB::table('ujian_has_soal')
                ->join('soalpg', 'ujian_has_soal.id_soal', '=', 'soalpg.id')
                ->where('ujian_has_soal.id_ujian','=',$idUjian)
                ->select('soalpg.*')
                ->get();
                return $this->successResponse($soalpg);
            }else{
                $isian = DB::table('ujian_has_soal')
                ->join('soalisian', 'ujian_has_soal.id_soal', '=', 'soalisian.id')
                ->where('ujian_has_soal.id_ujian','=',$idUjian)
                ->select('soalisian.*')
                ->get();
                return $this->successResponse($isian);
            }
        }else{
            //return gabungan
            return $this->errorResponse('Something has been wrong.', 400);
        }
    }
}
