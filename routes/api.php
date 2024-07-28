<?php

use App\Http\Controllers\API\DokterController;
use App\Http\Controllers\API\KamarController;
use App\Http\Controllers\API\PMKPController;
use App\Http\Controllers\API\PoliklinikController;
use App\Http\Controllers\API\SpesialisController;
use App\Http\Controllers\API\LaporanSPIController;
use App\Http\Controllers\APIDashboard\DashboardController;
use App\Http\Controllers\APIDashboard\RalanController;
use App\Http\Controllers\APIDashboard\RanapController;
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

Route::group(['prefix' => 'dokter'], function () {
    Route::get('/', [DokterController::class, 'index']);
    Route::get('/{id}', [DokterController::class, 'show']);
    Route::get('jadwal', [DokterController::class, 'jadwal']);
    Route::get('jadwal/{id}', [DokterController::class, 'jadwal']);
});
Route::group(['prefix' => 'poliklinik'], function () {
    Route::get('/app', [PoliklinikController::class, 'index']);
    Route::get('/{id}', [PoliklinikController::class, 'show']);
    Route::get('jadwal', [PoliklinikController::class, 'jadwal']);
    Route::get('jadwal/{kd_poli}', [PoliklinikController::class, 'jadwal']);
    Route::get('dokter-poli/{kd_poli}', [PoliklinikController::class, 'dokterPoli']);
});
Route::group(['prefix' => 'kamar'], function () {
    Route::get('/', [KamarController::class, 'index']);
    Route::get('kelas', [KamarController::class, 'kelas']);
    Route::get('kelas/{kelas}', [KamarController::class, 'kelas']);
    Route::get('/{kd_bangsal}', [KamarController::class, 'show']);
});
Route::group(['prefix' => 'pmkp'], function () {
    Route::get('/indikator-mutu-nasional/{year}', [PMKPController::class, 'indikatorMutuNasional']);
});

Route::group(['prefix'=>'spesialis'], function () {
    Route::get('/', [SpesialisController::class, 'index']);
    Route::get('/{kd_sps}', [SpesialisController::class, 'show']);
});

Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/range/{daterange}', [DashboardController::class, 'range']);
});

Route::group(['prefix' => 'ralan'], function () {
    Route::get('/', [RalanController::class, 'index']);
    Route::get('/range/{daterange}', [RalanController::class, 'range']);
});

Route::group(['prefix' => 'ranap'], function () {
    Route::get('/', [RanapController::class, 'index']);
    Route::get('/range/{daterange}', [RanapController::class, 'range']);
});

Route::apiResource('laporan-spi', LaporanSPIController::class);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });