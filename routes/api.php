<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InviteController;
use App\Http\Controllers\Api\CollaboratorController; 

// Rota de Login (Pública)
Route::post('/login', [AuthController::class, 'login']);

// Rota de Registro (Pública, mas validada pelo token no controller)
Route::post('/register', [AuthController::class, 'register']);

// Agrupando rotas que precisam de autenticação
Route::middleware('auth:sanctum')->group(function () {

    // Rota para convidar colaboradores
    Route::post('/invites', [InviteController::class, 'store']);

    // Rota de exemplo para obter dados do usuário logado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rota para listar e pesquisar colaboradores
    Route::get('/collaborators', [CollaboratorController::class, 'index']);
    Route::get('/collaborators/export', [CollaboratorController::class, 'export']);
    Route::get('/collaborators/{user}', [CollaboratorController::class, 'show']);
    Route::put('/collaborators/{user}', [CollaboratorController::class, 'update']); 
});