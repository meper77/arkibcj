<?php

use App\Http\Controllers\AvailableFakultiBahagianController;
use App\Http\Controllers\FailController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\NoRujukanController;
use App\Http\Controllers\PelupusanController;
use App\Http\Controllers\PemisahanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('no-rujukan.index')
        : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/transfer-position', [ProfileController::class, 'transferPosition'])->name('profile.transfer-position');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // No Rujukan
    Route::get('/no-rujukan', [NoRujukanController::class, 'index'])->name('no-rujukan.index');
    Route::get('/no-rujukan/xlsx-template', [NoRujukanController::class, 'xlsxTemplate'])->name('no-rujukan.xlsx-template');
    Route::middleware('write')->group(function () {
        Route::get('/no-rujukan/create', [NoRujukanController::class, 'create'])->name('no-rujukan.create');
        Route::post('/no-rujukan', [NoRujukanController::class, 'store'])->name('no-rujukan.store');
        Route::delete('/no-rujukan/delete', [NoRujukanController::class, 'destroy'])->name('no-rujukan.destroy');
        Route::post('/no-rujukan/xlsx-import', [NoRujukanController::class, 'xlsxImport'])->name('no-rujukan.xlsx-import');
    });

    // Fail
    Route::get('/fail', [FailController::class, 'index'])->name('fail.index');
    Route::get('/fail/csv-template', [FailController::class, 'csvTemplate'])->name('fail.csv-template');
    Route::post('/fail/print', [FailController::class, 'print'])->name('fail.print');
    Route::middleware('write')->group(function () {
        Route::get('/fail/create', [FailController::class, 'create'])->name('fail.create');
        Route::post('/fail', [FailController::class, 'store'])->name('fail.store');
        Route::get('/fail/{fail}/edit', [FailController::class, 'edit'])->name('fail.edit');
        Route::patch('/fail/{fail}', [FailController::class, 'update'])->name('fail.update');
        Route::delete('/fail/delete', [FailController::class, 'destroy'])->name('fail.destroy');
        Route::post('/fail/csv-import', [FailController::class, 'csvImport'])->name('fail.csv-import');
    });

    // Pemisahan
    Route::get('/pemisahan', [PemisahanController::class, 'index'])->name('pemisahan.index');
    Route::get('/pemisahan/print-pemisahan', [PemisahanController::class, 'printPemisahan'])->name('pemisahan.print-pemisahan');
    Route::get('/pemisahan/print-pentadbiran', [PemisahanController::class, 'printPentadbiran'])->name('pemisahan.print-pentadbiran');
    Route::get('/pemisahan/print-staf', [PemisahanController::class, 'printStaf'])->name('pemisahan.print-staf');
    Route::get('/pemisahan/print-pelajar', [PemisahanController::class, 'printPelajar'])->name('pemisahan.print-pelajar');
    Route::middleware('write')->group(function () {
        Route::get('/pemisahan/{pemisahan}/edit', [PemisahanController::class, 'edit'])->name('pemisahan.edit');
        Route::patch('/pemisahan/{pemisahan}', [PemisahanController::class, 'update'])->name('pemisahan.update');
    });

    // Pelupusan
    Route::get('/pelupusan', [PelupusanController::class, 'index'])->name('pelupusan.index');
    Route::get('/pelupusan/print', [PelupusanController::class, 'printPelupusan'])->name('pelupusan.print');
    Route::middleware('write')->group(function () {
        Route::patch('/pelupusan/{pelupusan}/status', [PelupusanController::class, 'updateStatus'])->name('pelupusan.update-status');
        Route::patch('/pelupusan/kotak-status', [PelupusanController::class, 'updateKotakStatus'])->name('pelupusan.kotak-status');
        Route::post('/pelupusan/{pelupusan}/lupus', [PelupusanController::class, 'lupus'])->name('pelupusan.lupus');
        Route::post('/pelupusan/lupus-kotak', [PelupusanController::class, 'lupusKotak'])->name('pelupusan.lupus-kotak');
        Route::delete('/pelupusan/selepas-kotak', [PelupusanController::class, 'destroySelepasKotak'])->name('pelupusan.selepas-kotak');
    });

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::middleware('superadmin')->group(function () {
        Route::delete('/history/{history}', [HistoryController::class, 'destroy'])->name('history.destroy');
    });

    // Users (superadmin only)
    Route::middleware('superadmin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/users/{user}/edit-position', [UserController::class, 'editPosition'])->name('users.edit-position');
        Route::patch('/users/{user}/position', [UserController::class, 'updatePosition'])->name('users.update-position');
        Route::patch('/users/{user}/fakulti', [UserController::class, 'updateFakulti'])->name('users.update-fakulti');

        Route::prefix('pengurusan/fakulti')->name('pengurusan.fakulti.')->group(function () {
            Route::get('/', [AvailableFakultiBahagianController::class, 'index'])->name('index');
            Route::post('/', [AvailableFakultiBahagianController::class, 'store'])->name('store');
            Route::patch('/{fakulti}/permissions', [AvailableFakultiBahagianController::class, 'updatePermissions'])->name('permissions');
            Route::delete('/{fakulti}', [AvailableFakultiBahagianController::class, 'destroy'])->name('destroy');
        });
    });
});

require __DIR__.'/auth.php';
