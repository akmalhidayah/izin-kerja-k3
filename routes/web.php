<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPermintaanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserPanelController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\ApproveSikController;
use App\Http\Controllers\User\IzinKerjaController;
use App\Http\Controllers\User\UserNotificationController;
use App\Http\Controllers\User\UploadController;
use App\Http\Controllers\User\JsaController;
use App\Http\Controllers\User\DataKontraktorController;
use App\Http\Controllers\User\WorkingPermit\UmumPermitController;
use App\Http\Controllers\User\WorkingPermit\GasPanasPermitController;
use App\Http\Controllers\User\WorkingPermit\AirPermitController;
use App\Http\Controllers\User\WorkingPermit\KetinggianPermitController;
use App\Http\Controllers\User\WorkingPermit\RuangTertutupPermitController;
use App\Http\Controllers\User\WorkingPermit\PerancahPermitController;
use App\Http\Controllers\User\WorkingPermit\PanasRisikoPermitController;
use App\Http\Controllers\User\WorkingPermit\BebanPermitController;
use App\Http\Controllers\User\WorkingPermit\PenggalianPermitController;
use App\Http\Controllers\User\WorkingPermit\PengangkatanPermitController;


Route::get('/', function () {
    return view('welcome');
});


// ✅ USER & PGO SIDE (step 1, 4, 5 saja untuk PGO)
Route::middleware(['auth', 'verified', 'usertype:user,admin,pgo'])->group(function () {

    // Dashboard User
    Route::get('pengajuan-user/izin-kerja', [IzinKerjaController::class, 'index'])->name('dashboard');

    // ✅ Step 1: Create Notification
    Route::post('/pengajuan-user/izin-kerja/notification', [UserNotificationController::class, 'store'])->name('notification.store');

    // ✅ Step 4: JSA
    Route::post('/user/jsa/store', [JsaController::class, 'store'])->name('jsa.store');
    Route::get('/user/jsa/{id}/edit', [JsaController::class, 'edit'])->name('jsa.edit');
    Route::patch('/user/jsa/{id}', [JsaController::class, 'update'])->name('jsa.update');
    Route::get('/user/jsa/pdf/{notification_id}', [JsaController::class, 'downloadPdf'])->name('jsa.pdf');
    Route::get('/user/jsa/pdf/view/{notification_id}', [JsaController::class, 'showPdf'])->name('jsa.pdf.view');

    // ✅ Step 5: Working Permit (semua jenis)
    Route::post('/user/working-permit/umum/store', [UmumPermitController::class, 'store'])->name('working-permit.umum.store');
    Route::get('/user/permit/umum/preview/{id}', [UmumPermitController::class, 'preview'])->name('working-permit.umum.preview');

    Route::post('/user/working-permit/gaspanas/store', [GasPanasPermitController::class, 'store'])->name('working-permit.gaspanas.store');
    Route::get('/user/permit/gaspanas/preview/{id}', [GasPanasPermitController::class, 'preview'])->name('working-permit.gaspanas.preview');

    Route::post('/working-permit/air/store', [AirPermitController::class, 'store'])->name('working-permit.air.store');
    Route::get('/working-permit/air/preview/{id}', [AirPermitController::class, 'preview'])->name('working-permit.air.preview');

    Route::post('/working-permit/ketinggian/store', [KetinggianPermitController::class, 'store'])->name('working-permit.ketinggian.store');
    Route::get('/user/permit/ketinggian/preview/{id}', [KetinggianPermitController::class, 'preview'])->name('working-permit.ketinggian.preview');

    Route::post('/user/working-permit/ruang-tertutup/store', [RuangTertutupPermitController::class, 'store'])->name('working-permit.ruangtertutup.store');
    Route::get('/user/permit/ruang-tertutup/preview/{id}', [RuangTertutupPermitController::class, 'preview'])->name('working-permit.ruangtertutup.preview');

    Route::post('/user/working-permit/perancah/store', [PerancahPermitController::class, 'store'])->name('working-permit.perancah.store');
    Route::get('/user/permit/perancah/preview/{id}', [PerancahPermitController::class, 'preview'])->name('working-permit.perancah.preview');

    Route::post('/user/working-permit/risiko-panas/store', [PanasRisikoPermitController::class, 'store'])->name('working-permit.risiko-panas.store');
    Route::get('/user/permit/risiko-panas/preview/{id}', [PanasRisikoPermitController::class, 'preview'])->name('working-permit.risiko-panas.preview');

    Route::post('/user/working-permit/beban/store', [BebanPermitController::class, 'store'])->name('working-permit.beban.store');
    Route::get('/user/permit/beban/preview/{id}', [BebanPermitController::class, 'preview'])->name('working-permit.beban.preview');

    Route::post('/user/working-permit/penggalian/store', [PenggalianPermitController::class, 'store'])->name('working-permit.penggalian.store');
    Route::get('/user/permit/penggalian/preview/{id}', [PenggalianPermitController::class, 'preview'])->name('working-permit.penggalian.preview');

    Route::post('/user/working-permit/pengangkatan/store', [PengangkatanPermitController::class, 'store'])->name('working-permit.pengangkatan.store');
    Route::get('/user/permit/pengangkatan/preview/{id}', [PengangkatanPermitController::class, 'preview'])->name('working-permit.pengangkatan.preview');

    // ✅ View PDF SIK
    Route::get('/izin-kerja/sik/pdf/{id}', [\App\Http\Controllers\Admin\AdminPermintaanController::class, 'viewSik'])->name('izin-kerja.sik.pdf');
});

