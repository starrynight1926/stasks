<x-layouts.app title="Kanban Board">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Bảng Kanban</h1>
            <p class="text-sm text-neutral mt-1">Quản lý công việc theo trạng thái</p>
        </div>
        <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Task
        </a>
    </div>

    <div class="flex gap-4 overflow-x-auto pb-4">
        @foreach($columns as $status => $column)
            <div class="flex-shrink-0 w-72">
                <div class="flex items-center gap-2 mb-3 px-1">
                    <div class="w-2.5 h-2.5 rounded-full" style="background: {{ $column['color'] }}"></div>
                    <h3 class="text-sm font-semibold text-primary">{{ $column['label'] }}</h3>
                    <span class="text-xs text-neutral bg-surface-alt px-1.5 py-0.5 rounded-full">{{ $column['tasks']->count() }}</span>
                </div>

                <div class="space-y-2.5 min-h-[200px] bg-surface-alt/50 rounded-xl p-2">
                    @forelse($column['tasks'] as $task)
                        <div class="bg-white rounded-lg border border-border p-3.5 hover:shadow-md transition-shadow cursor-pointer group relative"
                             onclick="window.location='{{ route('tasks.show', $task) }}'"
                             @contextmenu.prevent="$store.ctx.show($event, { taskUrl: '{{ route('tasks.show', $task) }}', editUrl: '{{ route('tasks.edit', $task) }}', deleteUrl: '{{ route('tasks.destroy', $task) }}', redirectUrl: '{{ route('tasks.board') }}' })">
                            {{-- Hover action buttons --}}
                            <div class="absolute top-2 right-2 flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition" onclick="event.stopPropagation()">
                                <a href="{{ route('tasks.edit', $task) }}" class="p-1 rounded hover:bg-surface-alt transition" title="Edit">
                                    <svg class="w-3.5 h-3.5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button onclick="event.stopPropagation(); deleteResource('{{ route('tasks.destroy', $task) }}', '{{ route('tasks.board') }}')" class="p-1 rounded hover:bg-red-50 transition" title="Delete">
                                    <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            @if($task->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($task->tags as $tag)
                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded" style="background: {{ $tag->color }}20; color: {{ $tag->color }}">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <h4 class="text-sm font-medium text-primary mb-2 group-hover:text-secondary transition">{{ $task->title }}</h4>

                            @if($task->description)
                                <p class="text-xs text-neutral mb-3 line-clamp-2">{{ Str::limit($task->description, 80) }}</p>
                            @endif

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($task->assignee)
                                        <div class="w-6 h-6 rounded-full bg-secondary flex items-center justify-center text-white text-[10px] font-semibold" title="{{ $task->assignee->name }}">
                                            {{ $task->assignee->initials() }}
                                        </div>
                                    @endif
                                    @if($task->due_date)
                                        <span class="text-[10px] text-neutral flex items-center gap-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $task->due_date->format('M d') }}
                                        </span>
                                    @endif
                                </div>
                                @php
                                    $priorityStyles = [
                                        'urgent' => 'bg-red-100 text-danger',
                                        'high' => 'bg-amber-100 text-amber-700',
                                        'medium' => 'bg-blue-100 text-secondary',
                                        'low' => 'bg-gray-100 text-neutral',
                                    ];
                                @endphp
                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded capitalize {{ $priorityStyles[$task->priority] ?? 'bg-gray-100 text-neutral' }}">{{ $task->priority }}</span>
                            </div>

                            @if($task->progress > 0)
                                <div class="mt-2.5 h-1 bg-surface-alt rounded-full overflow-hidden">
                                    <div class="h-full bg-secondary rounded-full" style="width: {{ $task->progress }}%"></div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-xs text-neutral">No tasks</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.app>
