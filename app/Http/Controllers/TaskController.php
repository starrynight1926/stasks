<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Department;
use App\Models\Tag;

class TaskController extends Controller
{
    public function board()
    {
        $columns = [
            'todo' => ['label' => 'To Do', 'color' => '#64748B', 'tasks' => Task::with('assignee', 'tags')->where('status', 'todo')->whereNull('parent_id')->orderBy('sort_order')->get()],
            'in_progress' => ['label' => 'In Progress', 'color' => '#3B82F6', 'tasks' => Task::with('assignee', 'tags')->where('status', 'in_progress')->whereNull('parent_id')->orderBy('sort_order')->get()],
            'review' => ['label' => 'Review', 'color' => '#F59E0B', 'tasks' => Task::with('assignee', 'tags')->where('status', 'review')->whereNull('parent_id')->orderBy('sort_order')->get()],
            'done' => ['label' => 'Done', 'color' => '#10B981', 'tasks' => Task::with('assignee', 'tags')->where('status', 'done')->whereNull('parent_id')->orderBy('sort_order')->get()],
        ];

        return view('tasks.board', compact('columns'));
    }

    public function timeline()
    {
        $tasks = Task::with('assignee', 'subtasks', 'dependencies')
            ->whereNull('parent_id')
            ->orderBy('start_date')
            ->get();
        $project = Project::first();

        return view('tasks.timeline', compact('tasks', 'project'));
    }

    public function list()
    {
        $tasks = Task::with('assignee', 'tags', 'project', 'subtasks')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('tasks.list', compact('tasks'));
    }

    public function create()
    {
        $members = TeamMember::all();
        $departments = Department::all();
        $tags = Tag::all();
        $projects = Project::all();

        return view('tasks.create', compact('members', 'departments', 'tags', 'projects'));
    }

    public function show(Task $task)
    {
        $task->load('assignee', 'tags', 'comments.member', 'files', 'subtasks.assignee', 'dependencies', 'project', 'department');

        return view('tasks.show', compact('task'));
    }
}
