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
        Route::livewire('/product-wizard', 'pages::product-wizard')->name('product-wizard');
        Route::livewire('/tag-input', 'pages::tag-input')->name('tag-input');
        Route::livewire('/infinite-scroll', 'pages::infinite-scroll')->name('infinite-scroll');
        Route::livewire('/notification-center', 'pages::notification-center')->name('notification-center');
        Route::livewire('/dynamic-search', 'pages::dynamic-search')->name('dynamic-search');
    });

require __DIR__.'/settings.php';
