<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TasksExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Task::with('assignee', 'department', 'tags')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }

    public function headings(): array
    {
        return ['ID', 'Title', 'Description', 'Status', 'Priority', 'Progress', 'Assignee', 'Department', 'Tags', 'Start Date', 'Due Date', 'Visibility', 'Created At'];
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->title,
            $task->description,
            $task->status,
            $task->priority,
            $task->progress . '%',
            $task->assignee?->name ?? '',
            $task->department?->name ?? '',
            $task->tags->pluck('name')->join(', '),
            $task->start_date?->format('Y-m-d') ?? '',
            $task->due_date?->format('Y-m-d') ?? '',
            $task->visibility,
            $task->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
