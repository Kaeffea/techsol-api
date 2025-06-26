<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Invitation;
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

    /**
     * Handle a registration request for a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // 1. Valida os dados recebidos do formulário
        $request->validate([
            'token' => 'required|string',
            'name' => 'required|string|max:100',
            'cpf' => 'required|string|size:14|unique:users,cpf',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&].*$/',
            // Adicione outras validações para celular, CEP, etc. se desejar.
        ]);

        // 2. Procura o convite pelo token
        $invitation = Invitation::where('token', $request->token)->first();

        // 3. Valida o convite
        if (!$invitation || $invitation->status !== 'pending' || $invitation->expires_at < now()) {
            return response()->json(['message' => 'Token de convite inválido ou expirado.'], 404);
        }

        // 4. Busca o ID do perfil "Colaborador Comum"
        $defaultRole = Role::where('slug', 'colaborador')->first();

        // 5. Cria o novo usuário no banco de dados
        $user = User::create([
            'name' => $request->name,
            'email' => $invitation->email, // Pega o e-mail do convite, não do formulário
            'cpf' => $request->cpf,
            'password' => Hash::make($request->password),
            'role_id' => $defaultRole->id,
            // Preenche outros campos que vieram do formulário
            'phone_number' => $request->phone_number,
            'postal_code' => $request->postal_code,
            'street' => $request->street,
            'neighborhood' => $request->neighborhood,
            'city' => $request->city,
            'state' => $request->state,
        ]);

        // 6. Atualiza o status do convite
        $invitation->status = 'completed';
        $invitation->registered_user_id = $user->id;
        $invitation->save();

        // 7. Retorna uma resposta de sucesso
        return response()->json(['message' => 'Usuário cadastrado com sucesso!'], 201);
    }

}