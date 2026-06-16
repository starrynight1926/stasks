<?php

namespace App\Imports;

use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Department;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TasksImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $assignee = !empty($row['assignee']) ? TeamMember::where('name', $row['assignee'])->first() : null;
        $department = !empty($row['department']) ? Department::where('name', $row['department'])->first() : null;
        $project = Project::first();

        return new Task([
            'title' => $row['title'],
            'description' => $row['description'] ?? null,
            'status' => $row['status'] ?? 'todo',
            'priority' => $row['priority'] ?? 'medium',
            'progress' => (int) str_replace('%', '', $row['progress'] ?? '0'),
            'assignee_id' => $assignee?->id,
            'department_id' => $department?->id,
            'project_id' => $project?->id,
            'start_date' => !empty($row['start_date']) ? $row['start_date'] : null,
            'due_date' => !empty($row['due_date']) ? $row['due_date'] : null,
            'visibility' => $row['visibility'] ?? 'public',
            'sort_order' => Task::whereNull('parent_id')->max('sort_order') + 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'status' => 'nullable|in:todo,in_progress,review,done',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ];
    }
}
