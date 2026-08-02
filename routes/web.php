<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});
Route::middleware(['auth', 'verified'])
    ->prefix('/ui')
    ->name('ui.')
    ->group(function () {
        Route::livewire('/meeting-notes', 'pages::meeting-notes')->name('meeting-notes');
        Route::livewire('/profile-settings', 'pages::profile-settings')->name('profile-settings');
    });

require __DIR__.'/settings.php';
