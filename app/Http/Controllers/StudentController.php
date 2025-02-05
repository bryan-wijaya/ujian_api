<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\JawabanSiswa;
use App\Models\Nilai;
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
                $nilai = DB::table('nilai')->where('nik','=',$nik)->where('ujian_ulang','=',0)->select('id_ujian')->get();
                $retNilai = json_decode(json_encode($nilai), true);
                $kelas = substr($siswa->kelas,0,1);
                $ujian = DB::table('ujian')
                    ->where('status','=','aktif')
                    ->where('kelas','=',$kelas)
                    ->whereNotIn('id', $retNilai)
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
            if($tipe == 'Tunggal'){
                if($jenis == "Pilihan Ganda"){
                    $soalpg = DB::table('ujian_has_soal')
                        ->join('soalpg', 'ujian_has_soal.id_soal', '=', 'soalpg.id')
                        ->where('ujian_has_soal.id_ujian','=',$idUjian)
                        ->select('soalpg.id','soalpg.materi','soalpg.kd','soalpg.soal','soalpg.kelas','soalpg.a','soalpg.b','soalpg.c','soalpg.d','soalpg.e','soalpg.gambarSoal','soalpg.gambarA','soalpg.gambarB','soalpg.gambarC','soalpg.gambarD','soalpg.gambarE','soalpg.kunci_pg','soalpg.kunci_jawaban','soalpg.selected')
                        ->get();
                    for($i = 0; $i < count($soalpg); $i++){
                        $valid = false;
                        $rand = rand(0,count($soalpg) - 1);
                        while(!$valid){
                            $valid = true;
                            for($j = 0; $j < count($tmpArr);$j++){
                                if($tmpArr[$j] == $rand){
                                    $valid = false;
                                    break;
                                }   
                            }
                            if(!$valid){
                                $rand +=1;
                                if($rand > count($soalpg) - 1){
                                    $rand = 0;
                                }
                            }
                        }
                        array_push($tmpArr, $rand);
                        array_push($retArr,$soalpg[$rand]);
                    }
                    for($i = 0; $i < count($retArr); $i++){
                        if($retArr[$i]->kunci_pg == "E"){
                            //kunci b
                            $tmp = $retArr[$i]->a;
                            $retArr[$i]->a =$retArr[$i]->b;
                            $retArr[$i]->b =$retArr[$i]->e;
                            $retArr[$i]->c = $tmp;

                            $tmpGambar = $retArr[$i]->gambarA;
                            $retArr[$i]->gambarA =$retArr[$i]->gambarB;
                            $retArr[$i]->gambarB =$retArr[$i]->gambarE;
                            $retArr[$i]->gambarC = $tmpGambar;

                            $retArr[$i]->kunci_pg = 5;
                        }else if($retArr[$i]->kunci_pg == "A"){
                            //kunci d
                            $tmp = $retArr[$i]->d;
                            $retArr[$i]->d =$retArr[$i]->a;
                            $retArr[$i]->c =$retArr[$i]->b;
                            $retArr[$i]->b =$retArr[$i]->e;
                            $retArr[$i]->a = $tmp;

                            $tmpGambar = $retArr[$i]->gambarD;
                            $retArr[$i]->gambarD =$retArr[$i]->gambarA;
                            $retArr[$i]->gambarC =$retArr[$i]->gambarB;
                            $retArr[$i]->gambarB =$retArr[$i]->gambarE;
                            $retArr[$i]->gambarA = $tmpGambar;

                            $retArr[$i]->kunci_pg = 1;
                        }else if($retArr[$i]->kunci_pg == "B"){
                            //kunci a
                            $tmp = $retArr[$i]->a;
                            $retArr[$i]->a =$retArr[$i]->b;
                            $retArr[$i]->b =$retArr[$i]->c;
                            $retArr[$i]->c =$retArr[$i]->d;
                            $retArr[$i]->d = $tmp;

                            $tmpGambar = $retArr[$i]->gambarA;
                            $retArr[$i]->gambarA =$retArr[$i]->gambarB;
                            $retArr[$i]->gambarB =$retArr[$i]->gambarC;
                            $retArr[$i]->gambarC =$retArr[$i]->gambarD;
                            $retArr[$i]->gambarD = $tmpGambar;

                            $retArr[$i]->kunci_pg = 2;
                        }else if($retArr[$i]->kunci_pg == "C"){
                            //kunci c (tidak di ubah)
                            $retArr[$i]->kunci_pg = 3;
                        }else if($retArr[$i]->kunci_pg == "D"){
                            //kunci d (tidak di ubah)
                            $retArr[$i]->kunci_pg = 4;
                        }
                        $retArr[$i]->e = null;
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
        if(isset($request->id_ujian)){
            $idUjian = $request->id_ujian;
            $arr = $request->arr;
            $nik = $request->nik;
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
            $nilai = 0;
            if($tipe == 'Tunggal'){
                if($jenis == "Pilihan Ganda"){
                    $benar = 0;
                    $soalpg = DB::table('ujian_has_soal')
                    ->join('soalpg', 'ujian_has_soal.id_soal', '=', 'soalpg.id')
                    ->where('ujian_has_soal.id_ujian','=',$idUjian)
                    ->select('soalpg.*')
                    ->get();
                    $totalSoal = count($soalpg);
                    if(isset($request->arr)){
                        $arr = $request->arr;
                        for($i = 0; $i < count($arr); $i++){
                            if($arr[$i]['selected'] != null){
                                if($this->checkKunci($arr[$i]['kunci_pg'],$arr[$i]['selected'])){
                                    $benar +=1;
                                }
                            }
                            //insert to jawaban siswa
                            $jawabanSiswa = new JawabanSiswa();
                            $jawabanSiswa->id_soal = $arr[$i]['id'];
                            $jawabanSiswa->nomor_soal = $i + 1;
                            $jawabanSiswa->id_ujian = $idUjian;
                            $jawabanSiswa->nik = $nik;
                            $jawabanSiswa->jawaban = $arr[$i]['e'];
                            $jawabanSiswa->pilihan_jawaban = $arr[$i]['selected'];
                            $jawabanSiswa->jawaban_asli = $this->checkJawabanAsli($arr[$i]['kunci_pg']);
                            $jawabanSiswa->ja = $arr[$i]['a'];
                            $jawabanSiswa->jb = $arr[$i]['b'];
                            $jawabanSiswa->jc = $arr[$i]['c'];
                            $jawabanSiswa->jd = $arr[$i]['d'];
                            $jawabanSiswa->save();
                        }
                    }
                    //insert to db nilai
                    $hasil = $benar / $totalSoal;
                    $hasil = $hasil * 100;

                    $nilai = new Nilai();
                    $nilai->nik = $nik;
                    $nilai->id_ujian = $idUjian;
                    $nilai->hasil = number_format($hasil,0);
                    $nilai->tipe = $tipe;
                    $nilai->tampil = "non-aktif";
                    $nilai->ujian_ulang = 0;
                    $nilai->save();
                    return $this->successResponse(null);

                }else{
                    $isian = DB::table('ujian_has_soal')
                    ->join('soalisian', 'ujian_has_soal.id_soal', '=', 'soalisian.id')
                    ->where('ujian_has_soal.id_ujian','=',$idUjian)
                    ->select('soalisian.*')
                    ->get();
                    return $this->successResponse(null);
                }
            }else{
                //return gabungan
            }
        }
        return $this->errorResponse('Something has been wrong.', 400);
    }

    public function report(Request $request){
        if(isset($request->nik)){
            $nilai = DB::table('nilai')
                ->join('ujian','nilai.id_ujian','=','ujian.id')->where('nilai.nik','=',$request->nik)->where('nilai.ujian_ulang','=',0)->select('nilai.*','ujian.nama as nama_ujian')->get();
            return $this->successResponse($nilai,"Success",200);
        }else{
            return $this->errorResponse('Something has been wrong.', 400);
        }
    }

    protected function checkKunci($kunci, $jawaban){
        $jawabanReal = '';
        if($kunci == 1){
            $jawabanReal = 'd';
        }else if($kunci == 2){
            $jawabanReal = 'a';
        }else if($kunci == 3){
            $jawabanReal = 'c';
        }else if($kunci == 4){
            $jawabanReal = 'd';
        }else if($kunci == 5){
            $jawabanReal = 'b';
        }
        if($jawaban == $jawabanReal){
            return true;
        }else{
            return false;
        }
    }

    protected function checkJawabanAsli($kunci){
        $jawabanReal = '';
        if($kunci == 1){
            $jawabanReal = 'd';
        }else if($kunci == 2){
            $jawabanReal = 'a';
        }else if($kunci == 3){
            $jawabanReal = 'c';
        }else if($kunci == 4){
            $jawabanReal = 'd';
        }else if($kunci == 5){
            $jawabanReal = 'b';
        }
        return $jawabanReal;
    }
}
