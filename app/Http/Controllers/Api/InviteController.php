<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\OperationalUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationEmail;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    public function store(Request $request)
    {
        $loggedInUser = Auth::user();

        // Valida os dados recebidos
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email',
            'operational_unit_id' => 'required|exists:operational_units,id'
        ]);

        // Lógica de Permissão
        if ($loggedInUser->role->slug === 'unit_admin' && $loggedInUser->operational_unit_id != $validatedData['operational_unit_id']) {
            // Um admin de unidade não pode convidar para uma UO que não é a sua
            return response()->json(['message' => 'Permissão negada. Você só pode convidar usuários para a sua própria Unidade Operacional.'], 403);
        }
        
        // Apenas Admin Nacional e Admin de Unidade podem convidar
        if (!in_array($loggedInUser->role->slug, ['national_admin', 'unit_admin'])) {
            return response()->json(['message' => 'Acesso não autorizado para criar convites.'], 403);
        }

        // Busca o DR a partir da UO para salvar a informação completa no convite
        $unit = OperationalUnit::find($validatedData['operational_unit_id']);

        $invitation = Invitation::create([
            'email' => $validatedData['email'],
            'operational_unit_id' => $validatedData['operational_unit_id'],
            'regional_department_id' => $unit->regional_department_id,
            'token' => Str::random(40),
            'expires_at' => now()->addHours(24)
        ]);

        // Mail::to($invitation->email)->send(new InvitationEmail($invitation)); // Descomente para enviar e-mail

        return response()->json(['message' => 'Convite enviado com sucesso!', 'invitation' => $invitation], 201);
    }
}