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
            $retArr = [];
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
            $tmpArr = [];
            $valid = false;
            if($tipe == 'Tunggal'){
                if($jenis == "Pilihan Ganda"){
                    $soalpg = DB::table('ujian_has_soal')
                        ->join('soalpg', 'ujian_has_soal.id_soal', '=', 'soalpg.id')
                        ->where('ujian_has_soal.id_ujian','=',$idUjian)
                        ->select('soalpg.*')
                        ->get();
                    for($i = 0; $i < count($soalpg); $i++){
                        $rand = rand(0,count($soalpg) - 1);
                        while(!$valid){
                            $valid = true;
                            for($j = 0; $j < count($tmpArr);$j++){
                                if($tmpArr[$j] == $rand){
                                    $valid = false;
                                    break;
                                }
                            }
                            $rand +=1;
                            if($rand > count($soalpg)){
                                $rand = 0;
                            }
                        }
                        array_push($tmpArr, $rand);
                        array_push($retArr,$soalpg[$rand]);
                    }
                    return $this->successResponse($retArr);
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

    public function collectUjian(Request $request){
        // echo $request->nik;
        // echo $request->id_ujian;
        // echo $request->arr;
        if(isset($request->id_ujian)){
            $idUjian = $request->id_ujian;
            $arr = $request->arr;
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
                    for($i=0; $i < count($soalpg); $i++){
                        for($j=0;$j < count($arr);$j++){
                            if($soalpg[$i]->id == $arr[$j]['id']){
                                echo $arr[$j]['selected'] . ' | '   ;
                                echo $soalpg[$i]->kunci_jawaban . ' - ';
                                echo $soalpg[$i]->kunci_pg . ' - ' . PHP_EOL;
                                break;
                            }
                        }
                    }
                    exit;
                }else{
                    $isian = DB::table('ujian_has_soal')
                    ->join('soalisian', 'ujian_has_soal.id_soal', '=', 'soalisian.id')
                    ->where('ujian_has_soal.id_ujian','=',$idUjian)
                    ->select('soalisian.*')
                    ->get();
                }
            }else{
                //return gabungan
            }
        }
        return null;
    }
}
