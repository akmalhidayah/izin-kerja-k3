<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPermintaanController;
use App\Http\Controllers\User\IzinKerjaController;
use App\Http\Controllers\User\UserNotificationController;
use App\Http\Controllers\User\UploadController;
use App\Http\Controllers\User\JsaController;
use App\Http\Controllers\User\WorkingPermit\UmumPermitController;
use App\Http\Controllers\User\WorkingPermit\GasPanasPermitController;
use App\Http\Controllers\User\WorkingPermit\AirPermitController;


Route::get('/', function () {
    return view('welcome');
});

// ✅ Default user dashboard (pakai IzinKerjaController)
Route::get('pengajuan-user/izin-kerja', [IzinKerjaController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ✅ Step 1 Create Notification
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/pengajuan-user/izin-kerja/notification', [UserNotificationController::class, 'store'])->name('notification.store');
});

// ✅ Step 2, 5, 6, 7, 8, 9, 12 Upload
Route::post('/user/upload', [UploadController::class, 'store'])->name('upload.store');
Route::patch('/user/upload/{id}/status', [UploadController::class, 'updateStatus'])->name('upload.updateStatus');

// ✅ Step 3 JSA
Route::post('/user/jsa/store', [JsaController::class, 'store'])->name('jsa.store');
Route::get('/user/jsa/{id}/edit', [JsaController::class, 'edit'])->name('jsa.edit');
Route::patch('/user/jsa/{id}', [JsaController::class, 'update'])->name('jsa.update');
Route::get('/user/jsa/pdf/{notification_id}', [JsaController::class, 'downloadPdf'])->name('jsa.pdf');
Route::get('/user/jsa/pdf/view/{notification_id}', [JsaController::class, 'showPdf'])->name('jsa.pdf.view');

// step 4 - Umum
Route::post('/user/working-permit/umum/store', [UmumPermitController::class, 'store'])->name('working-permit.umum.store');
Route::get('/user/permit/umum/preview/{id}', [UmumPermitController::class, 'preview'])->name('working-permit.umum.preview');

Route::post('/user/working-permit/gaspanas/store', [GasPanasPermitController::class, 'store'])->name('working-permit.gaspanas.store');
Route::get('/user/permit/gaspanas/preview/{id}', [GasPanasPermitController::class, 'preview'])->name('working-permit.gaspanas.preview');

Route::post('/working-permit/air/store', [AirPermitController::class, 'store'])->name('working-permit.air.store');
Route::get('/working-permit/air/preview/{id}', [AirPermitController::class, 'preview'])->name('working-permit.air.preview');



// ✅ Admin-only routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/permintaansik', [AdminDashboardController::class, 'permintaanSIK'])->name('permintaansik');

    // ✅ Yang ini WAJIB ADA agar dropdown status tidak error
    Route::post('/permintaansik/{id}/step/{step}/status', [AdminPermintaanController::class, 'updateStatus'])->name('permintaansik.updateStatus');

    // ✅ Route show
    Route::get('/permintaansik/{id}', [AdminPermintaanController::class, 'show'])->name('permintaansik.show');

    Route::post('/permintaansik/{id}/upload-sik', [AdminPermintaanController::class, 'uploadSik'])->name('permintaansik.uploadSik');
    Route::delete('/permintaansik/{id}/delete-file/{step}', [AdminPermintaanController::class, 'deleteFile'])
        ->name('permintaansik.deleteFile');
});

// ✅ Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
