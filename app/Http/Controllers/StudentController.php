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
            $student = Student::select('*')
                ->where('user.nik', '=', $nik)
                ->where('user.password', '=', md5($password))
                ->get();
            if(count($student) != 0){
                return $this->successResponse($student,"Success",200);
            }else {
                return $this->errorResponse('Cannot find the user.', 204);
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

    public function getUjianByUser(Request $request){
        if(isset($request->nik)){
            $nik = $request->nik;
            $siswa = Student::where('nik','=',$nik)->first();
            if(isset($siswa) && $siswa != null){
                $kelas = substr($siswa->kelas,0,1);
                $ujian = DB::table('ujian')
                    ->where('status','=','aktif')
                    ->where('kelas','=',$kelas)
                    ->select('*')
                    ->get();
                return $this->successResponse($ujian,"Success",200);
            }else{
                return $this->errorResponse('Student cannot be found.', 400);
            }
        }else{
            return $this->errorResponse('Something has been wrong.', 400);
        }
        return $this->errorResponse('Something has been wrong.', 400);
    }

    public function getSoalByIdUjian(Request $request)
    {
        if(isset($request->id)){
            $idUjian = $request->id;
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
        }else{
            return $this->errorResponse('Something has been wrong.', 400);
        }
    }
}
