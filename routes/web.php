<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPermintaanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RolePermissionController;
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

// ✅ USER SIDE (usertype = 'user')
Route::middleware(['auth', 'verified', 'usertype:user'])->group(function () {
    // Dashboard User
    Route::get('pengajuan-user/izin-kerja', [IzinKerjaController::class, 'index'])->name('dashboard');

    // Step 1: Create Notification
    Route::post('/pengajuan-user/izin-kerja/notification', [UserNotificationController::class, 'store'])->name('notification.store');

    // Step 2,5,6,7,8,9,12 Upload
    Route::post('/user/upload', [UploadController::class, 'store'])->name('upload.store');
    Route::patch('/user/upload/{id}/status', [UploadController::class, 'updateStatus'])->name('upload.updateStatus');

    // Step 3: JSA
    Route::post('/user/jsa/store', [JsaController::class, 'store'])->name('jsa.store');
    Route::get('/user/jsa/{id}/edit', [JsaController::class, 'edit'])->name('jsa.edit');
    Route::patch('/user/jsa/{id}', [JsaController::class, 'update'])->name('jsa.update');
    Route::get('/user/jsa/pdf/{notification_id}', [JsaController::class, 'downloadPdf'])->name('jsa.pdf');
    Route::get('/user/jsa/pdf/view/{notification_id}', [JsaController::class, 'showPdf'])->name('jsa.pdf.view');

    // Step 4: Working Permit
    Route::post('/user/working-permit/umum/store', [UmumPermitController::class, 'store'])->name('working-permit.umum.store');
    Route::get('/user/permit/umum/preview/{id}', [UmumPermitController::class, 'preview'])->name('working-permit.umum.preview');

    Route::post('/user/working-permit/gaspanas/store', [GasPanasPermitController::class, 'store'])->name('working-permit.gaspanas.store');
    Route::get('/user/permit/gaspanas/preview/{id}', [GasPanasPermitController::class, 'preview'])->name('working-permit.gaspanas.preview');

    Route::post('/working-permit/air/store', [AirPermitController::class, 'store'])->name('working-permit.air.store');
    Route::get('/working-permit/air/preview/{id}', [AirPermitController::class, 'preview'])->name('working-permit.air.preview');
});

// ✅ ADMIN SIDE (usertype = 'admin' + role = 'Admin' atau 'Super Admin')
Route::middleware(['auth', 'verified', 'usertype:admin', 'role:Admin,Super Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/permintaansik', [AdminDashboardController::class, 'permintaanSIK'])->name('permintaansik');

    Route::post('/permintaansik/{id}/step/{step}/status', [AdminPermintaanController::class, 'updateStatus'])->name('permintaansik.updateStatus');
    Route::get('/permintaansik/{id}', [AdminPermintaanController::class, 'show'])->name('permintaansik.show');
    Route::post('/permintaansik/{id}/upload-sik', [AdminPermintaanController::class, 'uploadSik'])->name('permintaansik.uploadSik');
    Route::delete('/permintaansik/{id}/delete-file/{step}', [AdminPermintaanController::class, 'deleteFile'])->name('permintaansik.deleteFile');
});

// ✅ SUPER ADMIN (usertype = 'admin' + role = 'Super Admin') untuk manajemen user
Route::middleware(['auth', 'verified', 'usertype:admin', 'role:Super Admin'])
    ->prefix('admin/userpanel')
    ->name('admin.userpanel.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserPanelController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\UserPanelController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\UserPanelController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserPanelController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [\App\Http\Controllers\Admin\UserPanelController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\UserPanelController::class, 'destroy'])->name('destroy');
    });
Route::middleware(['auth', 'verified', 'usertype:admin', 'role:Super Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/role-permission', [RolePermissionController::class, 'index'])->name('role_permission.index');
        Route::get('/role-permission/{notification}/edit', [RolePermissionController::class, 'edit'])->name('role_permission.edit');
        Route::patch('/role-permission/{notification}', [RolePermissionController::class, 'update'])->name('role_permission.update');
        Route::delete('/role-permission/{notification}', [RolePermissionController::class, 'destroy'])->name('role_permission.destroy'); // ✅ Tambahkan ini!
    });


// ✅ Profile (semua user bisa akses)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
