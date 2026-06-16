<x-layouts.app title="Archive">
    <div x-data="{ selected: [], taskIds: @json($tasks->pluck('id')) }" class="space-y-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary">Lưu trữ</h1>
                <p class="text-sm text-neutral mt-1">Tasks đã lưu trữ — có thể khôi phục</p>
            </div>
            <div class="flex items-center gap-2">
                <template x-if="selected.length > 0">
                    <button @click="if(confirm('Xóa vĩnh viễn ' + selected.length + ' task?')) bulkAction('{{ route('tasks.bulkDestroy') }}', selected, '{{ route('tasks.archive') }}')"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-danger border border-danger/30 rounded-lg hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Xóa vĩnh viễn
                    </button>
                </template>
            </div>
        </div>

        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-tertiary" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-border overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-surface-alt">
                        <th class="w-10 px-3 py-3">
                            <input type="checkbox" @change="selected = $event.target.checked ? [...taskIds] : []" :checked="taskIds.length > 0 && selected.length === taskIds.length" class="w-4 h-4 rounded border-border text-secondary focus:ring-secondary cursor-pointer">
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Task</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Priority</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Assignee</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Archived</th>
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
                        <tr class="border-b border-border-light hover:bg-surface-alt/50 transition">
                            <td class="w-10 px-3 py-3">
                                <input type="checkbox" value="{{ $task->id }}" x-model.number="selected" class="w-4 h-4 rounded border-border text-secondary focus:ring-secondary cursor-pointer">
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-neutral">{{ $task->title }}</span>
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
                            <td class="px-4 py-3"><span class="text-xs text-neutral">{{ $task->archived_at->format('M d, Y') }}</span></td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button onclick="archiveResource('{{ route('tasks.unarchiveTask', $task) }}', '{{ route('tasks.archive') }}')" class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-secondary border border-secondary/30 rounded-lg hover:bg-blue-50 transition" title="Khôi phục">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        Khôi phục
                                    </button>
                                    <button onclick="deleteResource('{{ route('tasks.destroy', $task) }}', '{{ route('tasks.archive') }}')" class="p-1.5 rounded-lg hover:bg-red-50 transition" title="Xóa vĩnh viễn">
                                        <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-12 text-sm text-neutral">
                            <svg class="w-12 h-12 text-neutral/30 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            Chưa có task nào trong lưu trữ
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
