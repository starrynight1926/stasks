<?php

namespace App\Exports;

use App\Models\TeamMember;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeamMembersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return TeamMember::with('department')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Password (encoded)', 'Role', 'Position', 'Department', 'Phone', 'Status', 'Active Tasks', 'Workload %', 'Created At'];
    }

    public function map($member): array
    {
        return [
            $member->id,
            $member->name,
            $member->email,
            bcrypt('password'),
            $member->role,
            $member->position,
            $member->department?->name ?? '',
            $member->phone,
            $member->status,
            $member->active_tasks,
            $member->workload_percent,
            $member->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
