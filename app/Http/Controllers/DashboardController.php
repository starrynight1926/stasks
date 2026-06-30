<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Department;

class DashboardController extends Controller
{
    public function index()
    {
        $project = Project::first();
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'done')->count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();
        $overdueTasks = Task::where('due_date', '<', now())->where('status', '!=', 'done')->count();
        $totalMembers = TeamMember::count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $tasksByStatus = [
            'todo' => Task::where('status', 'todo')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'review' => Task::where('status', 'review')->count(),
            'done' => Task::where('status', 'done')->count(),
        ];

        $tasksByPriority = [
            'urgent' => Task::where('priority', 'urgent')->count(),
            'high' => Task::where('priority', 'high')->count(),
            'medium' => Task::where('priority', 'medium')->count(),
            'low' => Task::where('priority', 'low')->count(),
        ];

        $recentTasks = Task::with('assignee', 'tags')->latest()->take(5)->get();
        $members = TeamMember::with('department')->take(5)->get();

        $statusColumns = [
            'todo'        => ['label' => 'To Do',       'dot' => '#94A3B8'],
            'in_progress' => ['label' => 'In Progress', 'dot' => '#3B82F6'],
            'review'      => ['label' => 'Review',      'dot' => '#F59E0B'],
            'done'        => ['label' => 'Done',        'dot' => '#10B981'],
        ];
        $allTasksByStatus = [];
        foreach ($statusColumns as $key => $meta) {
            $allTasksByStatus[$key] = [
                'meta'  => $meta,
                'tasks' => Task::with('assignee')
                    ->where('status', $key)
                    ->whereNull('parent_id')
                    ->whereNull('archived_at')
                    ->orderByDesc('updated_at')
                    ->take(20)
                    ->get(),
            ];
        }

        return view('dashboard', compact(
            'project', 'totalTasks', 'completedTasks', 'inProgressTasks',
            'overdueTasks', 'totalMembers', 'completionRate',
            'tasksByStatus', 'tasksByPriority', 'recentTasks', 'members',
            'allTasksByStatus'
        ));
    }
}
