<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AddDataController;
use App\Http\Controllers\DetailOrderPSBController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\TimApiController;
use App\Http\Controllers\TimTeknisiController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\RekapPhotoController;

Route::get('/', fn () => view('splash'));

// Auth
Auth::routes();

// Protected
Route::middleware(['auth'])->group(function () {

    /* ===================== HOME (Dashboard) ===================== */
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    /* ===================== Add Data ===================== */
    Route::middleware('role:Super Admin,HD TA')->group(function () {
        Route::get('/add-data',  [AddDataController::class, 'create'])->name('add.data');
        Route::post('/add-data', [AddDataController::class, 'store'])->name('add.data.store');

        // (opsional demo)
        Route::get('/data/create', fn () => view('data.create'))->name('data.create');
    });

    /* ===================== Logout ===================== */
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');

    /* ===================== Detail Order PSB ===================== */

    // Quick Update (popup) — semua role boleh quick edit
    Route::patch('detail-order-psb/{order}/quick-update', [DetailOrderPSBController::class, 'quickUpdate'])
        ->name('detail-order-psb.quick-update')
        ->middleware('role:Super Admin,HD TA,HD Mitra,Team Leader');

    Route::prefix('detail-order-psb')->name('detail-order-psb.')->group(function () {

        // LIST — semua role
        Route::get('/', [DetailOrderPSBController::class, 'index'])
            ->name('index')
            ->middleware('role:Super Admin,HD TA,HD Mitra,Team Leader');

        // Create/Store/Edit/Update/Add Status/Import/Export — admin & HD TA
        Route::middleware('role:Super Admin,HD TA')->group(function () {
            Route::get('/create', [DetailOrderPSBController::class, 'create'])->name('create');
            Route::post('/',       [DetailOrderPSBController::class, 'store'])->name('store');

            Route::get('/{psb}/edit', [DetailOrderPSBController::class, 'edit'])->name('edit');
            Route::put('/{psb}',      [DetailOrderPSBController::class, 'update'])->name('update');

            Route::post('/{psb}/add-status', [DetailOrderPSBController::class, 'addStatus'])->name('add-status');

            Route::get('/import',          [DetailOrderPSBController::class, 'importForm'])->name('import.form');
            Route::post('/import',         [DetailOrderPSBController::class, 'importStore'])->name('import.store');
            Route::get('/import/template', [DetailOrderPSBController::class, 'downloadTemplate'])->name('import.template');

            Route::get('/export/csv', [DetailOrderPSBController::class, 'exportCsv'])->name('export.csv');

            Route::post('/dup-check', [DetailOrderPSBController::class, 'dupCheck'])->name('dup-check');
        });

        // Destroy — khusus Super Admin
        Route::delete('/{psb}', [DetailOrderPSBController::class, 'destroy'])
            ->name('destroy')
            ->middleware('role:Super Admin');

        // Mapping territory (semua role)
        Route::get('/territory-options', [DetailOrderPSBController::class, 'territoryOptions'])
            ->name('territory-options')
            ->middleware('role:Super Admin,HD TA,HD Mitra,Team Leader');
    });

    /* ===================== Presensi ===================== */
    // HANYA Super Admin + Team Leader (HD TA di-off)
    Route::middleware('role:Super Admin,Team Leader')->group(function () {
        Route::get('/presensi/checkin', [PresensiController::class, 'create'])->name('presensi.checkin');
        Route::post('/presensi',        [PresensiController::class, 'store'])->name('presensi.store');
    });

    /* ===================== Page Teknisi (CRUD Tim) ===================== */
    // HANYA Super Admin + Team Leader (HD TA di-off)
    Route::prefix('teknisi')->name('teknisi.')
        ->middleware('role:Super Admin,Team Leader')
        ->group(function () {
            Route::get('/',               [TimTeknisiController::class, 'index'])->name('index');
            Route::get('/create',         [TimTeknisiController::class, 'create'])->name('create');
            Route::post('/',              [TimTeknisiController::class, 'store'])->name('store');
            Route::get('/{teknisi}/edit', [TimTeknisiController::class, 'edit'])->name('edit');
            Route::put('/{teknisi}',      [TimTeknisiController::class, 'update'])->name('update');
            Route::delete('/{teknisi}',   [TimTeknisiController::class, 'destroy'])->name('destroy');
        });

    /* ===================== Registrasi Teknisi (master teknisi) ===================== */
    // HANYA Super Admin + Team Leader (HD TA di-off)
    Route::prefix('registrasi-teknisi')->name('registrasi-teknisi.')
        ->middleware('role:Super Admin,Team Leader')
        ->group(function () {
            Route::get('/suggest', [TeknisiController::class, 'suggest'])->name('suggest');
            Route::get('/',               [TeknisiController::class, 'index'])->name('index');
            Route::get('/create',         [TeknisiController::class, 'create'])->name('create');
            Route::post('/',              [TeknisiController::class, 'store'])->name('store');
            Route::get('/{teknisi}/edit', [TeknisiController::class, 'edit'])->name('edit');
            Route::put('/{teknisi}',      [TeknisiController::class, 'update'])->name('update');
            Route::delete('/{teknisi}',   [TeknisiController::class, 'destroy'])->name('destroy');
        });

    /* ===================== API Tim by STO (JSON) ===================== */
    Route::get('/teams/by-sto', [TimApiController::class, 'teamsBySto'])->name('api.teams.by-sto');

    /* ===================== Rekap Foto ===================== */
    // Semua role boleh LIHAT galeri (index)
    Route::get('/rekap-foto', [RekapPhotoController::class,'index'])
        ->name('rekap-foto.index')
        ->middleware('role:Super Admin,HD TA,HD Mitra,Team Leader');

    // Hanya Super Admin + Team Leader boleh upload & hapus
    Route::middleware('role:Super Admin,Team Leader')->group(function () {
        Route::get('/rekap-foto/create', [RekapPhotoController::class,'create'])->name('rekap-foto.create');
        Route::post('/rekap-foto',        [RekapPhotoController::class,'store'])->name('rekap-foto.store');
        Route::delete('/rekap-foto/{photo}', [RekapPhotoController::class,'destroy'])->name('rekap-foto.destroy');
    });

    // Dev seed
    Route::get('/dev/seed-teams',     [TimApiController::class, 'seedAll'])->name('dev.seed.teams');
    Route::get('/dev/seed-teams-sto', [TimApiController::class, 'seedSto'])->name('dev.seed.teams.sto');
});
