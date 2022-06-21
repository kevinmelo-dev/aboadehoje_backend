<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Http\Request;
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
    Route::post('/auth/verify', [AuthController::class, 'verify']); // Verifica o número de telefone
    Route::post('/auth/logout', [AuthController::class, 'logout']); // Desloga usuário
    Route::post('/auth/update', [AuthController::class, 'update']); // Atualiza informações do usuário

    // Usuários
    Route::get('/{username}', [UserController::class, 'getUserProfile']); // Retorna dados de um usuário

    // Eventos
    Route::get('events/{id}', [\App\Http\Controllers\Api\EventController::class, 'show'])->where('id', '[0-9]+');
    Route::get('events/{method}', [\App\Http\Controllers\Api\EventController::class, 'index']);
    Route::post('events/create', [\App\Http\Controllers\Api\EventController::class, 'store']);
    Route::post('events/edit/{id}', [\App\Http\Controllers\Api\EventController::class, 'update']);
    Route::delete('events/{id}', [\App\Http\Controllers\Api\EventController::class, 'destroy']);

    // Likes
    Route::post('events/like/{id}', [\App\Http\Controllers\Api\LikeController::class, 'index']);
});
