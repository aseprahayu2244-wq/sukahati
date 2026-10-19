<?php

use App\Http\Controllers\BodController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (all authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================
    // Document Browser (all authenticated users can view)
    // =========================================================
    // Level 1: BOD list per menu type
    Route::get('/docs/{type}', [DocumentController::class, 'index'])->name('documents.index');
    // Level 2: Department list within BOD
    Route::get('/docs/{type}/{bod}', [DocumentController::class, 'departments'])->name('documents.departments');
    // Level 3: Positions list (job_desk) OR Document list (sop, bpm, struktur)
    Route::get('/docs/{type}/{bod}/{department}', [DocumentController::class, 'positions'])->name('documents.positions');
    // Level 4: Document list within Position (job_desk only)
    Route::get('/docs/{type}/{bod}/{department}/{position}', [DocumentController::class, 'files'])->name('documents.files');

    // Document actions
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // =========================================================
    // Document CRUD (Super Admin + Admin)
    // =========================================================
    Route::middleware(['role:super_admin,admin'])->group(function () {
        Route::get('/admin/documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/admin/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/admin/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/admin/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/admin/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        // Department (Division) management - Super Admin + Admin
        Route::get('/admin/departments', [DivisionController::class, 'index'])->name('admin.divisions.index');
        Route::post('/admin/departments', [DivisionController::class, 'store'])->name('admin.divisions.store');
        Route::put('/admin/departments/{division}', [DivisionController::class, 'update'])->name('admin.divisions.update');
        Route::delete('/admin/departments/{division}', [DivisionController::class, 'destroy'])->name('admin.divisions.destroy');

        // Position (Jabatan) management - Super Admin + Admin
        Route::get('/admin/positions', [PositionController::class, 'index'])->name('admin.positions.index');
        Route::post('/admin/positions', [PositionController::class, 'store'])->name('admin.positions.store');
        Route::put('/admin/positions/{position}', [PositionController::class, 'update'])->name('admin.positions.update');
        Route::delete('/admin/positions/{position}', [PositionController::class, 'destroy'])->name('admin.positions.destroy');
    });

    // =========================================================
    // Super Admin Only Routes
    // =========================================================
    Route::middleware(['role:super_admin'])->group(function () {
        // BOD management
        Route::get('/admin/bods', [BodController::class, 'index'])->name('admin.bods.index');
        Route::post('/admin/bods', [BodController::class, 'store'])->name('admin.bods.store');
        Route::put('/admin/bods/{bod}', [BodController::class, 'update'])->name('admin.bods.update');
        Route::delete('/admin/bods/{bod}', [BodController::class, 'destroy'])->name('admin.bods.destroy');

        // User management
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
        Route::patch('/admin/users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('admin.users.toggle-active');
    });

    // =========================================================
    // AJAX Helpers
    // =========================================================
    Route::get('/api/bods/{bod}/departments', [PositionController::class, 'departmentsByBod'])->name('api.departments.byBod');
    Route::get('/api/departments/{division}/positions', [PositionController::class, 'byDivision'])->name('api.positions.byDivision');
});

require __DIR__.'/auth.php';
