<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Department;
use App\Models\Tag;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function board()
    {
        $columns = [
            'todo' => ['label' => 'To Do', 'color' => '#64748B', 'tasks' => Task::with('assignee', 'tags')->where('status', 'todo')->whereNull('parent_id')->whereNull('archived_at')->orderBy('sort_order')->get()],
            'in_progress' => ['label' => 'In Progress', 'color' => '#3B82F6', 'tasks' => Task::with('assignee', 'tags')->where('status', 'in_progress')->whereNull('parent_id')->whereNull('archived_at')->orderBy('sort_order')->get()],
            'review' => ['label' => 'Review', 'color' => '#F59E0B', 'tasks' => Task::with('assignee', 'tags')->where('status', 'review')->whereNull('parent_id')->whereNull('archived_at')->orderBy('sort_order')->get()],
            'done' => ['label' => 'Done', 'color' => '#10B981', 'tasks' => Task::with('assignee', 'tags')->where('status', 'done')->whereNull('parent_id')->whereNull('archived_at')->orderBy('sort_order')->get()],
        ];

        return view('tasks.board', compact('columns'));
    }

    public function timeline()
    {
        $tasks = Task::with('assignee', 'subtasks', 'dependencies')
            ->whereNull('parent_id')
            ->whereNull('archived_at')
            ->orderBy('start_date')
            ->get();
        $project = Project::first();

        return view('tasks.timeline', compact('tasks', 'project'));
    }

    public function list()
    {
        $tasks = Task::with('assignee', 'tags', 'project', 'subtasks')
            ->whereNull('parent_id')
            ->whereNull('archived_at')
            ->orderBy('sort_order')
            ->get();

        return view('tasks.list', compact('tasks'));
    }

    public function calendar()
    {
        $tasks = Task::with('assignee', 'tags')
            ->whereNull('parent_id')
            ->whereNull('archived_at')
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->get();

        $calendarTasks = $tasks->map(fn($t) => [
            'id' => $t->id,
            'title' => $t->title,
            'due_date' => $t->due_date?->format('Y-m-d'),
            'priority' => $t->priority,
            'status' => $t->status,
        ]);

        return view('tasks.calendar', compact('tasks', 'calendarTasks'));
    }

    public function archive()
    {
        $tasks = Task::with('assignee', 'tags')
            ->whereNull('parent_id')
            ->whereNotNull('archived_at')
            ->orderByDesc('archived_at')
            ->get();

        return view('tasks.archive', compact('tasks'));
    }

    public function create()
    {
        $members = TeamMember::all();
        $departments = Department::all();
        $tags = Tag::all();
        $projects = Project::all();
        $tasks = Task::whereNull('parent_id')->get();

        return view('tasks.create', compact('members', 'departments', 'tags', 'projects', 'tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:5000',
            'assignee_id' => 'nullable|exists:team_members,id',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'visibility' => 'nullable|string|max:50',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'subtasks' => 'nullable|array',
            'subtasks.*.name' => 'nullable|string|max:200',
            'subtasks.*.weight' => 'nullable|integer|min:0|max:100',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:tasks,id',
        ]);

        $project = Project::first();

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'project_id' => $project?->id,
            'assignee_id' => $validated['assignee_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'status' => 'todo',
            'priority' => $validated['priority'],
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'visibility' => $validated['visibility'] ?? 'public',
            'progress' => 0,
            'sort_order' => Task::where('status', 'todo')->whereNull('parent_id')->max('sort_order') + 1,
        ]);

        if (!empty($validated['tags'])) {
            $task->tags()->attach($validated['tags']);
        }

        if (!empty($validated['dependencies'])) {
            $task->dependencies()->attach($validated['dependencies']);
        }

        if (!empty($validated['subtasks'])) {
            foreach ($validated['subtasks'] as $subtaskData) {
                if (!empty($subtaskData['name'])) {
                    Task::create([
                        'title' => $subtaskData['name'],
                        'parent_id' => $task->id,
                        'project_id' => $project?->id,
                        'status' => 'todo',
                        'priority' => $validated['priority'],
                        'weight' => $subtaskData['weight'] ?? 0,
                        'progress' => 0,
                    ]);
                }
            }
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $task->load('assignee', 'tags', 'comments.member', 'files', 'subtasks.assignee', 'dependencies', 'project', 'department');
        $members = TeamMember::all();

        return view('tasks.show', compact('task', 'members'));
    }

    public function edit(Task $task)
    {
        $task->load('tags', 'subtasks', 'dependencies');
        $members = TeamMember::all();
        $departments = Department::all();
        $tags = Tag::all();
        $projects = Project::all();
        $tasks = Task::whereNull('parent_id')->where('id', '!=', $task->id)->get();

        return view('tasks.edit', compact('task', 'members', 'departments', 'tags', 'projects', 'tasks'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:5000',
            'assignee_id' => 'nullable|exists:team_members,id',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'nullable|in:todo,in_progress,review,done',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'visibility' => 'nullable|string|max:50',
            'progress' => 'nullable|integer|min:0|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assignee_id' => $validated['assignee_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'] ?? $task->status,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'visibility' => $validated['visibility'] ?? 'public',
            'progress' => $validated['progress'] ?? $task->progress,
        ]);

        $task->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, Task $task)
    {
        $task->subtasks()->delete();
        $task->tags()->detach();
        $task->comments()->delete();
        $task->dependencies()->detach();
        $task->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('tasks.board')->with('success', 'Task deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:tasks,id',
        ]);

        $tasks = Task::whereIn('id', $validated['ids'])->whereNull('parent_id')->get();

        foreach ($tasks as $task) {
            $task->subtasks()->delete();
            $task->tags()->detach();
            $task->comments()->delete();
            $task->dependencies()->detach();
            $task->delete();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'count' => $tasks->count()]);
        }

        return redirect()->route('tasks.list')->with('success', "Đã xóa {$tasks->count()} tasks.");
    }

    public function archiveTask(Request $request, Task $task)
    {
        $task->update(['archived_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Task đã được lưu trữ.');
    }

    public function unarchiveTask(Request $request, Task $task)
    {
        $task->update(['archived_at' => null]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Task đã được khôi phục.');
    }

    public function bulkArchive(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:tasks,id',
        ]);

        Task::whereIn('id', $validated['ids'])->update(['archived_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Đã lưu trữ ' . count($validated['ids']) . ' tasks.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
        ]);

        $task->update([
            'status' => $validated['status'],
            'progress' => $validated['status'] === 'done' ? 100 : $task->progress,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Status updated.');
    }
}
