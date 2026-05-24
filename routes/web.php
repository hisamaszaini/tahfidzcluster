<?php

use App\Http\Controllers\SantriController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Protected Admin/Musyrif Dashboard Routes
Route::middleware('auth')->group(function () {
    
    // Redirect to Dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Advanced Data Santri CRUD & Features
    Route::post('santri/import', [SantriController::class, 'import']);
    Route::post('santri/destroy-bulk', [SantriController::class, 'destroyBulk']);
    Route::resource('santri', SantriController::class);

    // Kriteria
    Route::get('kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');

    // Advanced Data Nilai CRUD & Features
    Route::post('nilai/destroy-bulk', [NilaiController::class, 'destroyBulk']);
    Route::resource('nilai', NilaiController::class);

    // K-Means Processing & Final Dashboard
    Route::get('hasil', [HasilController::class, 'index'])->name('hasil.index');
    Route::get('hasil/proses', [HasilController::class, 'prosesForm'])->name('hasil.proses-form');
    Route::post('hasil/proses', [HasilController::class, 'proses'])->name('hasil.proses');
    Route::get('hasil/cetak', [HasilController::class, 'cetak'])->name('hasil.cetak');

    // Pengaturan Profil Mandiri (Semua User)
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Manajemen Akun CRUD (Khusus Admin)
    Route::resource('user', UserController::class)->except(['show', 'create', 'edit']);
});

