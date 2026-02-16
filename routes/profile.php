<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return redirect()->route('home');
    })->name('profile.edit');

    Route::patch('/profile', function () {
        return redirect()->route('home');
    })->name('profile.update');

    Route::delete('/profile', function () {
        return redirect()->route('home');
    })->name('profile.destroy');
});
