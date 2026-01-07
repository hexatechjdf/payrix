<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\MappingExtentionController;
use App\Http\Controllers\Api\UploaderController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\IndexController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('webhook/listen/appointment', [WebhookController::class, 'listenAppointment'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
