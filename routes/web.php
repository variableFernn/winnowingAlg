<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\PlagiarismController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\AuthController;



/*
|--------------------------------------------------------------------------
| Guest Routes (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Deteksi Plagiarisme
    Route::prefix('deteksi')->name('deteksi.')->group(function () {
        Route::get('/', [PlagiarismController::class, 'index'])->name('index');
        Route::post('/proses', [PlagiarismController::class, 'process'])->name('process');
        Route::post('/analyze', [PlagiarismController::class, 'analyze'])->name('analyze');

        // Safety Route (mencegah error 405 Method Not Allowed)
        Route::get('/proses', fn() => redirect()->route('deteksi.index'));
        Route::get('/analyze', fn() => redirect()->route('deteksi.index'));
    });

    // Riwayat Deteksi (Menggunakan PlagiarismController)
    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/', [PlagiarismController::class, 'history'])->name('index');
        Route::get('/{id}', [PlagiarismController::class, 'showResult'])->name('show');
        Route::delete('/{id}', [PlagiarismController::class, 'deleteResult'])->name('destroy');
        Route::get('/{id}/export-pdf', [PlagiarismController::class, 'exportPdf'])->name('export');
    });

    // Dataset Management
    Route::prefix('dataset')->name('dataset.')->group(function () {
        Route::get('/', [DatasetController::class, 'index'])->name('index');
        Route::get('/create', [DatasetController::class, 'create'])->name('create');
        Route::post('/store', [DatasetController::class, 'store'])->name('store');
        Route::get('/{id}', [DatasetController::class, 'show'])->name('show');
        Route::delete('/{id}', [DatasetController::class, 'destroy'])->name('destroy');
        Route::post('/regenerate', [DatasetController::class, 'regenerateFingerprints'])->name('regenerate');
        Route::post('/import', [DatasetController::class, 'import'])->name('import');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/tentang', function () {
    return view('pages.about_algoritma');
})->name('tentang');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::post('/detect', [PlagiarismController::class, 'apiDetect']);
});





// /*
// |--------------------------------------------------------------------------
// | Dashboard
// |--------------------------------------------------------------------------
// */
// Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// /*
// |--------------------------------------------------------------------------
// | Deteksi Plagiarisme
// |--------------------------------------------------------------------------
// */
// Route::prefix('deteksi')->name('deteksi.')->group(function () {

//     Route::get('/', [PlagiarismController::class, 'index'])->name('index');

//     // POST Routes (utama)
//     Route::post('/proses', [PlagiarismController::class, 'process'])->name('process');
//     Route::post('/analyze', [PlagiarismController::class, 'analyze'])->name('analyze');

//     /*
//     |--------------------------------------------------------------------------
//     | 🔥 GET Safety Route (ANTI ERROR 405)
//     |--------------------------------------------------------------------------
//     | Jika ada GET ke /deteksi/proses atau /deteksi/analyze
//     | maka akan diarahkan kembali ke halaman deteksi
//     */
//     Route::get('/proses', function () {
//         return redirect()->route('deteksi.index');
//     });

//     Route::get('/analyze', function () {
//         return redirect()->route('deteksi.index');
//     });
// });

// /*
// |--------------------------------------------------------------------------
// | Riwayat Deteksi
// |--------------------------------------------------------------------------
// */
// Route::prefix('riwayat')->name('riwayat.')->group(function () {
//     Route::get('/', [PlagiarismController::class, 'history'])->name('index');
//     Route::get('/{id}', [PlagiarismController::class, 'showResult'])->name('show');
//     Route::delete('/{id}', [PlagiarismController::class, 'deleteResult'])->name('destroy');
//     Route::get('/{id}/export-pdf', [PlagiarismController::class, 'exportPdf'])->name('export');
// });

// /*
// |--------------------------------------------------------------------------
// | Dataset Management
// |--------------------------------------------------------------------------
// */
// Route::prefix('dataset')->name('dataset.')->group(function () {
//     Route::get('/', [DatasetController::class, 'index'])->name('index');
//     Route::get('/create', [DatasetController::class, 'create'])->name('create');
//     Route::post('/store', [DatasetController::class, 'store'])->name('store');
//     Route::get('/{id}', [DatasetController::class, 'show'])->name('show');
//     Route::delete('/{id}', [DatasetController::class, 'destroy'])->name('destroy');
//     Route::post('/regenerate', [DatasetController::class, 'regenerateFingerprints'])->name('regenerate');
//     Route::post('/import', [DatasetController::class, 'import'])->name('import');
// });

// /*
// |--------------------------------------------------------------------------
// | API Endpoints
// |--------------------------------------------------------------------------
// */
// Route::prefix('api')->group(function () {
//     Route::post('/detect', [PlagiarismController::class, 'apiDetect']);
// });

// /*
// |--------------------------------------------------------------------------
// | Tentang Algoritma
// |--------------------------------------------------------------------------
// */
// Route::get('/tentang', function () {
//     return view('pages.about_algoritma');
// });

// /*
// |--------------------------------------------------------------------------
// | Riwayat (Controller Terpisah)
// |--------------------------------------------------------------------------
// */
// Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
// Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('riwayat.show');
// Route::delete('/riwayat/{id}', [RiwayatController::class, 'destroy'])->name('riwayat.destroy');