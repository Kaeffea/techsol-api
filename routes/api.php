<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InviteController; 

// Rota de Login (Pública)
Route::post('/login', [AuthController::class, 'login']);

// Agrupando rotas que precisam de autenticação
Route::middleware('auth:sanctum')->group(function () {

    // Rota para convidar colaboradores
    Route::post('/invites', [InviteController::class, 'store']);

    // Rota de exemplo para obter dados do usuário logado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});