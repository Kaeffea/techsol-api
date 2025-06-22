<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Rota de Login (Pública)
Route::post('login', [AuthController::class, 'login']);

// Esta rota de exemplo pode ser útil para testar se o token funciona
// Qualquer um que tentar acessar /api/user sem um token válido será bloqueado
Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return $request->user();
});

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/
