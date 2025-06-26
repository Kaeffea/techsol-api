<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

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
}