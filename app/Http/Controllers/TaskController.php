<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Department;
use App\Models\Tag;
use App\Support\TaskOwnership;
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
            'start_date' => $t->start_date?->format('Y-m-d'),
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
            'created_by' => TaskOwnership::currentUser() ?: null,
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
        $task->load('assignee', 'tags', 'comments.member', 'comments.files', 'files', 'subtasks.assignee', 'dependencies', 'project', 'department');
        $members = TeamMember::all();
        $canManage = TaskOwnership::isOwner($task);
        $allFiles = \App\Models\File::where('task_id', $task->id)->latest()->get();

        return view('tasks.show', compact('task', 'members', 'canManage', 'allFiles'));
    }

    public function edit(Task $task)
    {
        $task->load('tags', 'subtasks', 'dependencies');
        $members = TeamMember::all();
        $departments = Department::all();
        $tags = Tag::all();
        $projects = Project::all();
        $tasks = Task::whereNull('parent_id')->where('id', '!=', $task->id)->get();
        $canManage = TaskOwnership::isOwner($task);

        return view('tasks.edit', compact('task', 'members', 'departments', 'tags', 'projects', 'tasks', 'canManage'));
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
        ]);

        $task->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, Task $task)
    {
        $parentId = $task->parent_id;

        if ($parentId) {
            $parent = Task::find($parentId);
            if ($parent) {
                TaskOwnership::abortIfNotOwner($parent);
            }
        } else {
            TaskOwnership::abortIfNotOwner($task);
        }

        $task->subtasks()->delete();
        $task->tags()->detach();
        $task->comments()->delete();
        $task->dependencies()->detach();
        $task->delete();

        $parentProgress = $parentId ? $this->recalculateParentProgress($parentId) : null;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'parent_progress' => $parentProgress,
            ]);
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
            'status' => 'required|in:todo,in_progress,review,done,cancelled',
        ]);

        $hasSubtasks = $task->subtasks()->exists();
        $payload = ['status' => $validated['status']];

        if ($validated['status'] === 'done') {
            $payload['done_at'] = $task->done_at ?? now();
            $payload['cancelled_at'] = null;
            $payload['cancel_reason'] = null;
            if (!$hasSubtasks) $payload['progress'] = 100;
        } elseif ($validated['status'] === 'cancelled') {
            $payload['cancelled_at'] = $task->cancelled_at ?? now();
            $payload['done_at'] = null;
        } else {
            $payload['done_at'] = null;
            $payload['cancelled_at'] = null;
            $payload['cancel_reason'] = null;
        }

        $task->update($payload);

        $parentProgress = null;
        if ($task->parent_id) {
            $parentProgress = $this->recalculateParentProgress($task->parent_id);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $task->status,
                'lifecycle' => $task->fresh()->lifecycleState(),
                'progress' => (float) $task->progress,
                'parent_progress' => $parentProgress,
            ]);
        }

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function cancelSubtask(Request $request, Task $task)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $task->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => $validated['reason'],
            'done_at' => null,
        ]);

        $parentProgress = $task->parent_id ? $this->recalculateParentProgress($task->parent_id) : null;

        return response()->json([
            'success' => true,
            'lifecycle' => 'cancelled',
            'reason' => $task->cancel_reason,
            'parent_progress' => $parentProgress,
        ]);
    }

    public function updateSubtask(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:200',
            'weight' => 'sometimes|nullable|integer|min:0|max:100',
            'assignee_id' => 'sometimes|nullable|exists:team_members,id',
            'status' => 'sometimes|in:todo,in_progress,review,done,cancelled',
            'due_date' => 'sometimes|nullable|date',
        ]);

        $task->update($validated);

        $parentProgress = $task->parent_id ? $this->recalculateParentProgress($task->parent_id) : null;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'subtask' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'weight' => (float) $task->weight,
                    'status' => $task->status,
                ],
                'parent_progress' => $parentProgress,
            ]);
        }

        return redirect()->back();
    }

    public function storeSubtask(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'weight' => 'nullable|integer|min:0|max:100',
            'assignee_id' => 'nullable|exists:team_members,id',
            'due_date' => 'nullable|date',
        ]);

        $subtask = Task::create([
            'title' => $validated['title'],
            'parent_id' => $task->id,
            'project_id' => $task->project_id,
            'assignee_id' => $validated['assignee_id'] ?? null,
            'status' => 'todo',
            'priority' => $task->priority,
            'weight' => $validated['weight'] ?? 0,
            'progress' => 0,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        $parentProgress = $this->recalculateParentProgress($task->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'subtask' => [
                    'id' => $subtask->id,
                    'title' => $subtask->title,
                    'status' => $subtask->status,
                    'weight' => (float) $subtask->weight,
                    'due_date' => $subtask->due_date?->format('Y-m-d'),
                ],
                'parent_progress' => $parentProgress,
            ]);
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Subtask added.');
    }

    private function recalculateParentProgress(int $parentId): float
    {
        $parent = Task::with('subtasks')->find($parentId);
        if (!$parent) {
            return 0;
        }

        $active = $parent->subtasks->where('status', '!=', 'cancelled')->values();
        if ($active->isEmpty()) {
            $parent->update(['progress' => 0]);
            return 0;
        }

        $hasWeight = $active->contains(fn($s) => (float) $s->weight > 0);

        if ($hasWeight) {
            $doneWeight = (float) $active->where('status', 'done')->sum('weight');
            $progress = round($doneWeight, 2);
        } else {
            $total = $active->count();
            $done = $active->where('status', 'done')->count();
            $progress = round(($done / $total) * 100, 2);
        }

        $parent->update(['progress' => $progress]);

        return $progress;
    }
}
