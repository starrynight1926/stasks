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
        $scope = fn($q) => $this->scopeVisible($q);

        $project = Project::first();
        $totalTasks      = $scope(Task::query())->count();
        $completedTasks  = $scope(Task::query())->where('status', 'done')->count();
        $inProgressTasks = $scope(Task::query())->where('status', 'in_progress')->count();
        $overdueTasks    = $scope(Task::query())->where('due_date', '<', now())->where('status', '!=', 'done')->count();
        $totalMembers    = TeamMember::count();
        $completionRate  = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $tasksByStatus = [
            'todo'        => $scope(Task::query())->where('status', 'todo')->count(),
            'in_progress' => $scope(Task::query())->where('status', 'in_progress')->count(),
            'review'      => $scope(Task::query())->where('status', 'review')->count(),
            'done'        => $scope(Task::query())->where('status', 'done')->count(),
        ];

        $tasksByPriority = [
            'urgent' => $scope(Task::query())->where('priority', 'urgent')->count(),
            'high'   => $scope(Task::query())->where('priority', 'high')->count(),
            'medium' => $scope(Task::query())->where('priority', 'medium')->count(),
            'low'    => $scope(Task::query())->where('priority', 'low')->count(),
        ];

        $recentTasks = $scope(Task::with('assignee', 'tags'))->latest()->take(5)->get();
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
                'tasks' => $scope(
                        Task::with('assignee')
                            ->where('status', $key)
                            ->whereNull('parent_id')
                            ->whereNull('archived_at')
                    )
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

    private function scopeVisible($q)
    {
        if (session('is_admin')) return $q;
        $me = session('member_id') ? TeamMember::find(session('member_id')) : null;
        if (!$me) return $q->whereRaw('1=0');
        $visible = $me->visibleMemberIds();
        return $q->where(function ($w) use ($visible) {
            $w->whereIn('assignee_id', $visible)
              ->orWhereIn('created_by', $visible);
        });
    }
}
