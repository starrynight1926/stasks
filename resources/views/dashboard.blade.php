<x-layouts.app title="Dashboard">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary">Dashboard Overview</h1>
        <p class="text-sm text-neutral mt-1">{{ $project?->name ?? 'Project Overview' }} — Tổng quan tiến độ dự án</p>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-border p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-neutral uppercase tracking-wider">Total Tasks</span>
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-primary">{{ $totalTasks }}</div>
            <p class="text-xs text-neutral mt-1">{{ $inProgressTasks }} đang thực hiện</p>
        </div>

        <div class="bg-white rounded-xl border border-border p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-neutral uppercase tracking-wider">Completed</span>
                <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-primary">{{ $completedTasks }}</div>
            <p class="text-xs text-neutral mt-1">{{ $completionRate }}% hoàn thành</p>
        </div>

        <div class="bg-white rounded-xl border border-border p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-neutral uppercase tracking-wider">Team Size</span>
                <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-primary">{{ $totalMembers }}</div>
            <p class="text-xs text-neutral mt-1">Thành viên hoạt động</p>
        </div>

        <div class="bg-white rounded-xl border border-border p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-neutral uppercase tracking-wider">Overdue</span>
                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-danger">{{ $overdueTasks }}</div>
            <p class="text-xs text-neutral mt-1">Quá hạn</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Task Distribution Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-border p-5">
            <h3 class="text-sm font-semibold text-primary mb-4">Task Distribution</h3>
            <div class="flex items-end gap-6 h-48">
                @foreach($tasksByStatus as $status => $count)
                    @php
                        $colors = ['todo' => 'bg-neutral', 'in_progress' => 'bg-secondary', 'review' => 'bg-warning', 'done' => 'bg-tertiary'];
                        $labels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'];
                        $maxCount = max(1, max($tasksByStatus));
                        $height = ($count / $maxCount) * 100;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <span class="text-sm font-bold text-primary">{{ $count }}</span>
                        <div class="w-full rounded-t-lg {{ $colors[$status] }} transition-all duration-500" style="height: {{ max(8, $height) }}%"></div>
                        <span class="text-xs text-neutral">{{ $labels[$status] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Priority Breakdown --}}
        <div class="bg-white rounded-xl border border-border p-5">
            <h3 class="text-sm font-semibold text-primary mb-4">Priority Breakdown</h3>
            <div class="space-y-3">
                @foreach($tasksByPriority as $priority => $count)
                    @php
                        $colors = ['urgent' => 'bg-danger', 'high' => 'bg-warning', 'medium' => 'bg-secondary', 'low' => 'bg-neutral-light'];
                        $total = max(1, array_sum($tasksByPriority));
                        $percent = round(($count / $total) * 100);
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="capitalize text-primary font-medium">{{ $priority }}</span>
                            <span class="text-neutral">{{ $count }} ({{ $percent }}%)</span>
                        </div>
                        <div class="h-2 bg-surface-alt rounded-full overflow-hidden">
                            <div class="{{ $colors[$priority] }} h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Tasks & Team --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-xl border border-border p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-primary">Recent Tasks</h3>
                <a href="{{ route('tasks.board') }}" class="text-xs text-secondary hover:underline">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($recentTasks as $task)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-surface-alt transition">
                        @php
                            $statusColors = ['todo' => 'bg-neutral', 'in_progress' => 'bg-secondary', 'review' => 'bg-warning', 'done' => 'bg-tertiary'];
                        @endphp
                        <div class="w-2 h-2 rounded-full {{ $statusColors[$task->status] ?? 'bg-neutral' }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-primary truncate">{{ $task->title }}</p>
                            <p class="text-xs text-neutral">{{ $task->assignee?->name ?? 'Unassigned' }}</p>
                        </div>
                        @php
                            $priorityColors = ['urgent' => 'text-danger', 'high' => 'text-warning', 'medium' => 'text-secondary', 'low' => 'text-neutral'];
                        @endphp
                        <span class="text-xs font-medium capitalize {{ $priorityColors[$task->priority] ?? 'text-neutral' }}">{{ $task->priority }}</span>
                    </div>
                @empty
                    <p class="text-sm text-neutral text-center py-4">Chưa có task nào</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl border border-border p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-primary">Team Members</h3>
                <a href="{{ route('teams') }}" class="text-xs text-secondary hover:underline">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($members as $member)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-surface-alt transition">
                        <div class="w-8 h-8 rounded-full bg-secondary flex items-center justify-center text-white text-xs font-semibold">
                            {{ $member->initials() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-primary">{{ $member->name }}</p>
                            <p class="text-xs text-neutral">{{ $member->role }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-primary">{{ $member->active_tasks }} tasks</p>
                            <div class="w-16 h-1.5 bg-surface-alt rounded-full mt-1 overflow-hidden">
                                <div class="h-full bg-secondary rounded-full" style="width: {{ $member->workload_percent }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-neutral text-center py-4">Chưa có thành viên</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
