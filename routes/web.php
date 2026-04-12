<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LulusanController;
use App\Http\Controllers\PenggunaLulusanController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/survey', [SurveyController::class, 'index'])->name('survey');
Route::get('/addsurvey', [SurveyController::class, 'add'])->name('addsurvey');
Route::post('/survey/store', [SurveyController::class, 'store'])->name('survey.store');
Route::get('/lulusan', [LulusanController::class, 'index'])->name('lulusan');
Route::get('/addgrad', [LulusanController::class, 'add'])->name('addgrad');
Route::get('/penggunalulusan', [PenggunaLulusanController::class, 'index'])->name('penggunalulusan');
Route::get('/pertanyaan', [PertanyaanController::class, 'index'])->name('pertanyaan');
Route::get('/addquestion', [PertanyaanController::class, 'add'])->name('addquestion');
Route::get('/pertanyaan/{id}/edit', [PertanyaanController::class, 'edit'])->name('pertanyaan.edit');
Route::get('/pertanyaan/{id}/switch', [PertanyaanController::class, 'switch'])->name('pertanyaan.switch');
Route::put('/pertanyaan/{id}', [PertanyaanController::class, 'update'])->name('pertanyaan.update');
Route::post('/savequestion', [PertanyaanController::class, 'store'])->name('savequestion');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';