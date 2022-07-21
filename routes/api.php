<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post("students/login", 'App\Http\Controllers\StudentController@login');
Route::get("students/getAllUser", 'App\Http\Controllers\StudentController@getAllUser');
Route::post("students/getUjianByUser", 'App\Http\Controllers\StudentController@getUjianByUser');
Route::post("students/soalUjian", 'App\Http\Controllers\StudentController@getSoalByIdUjian');
Route::post("students/collectUjian", 'App\Http\Controllers\StudentController@collectUjian');
