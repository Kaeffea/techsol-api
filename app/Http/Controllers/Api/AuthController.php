<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // 1. Valida se a requisição tem os campos 'cpf' e 'password'
        $request->validate([
            'cpf' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Busca o usuário pelo CPF
        $user = User::where('cpf', $request->cpf)->first();

        // 3. Verifica se o usuário existe E se a senha está correta
        // Apenas usuários com senha cadastrada (Admin/RH) poderão logar
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // Se falhar, lança um erro padrão de autenticação
            throw ValidationException::withMessages([
                'cpf' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }
        
        // 4. Se a autenticação for bem-sucedida, cria um token de acesso
        $token = $user->createToken('auth-token')->plainTextToken;

        // 5. Retorna uma resposta JSON com o token e os dados do usuário
        return response()->json([
            'message' => 'Login bem-sucedido!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
}