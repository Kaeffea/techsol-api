<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CollaboratorsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $searchTerm;

    public function __construct($searchTerm = null)
    {
        $this->searchTerm = $searchTerm;
    }

    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query()
    {
        // mesma lógica de consulta do controller
        $query = User::query()->orderBy('name', 'asc');

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('name', 'LIKE', "%{$this->searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$this->searchTerm}%")
                  ->orWhere('cpf', 'LIKE', "%{$this->searchTerm}%");
            });
        }

        return $query;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // Define os nomes das colunas no arquivo Excel
        return [
            'ID',
            'Nome',
            'Email',
            'CPF',
            'Celular',
            'CEP',
            'Cidade',
            'Estado',
            'Data de Criação',
        ];
    }

    /**
    * @param mixed $user
    * @return array
    */
    public function map($user): array
    {
        // Mapeia os dados de cada usuário para as colunas
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->cpf,
            $user->phone_number,
            $user->postal_code,
            $user->city,
            $user->state,
            $user->created_at->format('d/m/Y H:i:s'), // Formata a data
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Deixa a primeira linha (o cabeçalho) em negrito
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}