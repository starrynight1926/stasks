<x-layouts.app title="Task List">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Danh sách công việc</h1>
            <p class="text-sm text-neutral mt-1">Tất cả tasks — dạng bảng</p>
        </div>
        <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Task
        </a>
    </div>

    <div class="bg-white rounded-xl border border-border overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border bg-surface-alt">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Task</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Priority</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Assignee</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Due Date</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    @php
                        $statusStyles = ['todo' => 'bg-gray-100 text-neutral', 'in_progress' => 'bg-blue-100 text-secondary', 'review' => 'bg-amber-100 text-amber-700', 'done' => 'bg-emerald-100 text-tertiary'];
                        $statusLabels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'];
                        $priorityStyles = ['urgent' => 'bg-red-100 text-danger', 'high' => 'bg-amber-100 text-amber-700', 'medium' => 'bg-blue-100 text-secondary', 'low' => 'bg-gray-100 text-neutral'];
                    @endphp
                    <tr class="border-b border-border-light hover:bg-surface-alt/50 transition">
                        <td class="px-4 py-3">
                            <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium text-primary hover:text-secondary transition">{{ $task->title }}</a>
                            @if($task->subtasks->count() > 0)
                                <span class="text-[10px] text-neutral ml-1">({{ $task->subtasks->count() }} subtasks)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3"><span class="text-[11px] font-medium px-2 py-0.5 rounded {{ $statusStyles[$task->status] ?? '' }}">{{ $statusLabels[$task->status] ?? $task->status }}</span></td>
                        <td class="px-4 py-3"><span class="text-[11px] font-medium px-2 py-0.5 rounded capitalize {{ $priorityStyles[$task->priority] ?? '' }}">{{ $task->priority }}</span></td>
                        <td class="px-4 py-3">
                            @if($task->assignee)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-secondary flex items-center justify-center text-white text-[10px] font-semibold">{{ $task->assignee->initials() }}</div>
                                    <span class="text-xs text-primary">{{ $task->assignee->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-neutral">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3"><span class="text-xs text-neutral">{{ $task->due_date?->format('M d, Y') ?? '—' }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 bg-surface-alt rounded-full overflow-hidden">
                                    <div class="h-full bg-secondary rounded-full" style="width: {{ $task->progress }}%"></div>
                                </div>
                                <span class="text-[10px] text-neutral">{{ $task->progress }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-12 text-sm text-neutral">Chưa có task nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
