<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/signin',[LoginController::class,'loginpage'])->name('loginpage');
Route::post('/signin/store',[LoginController::class,'login'])->name('loginstore');

Route::middleware(['admin'])->prefix('dashboard')->group(function () {
    Route::get('/',[DashboardController::class,'index'])->name('dashboard');

     //Examlple
    // Route::controller(ExController::class)->group(function(){
    //      Route::post('ex', 'exMethod')->name('ex.store');
    // });

    Route::post('/logoutstore',[LoginController::class,'logout'])->name('logoutstore'); 
});

// Auth::routes();
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
