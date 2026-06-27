<x-layouts.app title="Task List">
    <div x-data="taskList()" class="space-y-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary">Danh sách công việc</h1>
                <p class="text-sm text-neutral mt-1">Tất cả tasks — dạng bảng</p>
            </div>
            <div class="flex items-center gap-2">
                @include('tasks._view_switcher', ['current' => 'tasks.list'])
                <button onclick="exportFile('{{ route('export.tasks') }}')" class="inline-flex items-center gap-2 px-3 py-2 border border-border text-sm font-medium text-primary rounded-lg hover:bg-surface-alt transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export
                </button>
                <button onclick="document.getElementById('importTasksModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-3 py-2 border border-border text-sm font-medium text-primary rounded-lg hover:bg-surface-alt transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import
                </button>
                <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Task
                </a>
            </div>
        </div>

        {{-- Bulk Actions Bar --}}
        <div x-show="selected.length > 0" x-cloak
             class="flex items-center gap-3 px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-xl">
            <span class="text-sm font-medium text-secondary" x-text="selected.length + ' task được chọn'"></span>
            <div class="flex items-center gap-2 ml-auto">
                <button @click="bulkArchiveSelected()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-primary border border-border rounded-lg hover:bg-surface-alt transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Lưu trữ
                </button>
                <button @click="bulkDeleteSelected()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-danger border border-danger/30 rounded-lg hover:bg-red-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Xóa hàng loạt
                </button>
                <button @click="selected = []" class="p-1.5 rounded-lg hover:bg-surface-alt transition" title="Bỏ chọn">
                    <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-border overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-surface-alt">
                        <th class="w-10 px-3 py-3">
                            <input type="checkbox" @change="toggleAll($event)" :checked="allSelected" class="w-4 h-4 rounded border-border text-secondary focus:ring-secondary cursor-pointer">
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Task</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Priority</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Assignee</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Due Date</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Progress</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-neutral uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        @php
                            $statusStyles = ['todo' => 'bg-gray-100 text-neutral', 'in_progress' => 'bg-blue-100 text-secondary', 'review' => 'bg-amber-100 text-amber-700', 'done' => 'bg-emerald-100 text-tertiary'];
                            $statusLabels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'];
                            $priorityStyles = ['urgent' => 'bg-red-100 text-danger', 'high' => 'bg-amber-100 text-amber-700', 'medium' => 'bg-blue-100 text-secondary', 'low' => 'bg-gray-100 text-neutral'];
                        @endphp
                        <tr class="border-b border-border-light hover:bg-surface-alt/50 transition"
                            @contextmenu.prevent="$store.ctx.show($event, { taskUrl: '{{ route('tasks.show', $task) }}', editUrl: '{{ route('tasks.edit', $task) }}', deleteUrl: '{{ route('tasks.destroy', $task) }}', archiveUrl: '{{ route('tasks.archiveTask', $task) }}', redirectUrl: '{{ route('tasks.list') }}' })">
                            <td class="w-10 px-3 py-3">
                                <input type="checkbox" value="{{ $task->id }}" x-model.number="selected" class="w-4 h-4 rounded border-border text-secondary focus:ring-secondary cursor-pointer">
                            </td>
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
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('tasks.edit', $task) }}" class="p-1.5 rounded-lg hover:bg-surface-alt transition" title="Edit">
                                        <svg class="w-3.5 h-3.5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button onclick="deleteResource('{{ route('tasks.destroy', $task) }}', '{{ route('tasks.list') }}')" class="p-1.5 rounded-lg hover:bg-red-50 transition" title="Delete">
                                        <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-12 text-sm text-neutral">Chưa có task nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function taskList() {
        return {
            selected: [],
            taskIds: @json($tasks->pluck('id')),
            get allSelected() {
                return this.taskIds.length > 0 && this.selected.length === this.taskIds.length;
            },
            toggleAll(e) {
                this.selected = e.target.checked ? [...this.taskIds] : [];
            },
            bulkDeleteSelected() {
                if (!confirm(`Xóa ${this.selected.length} task? Hành động này không thể hoàn tác.`)) return;
                bulkAction('{{ route('tasks.bulkDestroy') }}', this.selected, '{{ route('tasks.list') }}');
            },
            bulkArchiveSelected() {
                bulkAction('{{ route('tasks.bulkArchive') }}', this.selected, '{{ route('tasks.list') }}');
            }
        };
    }
    </script>

    {{-- Import Modal --}}
    <div id="importTasksModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-xl">
            <h2 class="text-lg font-bold text-primary mb-4">Import Tasks từ Excel</h2>
            <form action="{{ route('import.tasks') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">File Excel (.xlsx)</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="w-full text-sm border border-border rounded-lg outline-none p-2 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-surface-alt file:text-primary hover:file:bg-secondary hover:file:text-white file:transition" required>
                    <p class="text-[10px] text-neutral mt-1">Cột bắt buộc: Title, Status (todo/in_progress/review/done), Priority (low/medium/high/urgent)</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('importTasksModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt transition">Hủy</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Import</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
