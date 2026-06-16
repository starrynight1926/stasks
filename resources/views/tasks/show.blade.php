<x-layouts.app title="{{ $task->title }}">
    <div class="mb-6">
        <nav class="text-xs text-neutral mb-2">
            <a href="{{ route('tasks.board') }}" class="hover:text-secondary">Tasks</a>
            <span class="mx-1">/</span>
            <span class="text-primary">{{ $task->title }}</span>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Task Header --}}
            <div class="bg-white rounded-xl border border-border p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-xl font-bold text-primary">{{ $task->title }}</h1>
                        <p class="text-xs text-neutral mt-1">Created {{ $task->created_at->diffForHumans() }}</p>
                    </div>
                    @php
                        $statusStyles = ['todo' => 'bg-gray-100 text-neutral', 'in_progress' => 'bg-blue-100 text-secondary', 'review' => 'bg-amber-100 text-amber-700', 'done' => 'bg-emerald-100 text-tertiary'];
                        $statusLabels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'];
                    @endphp
                    <span class="text-xs font-medium px-2.5 py-1 rounded {{ $statusStyles[$task->status] ?? '' }}">{{ $statusLabels[$task->status] ?? $task->status }}</span>
                </div>

                @if($task->description)
                    <div class="prose prose-sm max-w-none text-primary/80">
                        <p>{{ $task->description }}</p>
                    </div>
                @endif

                {{-- Progress --}}
                <div class="mt-4 p-3 bg-surface-alt rounded-lg">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-medium text-primary">Progress</span>
                        <span class="text-xs font-bold text-secondary">{{ $task->progress }}%</span>
                    </div>
                    <div class="h-2 bg-white rounded-full overflow-hidden">
                        <div class="h-full bg-secondary rounded-full transition-all" style="width: {{ $task->progress }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Sub-tasks --}}
            @if($task->subtasks->isNotEmpty())
                <div class="bg-white rounded-xl border border-border p-5">
                    <h3 class="text-sm font-semibold text-primary mb-3">Sub-Tasks ({{ $task->subtasks->count() }})</h3>
                    <div class="space-y-2">
                        @foreach($task->subtasks as $subtask)
                            <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-surface-alt transition">
                                @php $done = $subtask->status === 'done'; @endphp
                                <div class="w-5 h-5 rounded border-2 flex items-center justify-center {{ $done ? 'bg-tertiary border-tertiary' : 'border-border' }}">
                                    @if($done)
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </div>
                                <span class="text-sm {{ $done ? 'line-through text-neutral' : 'text-primary' }}">{{ $subtask->title }}</span>
                                @if($subtask->assignee)
                                    <div class="ml-auto w-6 h-6 rounded-full bg-secondary flex items-center justify-center text-white text-[10px] font-semibold">{{ $subtask->assignee->initials() }}</div>
                                @endif
                                @if($subtask->weight > 0)
                                    <span class="text-[10px] text-neutral">{{ $subtask->weight }}%</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Comments --}}
            <div class="bg-white rounded-xl border border-border p-5">
                <h3 class="text-sm font-semibold text-primary mb-4">Activity & Comments</h3>
                <div class="space-y-4">
                    @forelse($task->comments as $comment)
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-secondary flex-shrink-0 flex items-center justify-center text-white text-[10px] font-semibold">
                                {{ $comment->member?->initials() ?? '?' }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-medium text-primary">{{ $comment->member?->name ?? 'Unknown' }}</span>
                                    <span class="text-[10px] text-neutral">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-primary/80">{{ $comment->body }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-neutral text-center py-4">No comments yet</p>
                    @endforelse
                </div>
                <div class="mt-4 pt-4 border-t border-border">
                    <div class="flex gap-2">
                        <input type="text" placeholder="Write a comment..." class="flex-1 px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <button class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">Send</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Attributes --}}
            <div class="bg-white rounded-xl border border-border p-5 space-y-4">
                <h3 class="text-sm font-semibold text-primary">Attributes</h3>

                <div>
                    <label class="text-[10px] font-semibold text-neutral uppercase tracking-wider">Assignee</label>
                    <div class="flex items-center gap-2 mt-1">
                        @if($task->assignee)
                            <div class="w-7 h-7 rounded-full bg-secondary flex items-center justify-center text-white text-[10px] font-semibold">{{ $task->assignee->initials() }}</div>
                            <span class="text-sm text-primary">{{ $task->assignee->name }}</span>
                        @else
                            <span class="text-sm text-neutral">Unassigned</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-semibold text-neutral uppercase tracking-wider">Priority</label>
                    @php
                        $priorityColors = ['urgent' => 'bg-red-100 text-danger', 'high' => 'bg-amber-100 text-amber-700', 'medium' => 'bg-blue-100 text-secondary', 'low' => 'bg-gray-100 text-neutral'];
                    @endphp
                    <div class="mt-1">
                        <span class="text-xs font-medium px-2 py-1 rounded capitalize {{ $priorityColors[$task->priority] ?? '' }}">{{ $task->priority }}</span>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-semibold text-neutral uppercase tracking-wider">Due Date</label>
                    <p class="text-sm text-primary mt-1">{{ $task->due_date?->format('M d, Y') ?? '—' }}</p>
                </div>

                <div>
                    <label class="text-[10px] font-semibold text-neutral uppercase tracking-wider">Department</label>
                    <p class="text-sm text-primary mt-1">{{ $task->department?->name ?? '—' }}</p>
                </div>

                <div>
                    <label class="text-[10px] font-semibold text-neutral uppercase tracking-wider">Tags & Labels</label>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @forelse($task->tags as $tag)
                            <span class="text-[10px] font-medium px-2 py-0.5 rounded" style="background: {{ $tag->color }}20; color: {{ $tag->color }}">{{ $tag->name }}</span>
                        @empty
                            <span class="text-xs text-neutral">No tags</span>
                        @endforelse
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-semibold text-neutral uppercase tracking-wider">Visibility</label>
                    <p class="text-sm text-primary mt-1 capitalize">{{ $task->visibility }}</p>
                </div>
            </div>

            {{-- Attachments --}}
            <div class="bg-white rounded-xl border border-border p-5">
                <h3 class="text-sm font-semibold text-primary mb-3">Attachments</h3>
                @forelse($task->files as $file)
                    <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-surface-alt transition">
                        <div class="w-8 h-8 bg-surface-alt rounded flex items-center justify-center">
                            <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-primary truncate">{{ $file->original_name }}</p>
                            <p class="text-[10px] text-neutral">{{ $file->sizeForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="border-2 border-dashed border-border rounded-lg p-4 text-center">
                        <svg class="w-6 h-6 text-neutral mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-[10px] text-neutral">Drop files here or click to upload</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
