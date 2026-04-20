<?php

use App\Http\Controllers\FailController;
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
    Route::get('/no-rujukan/create', [NoRujukanController::class, 'create'])->name('no-rujukan.create');
    Route::post('/no-rujukan', [NoRujukanController::class, 'store'])->name('no-rujukan.store');
    Route::delete('/no-rujukan/delete', [NoRujukanController::class, 'destroy'])->name('no-rujukan.destroy');
    Route::get('/no-rujukan/csv-template', [NoRujukanController::class, 'csvTemplate'])->name('no-rujukan.csv-template');
    Route::post('/no-rujukan/csv-import', [NoRujukanController::class, 'csvImport'])->name('no-rujukan.csv-import');

    // Fail
    Route::get('/fail', [FailController::class, 'index'])->name('fail.index');
    Route::get('/fail/create', [FailController::class, 'create'])->name('fail.create');
    Route::post('/fail', [FailController::class, 'store'])->name('fail.store');
    Route::get('/fail/{fail}/edit', [FailController::class, 'edit'])->name('fail.edit');
    Route::patch('/fail/{fail}', [FailController::class, 'update'])->name('fail.update');
    Route::delete('/fail/delete', [FailController::class, 'destroy'])->name('fail.destroy');
    Route::get('/fail/csv-template', [FailController::class, 'csvTemplate'])->name('fail.csv-template');
    Route::post('/fail/csv-import', [FailController::class, 'csvImport'])->name('fail.csv-import');

    // Pemisahan
    Route::get('/pemisahan', [PemisahanController::class, 'index'])->name('pemisahan.index');
    Route::get('/pemisahan/{pemisahan}/edit', [PemisahanController::class, 'edit'])->name('pemisahan.edit');
    Route::patch('/pemisahan/{pemisahan}', [PemisahanController::class, 'update'])->name('pemisahan.update');
    Route::get('/pemisahan/print-pemisahan', [PemisahanController::class, 'printPemisahan'])->name('pemisahan.print-pemisahan');
    Route::get('/pemisahan/print-pentadbiran', [PemisahanController::class, 'printPentadbiran'])->name('pemisahan.print-pentadbiran');
    Route::get('/pemisahan/print-staf', [PemisahanController::class, 'printStaf'])->name('pemisahan.print-staf');
    Route::get('/pemisahan/print-pelajar', [PemisahanController::class, 'printPelajar'])->name('pemisahan.print-pelajar');

    // Pelupusan
    Route::get('/pelupusan', [PelupusanController::class, 'index'])->name('pelupusan.index');
    Route::patch('/pelupusan/{pelupusan}/status', [PelupusanController::class, 'updateStatus'])->name('pelupusan.update-status');
    Route::patch('/pelupusan/kotak-status', [PelupusanController::class, 'updateKotakStatus'])->name('pelupusan.kotak-status');
    Route::post('/pelupusan/{pelupusan}/lupus', [PelupusanController::class, 'lupus'])->name('pelupusan.lupus');
    Route::post('/pelupusan/lupus-kotak', [PelupusanController::class, 'lupusKotak'])->name('pelupusan.lupus-kotak');
    Route::delete('/pelupusan/selepas-kotak', [PelupusanController::class, 'destroySelepasKotak'])->name('pelupusan.selepas-kotak');
    Route::get('/pelupusan/print', [PelupusanController::class, 'printPelupusan'])->name('pelupusan.print');

    // Users (superadmin only)
    Route::middleware('superadmin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/users/{user}/edit-position', [UserController::class, 'editPosition'])->name('users.edit-position');
        Route::patch('/users/{user}/position', [UserController::class, 'updatePosition'])->name('users.update-position');
    });
});

require __DIR__.'/auth.php';
