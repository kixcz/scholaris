<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotebookController;
use App\Http\Controllers\PillarController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\VocabularyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pillars Module
    Route::resource('pillars', PillarController::class)->except(['show', 'edit', 'create']);

    // Notebooks Module
    Route::resource('notebooks', NotebookController::class)->except(['show', 'edit', 'create']);

    // Vocabulary Module
    Route::resource('vocabulary', VocabularyController::class)->except(['show', 'edit', 'create']);

    // Roadmaps Module
    Route::resource('roadmaps', RoadmapController::class)->except(['show', 'edit', 'create']);

    // Module routes
    Route::get('references', function () {
        return Inertia::render('references');
    })->name('references');

    Route::get('weekly-goals', function () {
        return Inertia::render('weekly-goals');
    })->name('weekly-goals');

    Route::get('links', function () {
        return Inertia::render('links');
    })->name('links');

    Route::get('productivity', function () {
        return Inertia::render('productivity');
    })->name('productivity');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
