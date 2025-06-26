<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationEmail;

class InviteController extends Controller
{
    /**
     * Armazena um novo convite no banco de dados.
     */
    public function store(Request $request){
        // 1. Valida se o campo 'email' foi enviado e se é um e-mail válido
        $request->validate([
            'email' => 'required|email|unique:users,email|unique:invitations,email,NULL,id,status,pending'
        ]);

        // 2. Cria o convite no banco de dados
        $invitation = Invitation::create([
            'email' => $request->email,
            'token' => Str::random(40), // Gera um token aleatório e seguro de 40 caracteres
            'expires_at' => now()->addHours(24) // Define a data de expiração para 24 horas a partir de agora
        ]);

        // 3. Lógica de envio de email
        Mail::to($invitation->email)->send(new InvitationEmail($invitation));

        // 4. Retorna uma resposta de sucesso. O status 201 significa "Recurso Criado".
        return response()->json([
            'message' => 'Convite criado e salvo com sucesso!',
            'invitation' => $invitation // Depuração
        ], 201);
    }
}
