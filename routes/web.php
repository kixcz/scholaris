<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Module routes
    Route::get('references', function () {
        return Inertia::render('references');
    })->name('references');

    Route::get('notebooks', function () {
        return Inertia::render('notebooks');
    })->name('notebooks');

    Route::get('vocabulary', function () {
        return Inertia::render('vocabulary');
    })->name('vocabulary');

    Route::get('weekly-goals', function () {
        return Inertia::render('weekly-goals');
    })->name('weekly-goals');

    Route::get('roadmaps', function () {
        return Inertia::render('roadmaps');
    })->name('roadmaps');

    Route::get('links', function () {
        return Inertia::render('links');
    })->name('links');

    Route::get('productivity', function () {
        return Inertia::render('productivity');
    })->name('productivity');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
