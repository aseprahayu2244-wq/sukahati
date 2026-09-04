<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Organization Chart
    Route::get('/organization', [OrganizationController::class, 'index'])->name('organization.index');

    // Document Browser (for all document types)
    Route::get('/docs/{type}', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/docs/{type}/{division}', [DocumentController::class, 'positions'])->name('documents.positions');
    Route::get('/docs/{type}/{division}/{position}', [DocumentController::class, 'files'])->name('documents.files');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // --- Ketua Only -----------------------------------------------------------
    Route::middleware(['role:ketua'])->group(function () {

        // Division management
        Route::get('/admin/divisions', [DivisionController::class, 'index'])->name('admin.divisions.index');
        Route::post('/admin/divisions', [DivisionController::class, 'store'])->name('admin.divisions.store');
        Route::put('/admin/divisions/{division}', [DivisionController::class, 'update'])->name('admin.divisions.update');
        Route::delete('/admin/divisions/{division}', [DivisionController::class, 'destroy'])->name('admin.divisions.destroy');

        // Position management
        Route::get('/admin/positions', [PositionController::class, 'index'])->name('admin.positions.index');
        Route::post('/admin/positions', [PositionController::class, 'store'])->name('admin.positions.store');
        Route::put('/admin/positions/{position}', [PositionController::class, 'update'])->name('admin.positions.update');
        Route::delete('/admin/positions/{position}', [PositionController::class, 'destroy'])->name('admin.positions.destroy');

        // Document CRUD
        Route::get('/admin/documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/admin/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/admin/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/admin/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/admin/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        // Org chart edit
        Route::post('/organization/positions/{position}/set-parent', [OrganizationController::class, 'setParent'])
            ->name('organization.setParent');
    });

    // AJAX: positions by division
    Route::get('/api/divisions/{division}/positions', [PositionController::class, 'byDivision'])
        ->name('api.positions.byDivision');
});

require __DIR__.'/auth.php';
