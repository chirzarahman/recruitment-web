<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Candidate\ApplicationController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HRD\ApplicationController as HRDApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HRD\JobVacancyController as HrdJobVacancyController;
use App\Http\Controllers\HRD\PsychotestQuestionController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\PsychotestController;

// ... route lainnya

// == AUTHENTICATION ROUTES ==

// Registrasi
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// == APPLICATION ROUTES (DESTINATIONS) ==

Route::middleware(['auth', 'role:manager,hrd'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Manager Routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/laporan', [DashboardController::class, 'laporan'])->name('laporan');
    Route::get('/pelamar', [HRDApplicationController::class, 'index'])->name('applications.index');
    Route::get('/pelamar/lamaran/{application}', [HRDApplicationController::class, 'getDocuments'])->name('applications.ambilDokumen');
});

// HRD Routes
Route::middleware(['auth', 'role:hrd'])->prefix('hrd')->name('hrd.')->group(function () {
    // Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


    Route::resource('lowongan', HrdJobVacancyController::class)->names('job_vacancies');

    // Pelamar
    Route::get('/pelamar/lamaran/{application}', [HRDApplicationController::class, 'getDocuments'])->name('applications.ambilDokumen');
    Route::get('/pelamar', [HRDApplicationController::class, 'index'])->name('applications.index');
    Route::patch('/pelamar/{application}/update-status', [HRDApplicationController::class, 'updateStatus'])->name('applications.updateStatus');

    // Soal Psikotes
    Route::resource('soal-psikotes', PsychotestQuestionController::class)->names('psychotest_questions');
});

// Candidate Routes
Route::middleware(['auth', 'role:candidate'])->group(function () {
    Route::get('/kandidat/profil', [CandidateController::class, 'profile'])->name('candidate.profile');

    // Edit Profile
    Route::get('/kandidat/profil/edit', [CandidateProfileController::class, 'edit'])->name('candidate.profile.edit');
    Route::put('/kandidat/profil', [CandidateProfileController::class, 'update'])->name('candidate.profile.update');

    // Upload CV
    Route::get('/kandidat/profil/upload-cv', [CandidateProfileController::class, 'editResume'])->name('candidate.resume.edit');
    Route::patch('/kandidat/profil/upload-cv', [CandidateProfileController::class, 'updateResume'])->name('candidate.resume.update');

    // Lowongan Pekerjaan
    Route::get('/lowongan-pekerjaan', [JobVacancyController::class, 'index'])->name('job_vacancies.index');
    Route::get('/lowongan-pekerjaan/{jobVacancy}', [JobVacancyController::class, 'show'])->name('job_vacancies.show');
    Route::post('/lowongan-pekerjaan/{jobVacancy}/lamar', [JobVacancyController::class, 'apply'])
        ->middleware(['auth', 'role:candidate'])
        ->name('job_vacancies.apply');

    // Lamaran
    Route::get('/kandidat/lamaran/{application}', [ApplicationController::class, 'show'])->name('candidate.applications.show');
    Route::post('/kandidat/lamaran/{application}/dokumen', [ApplicationController::class, 'storeDocument'])->name('candidate.applications.storeDocument');

    // Psikotes
    Route::get('/tes-psikotes/{application}', [PsychotestController::class, 'show'])->name('psychotest.show');
    Route::post('/tes-psikotes/{application}', [PsychotestController::class, 'store'])->name('psychotest.store');
});