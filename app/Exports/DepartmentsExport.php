<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartmentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Department::withCount('members')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Code', 'Description', 'Color', 'Head Name', 'Member Count', 'Active Projects', 'Performance Score', 'Created At'];
    }

    public function map($dept): array
    {
        return [
            $dept->id,
            $dept->name,
            $dept->code,
            $dept->description,
            $dept->color,
            $dept->head_name,
            $dept->members_count,
            $dept->active_projects,
            $dept->performance_score,
            $dept->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
