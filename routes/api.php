<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
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

// Rotas públicas
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::group(['middleware' => ['auth:sanctum']], function () {

    // Autenticação
    Route::post('/auth/verify', [AuthController::class, 'verify']);
    Route::post('/auth/upload', [AuthController::class, 'upload_picture']);
    Route::put('/auth/update', [AuthController::class, 'update']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Usuários
    Route::get('/{username}', [UserController::class, 'getUserProfile']);

    // Eventos
    Route::get('events/{id}', [\App\Http\Controllers\Api\EventController::class, 'show'])->where('id', '[0-9]+');
    Route::get('events/{method}', [\App\Http\Controllers\Api\EventController::class, 'index']);
    Route::post('events/create', [\App\Http\Controllers\Api\EventController::class, 'store']);
    Route::post('events/upload/{id}', [\App\Http\Controllers\Api\EventController::class, 'upload_image']);
    Route::put('events/edit/{id}', [\App\Http\Controllers\Api\EventController::class, 'update']);
    Route::delete('events/{id}', [\App\Http\Controllers\Api\EventController::class, 'destroy']);

    // Likes
    Route::post('events/like/{id}', [\App\Http\Controllers\Api\LikeController::class, 'index']);
});
