<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BarangayController;

use App\Http\Controllers\Api\BeaconController;

Route::get('/user', function (Request $request) {
    return $request->user();

    
})->middleware('auth:sanctum');

Route::post('/beacons/{beaconId}/status', [App\Http\Controllers\Api\BeaconStatusController::class, 'update'])
    ->middleware('auth');

    Route::get('/barangays', [BarangayController::class, 'index']);

Route::prefix('Data')
    ->middleware('apikey')
    ->group(function () {

        Route::get('/Beacons', [BeaconController::class, 'getBeacons']);
        Route::get('/BeaconStatus', [BeaconController::class, 'getBeaconStatus']);
        Route::get('/BeaconStatus/summary', [BeaconController::class, 'getStatusSummary']);

    });
