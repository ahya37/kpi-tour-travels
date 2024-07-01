<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\NotificationController;

#All test fitur
Route::get('/marketings/report/monthly', [MarketingController::class, 'reportUmrahBulanan']); 
Route::get('/marketings/pekerjaan/report',[ProgramKerjaController::class, 'reportPekerjaanMarketing']);
Route::post('/notifications/alumni/create',[NotificationController::class, 'createNotificationAlumniJamaah']);
Route::get('/notifications/show/user/{userId}',[NotificationController::class, 'showNotificationByUserLogin']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')
    ->post('/test', function () {
        return response()->json('OKE');
});
