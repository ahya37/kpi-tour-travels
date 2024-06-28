<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ProgramKerjaController;

#All test fitur
Route::get('/marketings/report/monthly', [MarketingController::class, 'reportUmrahBulanan']); 
Route::get('/marketings/pekerjaan/report',[ProgramKerjaController::class, 'reportPekerjaanMarketing']);
// Route::post('/marketings/haji/report/data',[MarketingController::class, 'getReportHaji']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')
    ->post('/test', function () {
        return response()->json('OKE');
});
