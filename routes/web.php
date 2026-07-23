<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PollingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SmartVillageController;
use App\Http\Controllers\SuratPengajuanController;
use App\Http\Controllers\TanggapanController;
use App\Http\Controllers\UsulanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = request()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    $laporans = $user->laporan();

    return view('dashboard', [
        'total' => (clone $laporans)->count(),
        'menunggu' => (clone $laporans)->where('status', 'menunggu')->count(),
        'diproses' => (clone $laporans)->where('status', 'diproses')->count(),
        'selesai' => (clone $laporans)->where('status', 'selesai')->count(),
        'recentReports' => $user->laporan()->with('kategori')->latest()->limit(5)->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('role:admin');
    Route::resource('laporan', LaporanController::class);
    Route::post('tanggapan', [TanggapanController::class, 'store'])->name('tanggapan.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/notifications/{notification}', [NotificationController::class, 'read'])->name('notifications.read');
    Route::get('/surat-online', [SuratPengajuanController::class, 'index'])->name('surat.index');
    Route::post('/surat-online', [SuratPengajuanController::class, 'store'])->name('surat.store');
    Route::get('/informasi-desa', [SmartVillageController::class, 'informasi'])->name('informasi.index');
    Route::get('/panic-button', [SmartVillageController::class, 'darurat'])->name('darurat.index');
    Route::post('/panic-button', [SmartVillageController::class, 'storeDarurat'])->middleware('throttle:3,1')->name('darurat.store');
    Route::get('/pasar-desa', [SmartVillageController::class, 'pasar'])->name('pasar.index');
    Route::post('/pasar-desa', [SmartVillageController::class, 'storeProdukUmkm'])->name('pasar.store');
    Route::get('/pasar-desa/{produkUmkm}/foto', [SmartVillageController::class, 'photoProduk'])->name('pasar.photo');
    Route::get('/pasar-desa/{produkUmkm}/edit', [SmartVillageController::class, 'editProdukUmkm'])->name('pasar.edit');
    Route::put('/pasar-desa/{produkUmkm}', [SmartVillageController::class, 'updateProdukUmkm'])->name('pasar.update');
    Route::get('/usulan-warga', [UsulanController::class, 'index'])->name('usulan.index');
    Route::post('/usulan-warga', [UsulanController::class, 'store'])->name('usulan.store');
    Route::post('/usulan-warga/{usulan}/vote', [UsulanController::class, 'vote'])->name('usulan.vote');
    Route::get('/polling-desa', [PollingController::class, 'index'])->name('polling.index');
    Route::post('/polling-desa/{polling}/vote', [PollingController::class, 'vote'])->name('polling.vote');
    Route::get('/peta-desa', [SmartVillageController::class, 'peta'])->name('peta.index');
});

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])->name('admin.dashboard');
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::patch('/surat-online/{surat}', [SuratPengajuanController::class, 'update'])->name('admin.surat.update');
    Route::get('/smart-village', [SmartVillageController::class, 'admin'])->name('admin.smart');
    Route::patch('/darurat/{darurat}', [SmartVillageController::class, 'updateDarurat'])->name('admin.darurat.update');
});

require __DIR__.'/auth.php';
