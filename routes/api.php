<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SettingController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function() {
    Route::get('/server_update', function() {
        return response()->json([ 'message' => 'Create success' ], 201);
    });

    Route::post('/tool_license_check', [SettingController::class, 'tool_license_check']);
    Route::post('/get_shop', [SettingController::class, 'get_shop']);
});
