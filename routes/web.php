<?php

use Illuminate\Support\Facades\Route;

// Redirect root to admin panel
Route::get('/', function () {
    return redirect('/admin');
})->name('home');

require __DIR__.'/settings.php';
