<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Exports\CollaboratorsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 

class CollaboratorController extends Controller
{
    /**
     * Exibe uma lista de colaboradores com filtros e paginação.
     */
    public function index(Request $request)
    {
        // 1. Começa a construir a consulta na tabela 'users'
        $query = User::query();

        // 2. Aplica os filtros se eles existirem na requisição
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('cpf', 'LIKE', "%{$searchTerm}%");
            });
        }

        // 3. Ordena os resultados por nome em ordem alfabética
        $query->orderBy('name', 'asc');

        // 4. Executa a consulta e pagina os resultados (15 por página por padrão)
        $collaborators = $query->paginate(15);

        // 5. Retorna a lista paginada como uma resposta JSON
        return response()->json($collaborators);
    }

    /**
     * Exporta os dados dos colaboradores para um arquivo Excel.
     */
    public function export(Request $request)
    {
        $searchTerm = $request->input('search', null);

        // Define o nome do arquivo que será baixado
        $fileName = 'colaboradores_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Usa a facade do Excel para baixar o arquivo,
        // passando a nossa classe de exportação e o termo de busca.
        return Excel::download(new CollaboratorsExport($searchTerm), $fileName);
    }

    /**
     * Exibe os detalhes de um colaborador específico.
     */
    public function show(User $user)
    {
        // O Laravel automaticamente encontra o usuário pelo ID na URL
        // e o injeta aqui.
        return response()->json($user);
    }

    
    /**
     * Atualiza o perfil de um colaborador.
     */
    public function update(Request $request, User $user)
    {
        $loggedInUser = Auth::user();
        $newRoleId = $request->input('role_id');

        // Validação básica dos dados recebidos
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8', // Senha é opcional
        ]);

        // Lógica de Permissão
        if ($loggedInUser->role->slug === 'gente-e-cultura') {
            $adminRole = Role::where('slug', 'admin')->first();
            if ($newRoleId == $adminRole->id) {
                return response()->json(['message' => 'Permissão negada.'], 403);
            }
        }

        // Atualiza o perfil do usuário
        $user->role_id = $newRoleId;

        // Se uma nova senha foi enviada, atualiza a senha
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil do colaborador atualizado com sucesso!',
            'user' => $user,
        ]);
    }
}