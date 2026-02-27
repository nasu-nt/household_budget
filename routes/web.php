<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route::get('/', function () {
//     return Auth::check()
//         ? redirect()->route('dashboard')
//         : view('auth.login');
// })->name('login');
// 
// Route::middleware('auth')->group(function () {
//     Route::view('/dashboard', 'dashboard.index')->name('dashboard');
// });


Route::view('/', 'auth.login');
Route::view('/login', 'auth.login')->name('login');

//Route::view('/register', 'auth.register')->name('register');
//Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

