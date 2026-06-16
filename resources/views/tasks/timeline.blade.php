<x-layouts.app title="Gantt Chart">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Biểu đồ Gantt</h1>
            <p class="text-sm text-neutral mt-1">Tiến độ dự án — {{ $project?->name ?? 'All Projects' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Task
            </a>
        </div>
    </div>

    @php
        $startRange = $tasks->min('start_date') ?? now()->startOfMonth();
        $endRange = $tasks->max('due_date') ?? now()->endOfMonth()->addMonth();
        $startRange = \Carbon\Carbon::parse($startRange)->startOfWeek();
        $endRange = \Carbon\Carbon::parse($endRange)->endOfWeek()->addWeek();
        $totalDays = $startRange->diffInDays($endRange) ?: 30;
        $weeks = [];
        $current = $startRange->copy();
        while ($current->lt($endRange)) {
            $weeks[] = $current->copy();
            $current->addWeek();
        }
    @endphp

    <div class="bg-white rounded-xl border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <div class="min-w-[900px]">
                {{-- Timeline Header --}}
                <div class="flex border-b border-border">
                    <div class="w-64 flex-shrink-0 px-4 py-3 bg-surface-alt border-r border-border">
                        <span class="text-xs font-semibold text-neutral uppercase">Task Name</span>
                    </div>
                    <div class="flex-1 flex">
                        @foreach($weeks as $week)
                            <div class="flex-1 px-2 py-3 text-center border-r border-border-light last:border-0 bg-surface-alt">
                                <span class="text-[10px] text-neutral">{{ $week->format('M d') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Task Rows --}}
                @forelse($tasks as $task)
                    @php
                        $taskStart = $task->start_date ? \Carbon\Carbon::parse($task->start_date) : now();
                        $taskEnd = $task->due_date ? \Carbon\Carbon::parse($task->due_date) : $taskStart->copy()->addDays(7);
                        $leftPercent = max(0, $startRange->diffInDays($taskStart) / $totalDays * 100);
                        $widthPercent = max(2, $taskStart->diffInDays($taskEnd) / $totalDays * 100);
                        $barColors = [
                            'todo' => 'bg-neutral',
                            'in_progress' => 'bg-secondary',
                            'review' => 'bg-warning',
                            'done' => 'bg-tertiary',
                        ];
                        $barColor = $barColors[$task->status] ?? 'bg-secondary';
                    @endphp
                    <div class="flex border-b border-border-light hover:bg-blue-50/30 transition group">
                        <div class="w-64 flex-shrink-0 px-4 py-3 border-r border-border flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full {{ $barColor }}"></div>
                            <a href="{{ route('tasks.show', $task) }}" class="text-sm text-primary truncate hover:text-secondary transition">{{ $task->title }}</a>
                        </div>
                        <div class="flex-1 relative py-3 px-1">
                            <div class="absolute top-1/2 -translate-y-1/2 h-6 rounded {{ $barColor }} opacity-80 hover:opacity-100 transition-opacity cursor-pointer flex items-center px-2"
                                 style="left: {{ $leftPercent }}%; width: {{ $widthPercent }}%;"
                                 title="{{ $task->title }} ({{ $task->progress }}%)">
                                @if($task->progress > 0)
                                    <div class="absolute inset-y-0 left-0 rounded bg-white/20" style="width: {{ $task->progress }}%"></div>
                                @endif
                                <span class="text-[10px] text-white font-medium relative z-10 truncate">{{ $task->progress }}%</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <p class="text-sm text-neutral">Chưa có task nào. <a href="{{ route('tasks.create') }}" class="text-secondary hover:underline">Tạo task mới</a></p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
