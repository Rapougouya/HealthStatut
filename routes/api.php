<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VitalSignsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/patients/{patient}/vital-signs', [VitalSignsController::class, 'getPatientData']);
});
