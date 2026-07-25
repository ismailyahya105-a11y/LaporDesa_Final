<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\TanggapanController;
use App\Http\Controllers\Api\SmartVillageController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/kategori', [KategoriController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/informasi', [SmartVillageController::class, 'informasi']);
    Route::get('/surat', [SmartVillageController::class, 'surat']); Route::post('/surat', [SmartVillageController::class, 'storeSurat']);
    Route::get('/darurat', [SmartVillageController::class, 'darurat']); Route::post('/darurat', [SmartVillageController::class, 'storeDarurat']);
    Route::get('/pasar', [SmartVillageController::class, 'pasar']); Route::post('/pasar', [SmartVillageController::class, 'storePasar']);
    Route::get('/usulan', [SmartVillageController::class, 'usulan']); Route::post('/usulan', [SmartVillageController::class, 'storeUsulan']);
    Route::get('/polling', [SmartVillageController::class, 'polling']); Route::post('/polling/{polling}/vote', [SmartVillageController::class, 'votePolling']);
    Route::get('/peta-desa', [SmartVillageController::class, 'peta']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::get('/laporan', [LaporanController::class, 'index']);
    Route::post('/laporan', [LaporanController::class, 'store']);
    Route::get('/laporan/{laporan}', [LaporanController::class, 'show']);
    Route::put('/laporan/{laporan}/status', [LaporanController::class, 'updateStatus']);

    Route::post('/tanggapan', [TanggapanController::class, 'store']);
});
