<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PermissionController;

Route::get('/', function () {
    return view('auth.login');
});

// NEW CODE
//route login index
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');

//route login store
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('guest');
//route logout

Route::group(['middleware' => ['auth']], function () {
    //route dashboard
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class,'logout'])->name('logout.store');

});

// OLD CODE
// Route::get('/login',[LoginController::class,'login'])->name('login');
// Route::post('/login',[LoginController::class,'store'])->name('login.store');

// Route::middleware(['admin'])->prefix('dashboard')->group(function () {
//     Route::get('/',[DashboardController::class,'index'])->name('dashboard');    
//     Route::post('/logout',[LoginController::class,'logout'])->name('logout.store'); 
// });

// Auth::routes();
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