// ✅ Tambahan: user dan admin only (tanpa PGO)
Route::middleware(['auth', 'verified', 'usertype:user,admin'])->group(function () {

    // Step 2: Data Kontraktor
    Route::post('/pengajuan-user/izin-kerja/data-kontraktor/{notification}', [DataKontraktorController::class, 'store'])->name('izin-kerja.data-kontraktor');
    Route::get('/pengajuan-user/izin-kerja/data-kontraktor/pdf/{notification_id}', [DataKontraktorController::class, 'previewPdf'])->name('izin-kerja.data-kontraktor.pdf');

    // Step 3,6,7,8,9,10,13: Upload
    Route::post('/user/upload', [UploadController::class, 'store'])->name('upload.store');
    Route::patch('/user/upload/{id}/status', [UploadController::class, 'updateStatus'])->name('upload.updateStatus');
    Route::delete('/user/upload/{id}', [UploadController::class, 'destroy'])->name('upload.delete');
});


// ✅ ADMIN SIDE (usertype = 'admin' + role = 'Admin' atau 'Super Admin')
Route::middleware(['auth', 'verified', 'usertype:admin', 'role:Admin,Super Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/permintaansik', [AdminDashboardController::class, 'permintaanSIK'])->name('permintaansik');

    Route::post('/permintaansik/{id}/step/{step}/status', [AdminPermintaanController::class, 'updateStatus'])->name('permintaansik.updateStatus');
    Route::get('/permintaansik/{id}', [AdminPermintaanController::class, 'show'])->name('permintaansik.show');
Route::get('/permintaansik/{id}/view-sik', [AdminPermintaanController::class, 'viewSik'])->name('permintaansik.viewSik');
    Route::delete('/permintaansik/{id}/delete-file/{step}', [AdminPermintaanController::class, 'deleteFile'])->name('permintaansik.deleteFile');
    Route::delete('/permintaansik/{id}/data-kontraktor', [AdminPermintaanController::class, 'deleteDataKontraktor'])->name('permintaansik.deleteDataKontraktor');
    Route::delete('/permintaansik/{id}/jsa', [AdminPermintaanController::class, 'deleteJsa'])->name('permintaansik.deleteJsa');
    Route::delete('/permintaansik/{id}/working-permit/{type}', [AdminPermintaanController::class, 'deleteWorkingPermit'])->name('permintaansik.deleteWorkingPermit');
});

