<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OperationalUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationalUnitController extends Controller
{
    // Método para listar todas as Unidades Operacionais
    public function index()
    {
        $user = Auth::user();

        // Admin Nacional vê todas as UOs
        if ($user->role->slug === 'national_admin') {
            return OperationalUnit::orderBy('name')->get();
        }

        // Admin Regional vê apenas as UOs do seu DR
        if ($user->role->slug === 'regional_admin') {
            return OperationalUnit::where('regional_department_id', $user->regional_department_id)
                ->orderBy('name')
                ->get();
        }

        return response()->json(['message' => 'Acesso não autorizado.'], 403);
    }

    // Método para criar uma nova Unidade Operacional
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Apenas Admin Nacional e Regional podem criar UOs
        if (!in_array($user->role->slug, ['national_admin', 'regional_admin'])) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'regional_department_id' => 'required|exists:regional_departments,id',
        ]);

        // Se quem cria é um Admin Regional, força a criação dentro do seu próprio DR
        if ($user->role->slug === 'regional_admin') {
            if ($validatedData['regional_department_id'] != $user->regional_department_id) {
                return response()->json(['message' => 'Permissão para criar UO apenas no seu próprio Departamento Regional.'], 403);
            }
        }

        $unit = OperationalUnit::create($validatedData);

        return response()->json($unit, 201);
    }
}