<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LulusanController;
use App\Http\Controllers\PenggunaLulusanController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::post('/access-survey', [SurveyController::class, 'verifyCode'])->name('survey.access');
Route::get('/fill-survey/{code}', [SurveyController::class, 'fill'])->name('survey.fill');
Route::post('/submit-survey/{code}', [SurveyController::class, 'submitJawaban'])->name('survey.submit');

Route::middleware('auth')->group(function () {

    // --- AKSES SEMUA ROLE (Admin & User Reguler) ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- KHUSUS ROLE ADMIN ---
    // Ganti 'admin' sesuai dengan 'code' yang ada di tabel roles Anda
    Route::middleware(['role:admin'])->group(function () {

        // Survey
        Route::get('/survey', [SurveyController::class, 'index'])->name('survey');
        Route::get('/addsurvey', [SurveyController::class, 'add'])->name('addsurvey');
        Route::post('/survey/store', [SurveyController::class, 'store'])->name('survey.store');
        Route::get('/survey/bulk', [SurveyController::class, 'bulkCreate'])->name('survey.bulk');
        Route::post('/survey/bulk', [SurveyController::class, 'bulkStore'])->name('survey.bulk.store');
        Route::get('/survey/lulusan-by-tahun', [SurveyController::class, 'getLulusanByTahun'])->name('survey.lulusan-by-tahun');
        Route::get('/get-perusahaan/{id}', [SurveyController::class, 'getPerusahaanData']);
        Route::get('/survey/{id}/edit', [SurveyController::class, 'edit'])->name('survey.edit');
        Route::put('/survey/{id}', [SurveyController::class, 'update'])->name('survey.update');

        // Lulusan
        Route::get('/lulusan', [LulusanController::class, 'index'])->name('lulusan');
        Route::get('/addgrad', [LulusanController::class, 'add'])->name('addgrad');
        Route::post('/lulusan.store', [LulusanController::class, 'store'])->name('lulusan.store');

        // Pengguna Lulusan
        Route::get('/penggunalulusan', [PenggunaLulusanController::class, 'index'])->name('penggunalulusan');
        Route::get('/create', [PenggunaLulusanController::class, 'create'])->name('create');
        Route::post('/pengguna.store', [PenggunaLulusanController::class, 'store'])->name('pengguna.store');
        Route::get('/penggunalulusan/{id}/edit', [PenggunaLulusanController::class, 'edit'])->name('penggunalulusan.edit');
        Route::put('/penggunalulusan/{id}', [PenggunaLulusanController::class, 'update'])->name('penggunalulusan.update');
        Route::delete('/penggunalulusan/{id}', [PenggunaLulusanController::class, 'destroy'])->name('penggunalulusan.destroy');

        // Kategori
        Route::resource('kategori', \App\Http\Controllers\KategoriController::class)->except(['show']);

        // Report
        Route::get('/report', [ReportController::class, 'index'])->name('report');
        Route::get('/report/download', [ReportController::class, 'download'])->name('report.download');

        // Pertanyaan
        Route::get('/pertanyaan', [PertanyaanController::class, 'index'])->name('pertanyaan');
        Route::get('/addquestion', [PertanyaanController::class, 'add'])->name('addquestion');
        Route::get('/pertanyaan/{id}/edit', [PertanyaanController::class, 'edit'])->name('pertanyaan.edit');
        Route::get('/pertanyaan/{id}/switch', [PertanyaanController::class, 'switch'])->name('pertanyaan.switch');
        Route::put('/pertanyaan/{id}', [PertanyaanController::class, 'update'])->name('pertanyaan.update');
        Route::post('/savequestion', [PertanyaanController::class, 'store'])->name('savequestion');

    });
});

require __DIR__ . '/auth.php';