<?php

namespace App\Imports;

use App\Models\TeamMember;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeamMembersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $department = !empty($row['department']) ? Department::where('name', $row['department'])->first() : null;

        return new TeamMember([
            'name' => $row['name'],
            'email' => $row['email'],
            'role' => $row['role'] ?? 'member',
            'position' => $row['position'] ?? null,
            'department_id' => $department?->id,
            'phone' => $row['phone'] ?? null,
            'status' => $row['status'] ?? 'active',
            'active_tasks' => 0,
            'workload_percent' => 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email',
        ];
    }
}
