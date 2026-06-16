<?php

namespace App\Imports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DepartmentsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Department([
            'name' => $row['name'],
            'code' => $row['code'] ?? strtoupper(substr($row['name'], 0, 3)),
            'description' => $row['description'] ?? null,
            'color' => $row['color'] ?? '#3B82F6',
            'head_name' => $row['head_name'] ?? null,
            'member_count' => 0,
            'active_projects' => 0,
            'performance_score' => 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
        ];
    }
}
