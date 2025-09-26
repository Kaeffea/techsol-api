<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Invitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth; // <-- A LINHA QUE FALTAVA

class AuthController extends Controller
{
    /**
     * O NOVO MÉTODO DE LOGIN UNIFICADO (aceita CPF ou E-mail)
     */
    public function login(Request $request)
    {
        // 1. Valida a requisição para um campo genérico "login" e a senha
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Descobre se o campo "login" é um e-mail ou outra coisa (CPF)
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'cpf';

        // 3. Monta as credenciais para a tentativa de login
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password
        ];

        // 4. Tenta autenticar usando a ferramenta Auth
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'login' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // 5. Se deu certo, pega o usuário e cria o token
        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login bem-sucedido!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * O MÉTODO DE REGISTRO QUE JÁ EXISTE E ESTÁ FUNCIONANDO
     */
    public function register(Request $request)
{
    // A validação dos dados do formulário permanece a mesma
    $request->validate([
        'token' => 'required|string',
        'name' => 'required|string|max:100',
        'cpf' => 'required|string|size:11|unique:users,cpf',
        'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&].*$/',
    ]);

    // A validação do token permanece a mesma
    $invitation = Invitation::where('token', $request->token)->first();
    if (!$invitation || $invitation->status !== 'pending' || $invitation->expires_at < now()) {
        return response()->json(['message' => 'Token de convite inválido ou expirado.'], 404);
    }

    // Define o perfil padrão para o novo usuário (ex: Aluno)
    $defaultRole = Role::where('slug', 'student')->first();

    // Cria o novo usuário
    $user = User::create([
        'name' => $request->name,
        'email' => $invitation->email,
        'cpf' => $request->cpf,
        'password' => Hash::make($request->password),
        'role_id' => $defaultRole->id,
        
        // A MUDANÇA CRÍTICA ESTÁ AQUI:
        // Copiamos os IDs da organização a partir do registro do convite
        'operational_unit_id' => $invitation->operational_unit_id,
        'regional_department_id' => $invitation->regional_department_id,
    ]);

    // Atualiza o status do convite
    $invitation->status = 'completed';
    $invitation->registered_user_id = $user->id;
    $invitation->save();

    return response()->json(['message' => 'Usuário cadastrado com sucesso!'], 201);
}
}