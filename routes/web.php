<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketingController;
use Illuminate\Support\Facades\Route;

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

    //marketing
    Route::prefix('marketings')->controller(MarketingController::class)->group(function(){
        Route::get('/target','target')->name('marketing.target');
        Route::post('/target','storeTarget')->name('marketing.target.store');
        
        // datatable
        Route::post('/target/list','listTarget');
        
        // detail target marketing
        Route::get('/target/detail/{marketingTargetId}','detailMarketingTarget');
        Route::post('/target/detail/{marketingTargetId}/store','detailMarketingTargetStore');
        Route::post('/target/detail/list/{detailMarketingTargetId}','detailListTarget');
        
        // bahan prospek
        Route::get('/prospectmaterial','prospectMaterial')->name('marketing.prospectmaterial');
        Route::post('/prospectmaterial/store','prospectMaterialStore')->name('marketing.prospectmaterial.store');

        //modal 
        Route::get('modal/target','loadModalMarketingTarget');
        Route::get('modal/target/detail','loadModalDetailMarketingTarget');
    });

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
