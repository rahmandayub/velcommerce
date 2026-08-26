<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

// Admin placeholder — will be expanded in Fase 2 with product CRUD etc.
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::inertia('/', 'dashboard')->name('dashboard');
    Route::inertia('products', 'dashboard')->name('products.index');
});

require __DIR__.'/settings.php';