// ✅ SUPER ADMIN - Manajemen User Panel
Route::middleware(['auth', 'verified', 'usertype:admin', 'role:Super Admin'])
    ->prefix('admin/userpanel')
    ->name('admin.userpanel.')
    ->group(function () {
        Route::get('/', [UserPanelController::class, 'index'])->name('index');
        Route::get('/create', [UserPanelController::class, 'create'])->name('create');
        Route::post('/store', [UserPanelController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserPanelController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [UserPanelController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserPanelController::class, 'destroy'])->name('destroy');
    });

// ✅ SUPER ADMIN - Role Permission & Approve SIK
Route::middleware(['auth', 'verified', 'usertype:admin', 'role:Super Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Role Permission
        Route::get('/role-permission', [RolePermissionController::class, 'index'])->name('role_permission.index');
        Route::get('/role-permission/{notification}/edit', [RolePermissionController::class, 'edit'])->name('role_permission.edit');
        Route::patch('/role-permission/{notification}', [RolePermissionController::class, 'update'])->name('role_permission.update');
        Route::delete('/role-permission/{notification}', [RolePermissionController::class, 'destroy'])->name('role_permission.destroy');

        // ✅ Tambahan route untuk Approve SIK
        Route::get('/approvesik', [ApproveSikController::class, 'index'])->name('approvesik.index');
Route::post('/approvesik/{id}/ttd', [ApproveSikController::class, 'storeSignature'])->name('approvesik.signature');
Route::post('/approvesik/{id}/ttd-manager', [ApproveSikController::class, 'storeManagerSignature'])->name('approvesik.signature_manager');


    });

// ✅ Profile (semua user bisa akses)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/permintaansik/{id}/download-sik', [AdminPermintaanController::class, 'downloadSik'])->name('permintaansik.downloadSik');

// Route untuk form kontraktor dengan token
Route::get('/izin-kerja/data-kontraktor/{token}', [DataKontraktorController::class, 'showByToken'])->name('izin-kerja.data-kontraktor.token');
Route::post('/izin-kerja/data-kontraktor/{token}', [DataKontraktorController::class, 'storeByToken'])->name('izin-kerja.data-kontraktor.store');

Route::get('/izin-kerja/jsa/form/{token}', [JsaController::class, 'showByToken'])->name('jsa.form.token');
Route::post('/izin-kerja/jsa/form/{token}', [JsaController::class, 'storeByToken'])->name('jsa.form.token.store');

Route::get('/izin-kerja/working-permit/umum/{token}', [UmumPermitController::class, 'showByToken'])->name('working-permit.umum.token');
Route::post('/izin-kerja/working-permit/umum/{token}', [UmumPermitController::class, 'storeByToken'])->name('working-permit.umum.token.store');

// ✅ Route Form Token untuk Permit Gas
Route::get('/izin-kerja/working-permit/gaspanas/{token}', [GasPanasPermitController::class, 'showByToken'])->name('working-permit.gaspanas.token');
Route::post('/izin-kerja/working-permit/gaspanas/{token}', [GasPanasPermitController::class, 'storeByToken'])->name('working-permit.gaspanas.token.store');

// ✅ Route Form Token untuk Permit Air
Route::get('/izin-kerja/working-permit/air/{token}', [AirPermitController::class, 'showByToken'])->name('working-permit.air.token');
Route::post('/izin-kerja/working-permit/air/{token}', [AirPermitController::class, 'storeByToken'])->name('working-permit.air.token.store');

// ✅ Route Form Token untuk Permit Ketinggian
Route::get('/izin-kerja/working-permit/ketinggian/{token}', [KetinggianPermitController::class, 'showByToken'])->name('working-permit.ketinggian.token');
Route::post('/izin-kerja/working-permit/ketinggian/{token}', [KetinggianPermitController::class, 'storeByToken'])->name('working-permit.ketinggian.token.store');

// ✅ Route Form Token untuk Permit Ruang Tertutup
Route::get('/izin-kerja/working-permit/ruang-tertutup/{token}', [RuangTertutupPermitController::class, 'showByToken'])->name('working-permit.ruangtertutup.token');
Route::post('/izin-kerja/working-permit/ruang-tertutup/{token}', [RuangTertutupPermitController::class, 'storeByToken'])->name('working-permit.ruangtertutup.token.store');

// ✅ Route Form Token untuk Permit Perancah
Route::get('/izin-kerja/working-permit/perancah/{token}', [PerancahPermitController::class, 'showByToken'])->name('working-permit.perancah.token');
Route::post('/izin-kerja/working-permit/perancah/{token}', [PerancahPermitController::class, 'storeByToken'])->name('working-permit.perancah.token.store');

// ✅ Route Form Token untuk Permit Risiko Panas
Route::get('/izin-kerja/working-permit/risiko-panas/{token}', [PanasRisikoPermitController::class, 'showByToken'])->name('working-permit.risiko-panas.token');
Route::post('/izin-kerja/working-permit/risiko-panas/{token}', [PanasRisikoPermitController::class, 'storeByToken'])->name('working-permit.risiko-panas.token.store');

// ✅ Route Form Token untuk Permit Beban
Route::get('/izin-kerja/working-permit/beban/{token}', [BebanPermitController::class, 'showByToken'])->name('working-permit.beban.token');
Route::post('/izin-kerja/working-permit/beban/{token}', [BebanPermitController::class, 'storeByToken'])->name('working-permit.beban.token.store');

Route::get('/izin-kerja/working-permit/penggalian/{token}', [PenggalianPermitController::class, 'showByToken'])->name('working-permit.penggalian.token');
Route::post('/izin-kerja/working-permit/penggalian/{token}', [PenggalianPermitController::class, 'storeByToken'])->name('working-permit.penggalian.token.store');

Route::get('/izin-kerja/working-permit/pengangkatan/{token}', [PengangkatanPermitController::class, 'showByToken'])->name('working-permit.pengangkatan.token');
Route::post('/izin-kerja/working-permit/pengangkatan/{token}', [PengangkatanPermitController::class, 'storeByToken'])->name('working-permit.pengangkatan.token.store');
require __DIR__.'/auth.php';
