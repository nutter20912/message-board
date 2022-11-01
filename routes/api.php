<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/auth/csrf-cookie', [AuthController::class, 'getCsrfCookie']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::put('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/users', [UserController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->except(['index', 'store']);
});
