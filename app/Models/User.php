<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'role_id',
        'regional_department_id', // <-- GARANTE QUE ESTE CAMPO SEJA SALVO
        'operational_unit_id',    // <-- GARANTE QUE ESTE CAMPO SEJA SALVO
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relação: Um usuário pertence a um Perfil (Role).
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relação: Um usuário pode pertencer a um Departamento Regional.
     */
    public function regionalDepartment()
    {
        return $this->belongsTo(RegionalDepartment::class);
    }

    /**
     * Relação: Um usuário pode pertencer a uma Unidade Operacional.
     */
    public function operationalUnit()
    {
        return $this->belongsTo(OperationalUnit::class);
    }
}