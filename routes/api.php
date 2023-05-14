<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\NewsController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function () {
    Route::post('in', [AuthenticationController::class, 'login']);
    Route::post('register', [AuthenticationController::class, 'register']);
    Route::get('out', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
});

Route::prefix('account')->middleware('auth:sanctum')->group(function () {
    Route::get('me', [AccountController::class, 'me']);
    Route::post('change-password', [AccountController::class, 'changePassword']);
    Route::post('change-news-settings', [AccountController::class, 'changeNewsSettings']);
    Route::post('change-account-settings', [AccountController::class, 'changeAccountSettings']);
});
