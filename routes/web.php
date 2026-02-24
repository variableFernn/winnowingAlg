<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\PlagiarismController;
use App\Http\Controllers\aboutController;
use App\Http\Controllers\RiwayatController;

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Deteksi Plagiarisme
|--------------------------------------------------------------------------
*/
Route::prefix('deteksi')->name('deteksi.')->group(function () {
    Route::get('/', [PlagiarismController::class, 'index'])->name('index');
    Route::post('/proses', [PlagiarismController::class, 'process'])->name('process');
    Route::post('/analyze', [PlagiarismController::class, 'analyze'])->name('analyze');
});

/*
|--------------------------------------------------------------------------
| Riwayat Deteksi
|--------------------------------------------------------------------------
*/
Route::prefix('riwayat')->name('riwayat.')->group(function () {
    Route::get('/', [PlagiarismController::class, 'history'])->name('index');
    Route::get('/{id}', [PlagiarismController::class, 'showResult'])->name('show');
    Route::delete('/{id}', [PlagiarismController::class, 'deleteResult'])->name('destroy');
    Route::get('/{id}/export-pdf', [PlagiarismController::class, 'exportPdf'])->name('export');
});

/*
|--------------------------------------------------------------------------
| Dataset Management
|--------------------------------------------------------------------------
*/
Route::prefix('dataset')->name('dataset.')->group(function () {
    Route::get('/', [DatasetController::class, 'index'])->name('index');
    Route::get('/create', [DatasetController::class, 'create'])->name('create');
    Route::post('/store', [DatasetController::class, 'store'])->name('store');
    Route::get('/{id}', [DatasetController::class, 'show'])->name('show');
    Route::delete('/{id}', [DatasetController::class, 'destroy'])->name('destroy');
    Route::post('/regenerate', [DatasetController::class, 'regenerateFingerprints'])->name('regenerate');
    Route::post('/import', [DatasetController::class, 'import'])->name('import');
});

/*
|--------------------------------------------------------------------------
| API Endpoints (Optional)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::post('/detect', [PlagiarismController::class, 'apiDetect']);
});

Route::get('/tentang', function () {
    return view('pages.about_algoritma');
});


Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('riwayat.show');
Route::delete('/riwayat/{id}', [RiwayatController::class, 'destroy'])->name('riwayat.destroy');
