<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketingController;

#All test fitur
Route::get('/marketings/report/monthly', [MarketingController::class, 'reportUmrahBulanan']); 

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')
    ->post('/test', function () {
        return response()->json('OKE');
});
