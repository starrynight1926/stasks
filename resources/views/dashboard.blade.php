<x-layouts.app title="Dashboard">
    <div x-data="taskSummaryPanel()" @keydown.escape.window="close()">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary">Dashboard Overview</h1>
        <p class="text-sm text-neutral mt-1">{{ $project?->name ?? 'Project Overview' }} — Tổng quan tiến độ dự án. <span class="text-secondary">Click task bất kỳ để xem tóm tắt.</span></p>
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
                    @php
                        $statusColors   = ['todo' => 'bg-neutral', 'in_progress' => 'bg-secondary', 'review' => 'bg-warning', 'done' => 'bg-tertiary'];
                        $priorityColors = ['urgent' => 'text-danger', 'high' => 'text-warning', 'medium' => 'text-secondary', 'low' => 'text-neutral'];
                    @endphp
                    <button type="button" @click="open({{ $task->id }})" class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-surface-alt transition text-left">
                        <div class="w-2 h-2 rounded-full {{ $statusColors[$task->status] ?? 'bg-neutral' }} flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-primary truncate">{{ $task->title }}</p>
                            <p class="text-xs text-neutral">{{ $task->assignee?->name ?? 'Unassigned' }}</p>
                        </div>
                        <span class="text-xs font-medium capitalize {{ $priorityColors[$task->priority] ?? 'text-neutral' }}">{{ $task->priority }}</span>
                    </button>
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

    {{-- Slide-over task summary panel --}}
    <div x-show="isOpen" x-cloak class="fixed inset-0 z-50" @click.self="close()">
        <div class="absolute inset-0 bg-black/40" x-show="isOpen" x-transition.opacity></div>
        <aside class="absolute right-0 top-0 h-full w-full sm:w-[480px] bg-white shadow-2xl flex flex-col"
               x-show="isOpen"
               x-transition:enter="transition transform ease-out duration-200"
               x-transition:enter-start="translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition transform ease-in duration-150"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="translate-x-full">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-border flex-shrink-0">
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-neutral">Task Summary</span>
                </div>
                <div class="flex items-center gap-1">
                    <template x-if="data && data.url">
                        <a :href="data.url" class="px-2 py-1 text-xs text-secondary hover:bg-surface-alt rounded transition">Open full →</a>
                    </template>
                    <button type="button" @click="close()" class="p-1.5 rounded-lg hover:bg-surface-alt transition" title="Close">
                        <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto">
                <template x-if="loading">
                    <div class="p-8 text-center text-xs text-neutral">Loading…</div>
                </template>
                <template x-if="errorMsg">
                    <div class="p-5 text-xs text-danger" x-text="errorMsg"></div>
                </template>
                <template x-if="data && !loading">
                    <div class="p-5 space-y-5">
                        {{-- Title --}}
                        <div>
                            <h2 class="text-lg font-bold text-primary leading-tight break-words" x-text="data.title"></h2>
                            <template x-if="data.description">
                                <p class="text-sm text-primary/70 mt-2 break-words whitespace-pre-wrap" x-text="data.description"></p>
                            </template>
                        </div>

                        {{-- Details grid --}}
                        <div class="bg-surface-alt rounded-lg p-3 space-y-2.5 text-xs">
                            <div class="flex items-center gap-3">
                                <span class="w-20 text-neutral flex-shrink-0">Status</span>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full font-medium"
                                      :style="`background:${statusBg(data.status)};color:${statusFg(data.status)}`"
                                      x-text="statusLabel(data.status)"></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-20 text-neutral flex-shrink-0">Priority</span>
                                <span class="inline-flex items-center gap-1.5 font-medium capitalize"
                                      :style="`color:${priorityColor(data.priority)}`">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M2 21V3l20 9-20 9z"/></svg>
                                    <span x-text="data.priority"></span>
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-20 text-neutral flex-shrink-0">Assignee</span>
                                <template x-if="data.assignee">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full bg-secondary text-white text-[10px] font-semibold flex items-center justify-center" x-text="data.assignee.initials"></span>
                                        <span class="text-primary" x-text="data.assignee.name"></span>
                                    </span>
                                </template>
                                <template x-if="!data.assignee">
                                    <span class="text-neutral italic">Unassigned</span>
                                </template>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-20 text-neutral flex-shrink-0">Start</span>
                                <span class="text-primary" x-text="data.start_date || '—'"></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-20 text-neutral flex-shrink-0">Due</span>
                                <span :class="data.overdue ? 'text-danger font-medium' : 'text-primary'" x-text="data.due_date || '—'"></span>
                                <template x-if="data.overdue">
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-danger font-medium">OVERDUE</span>
                                </template>
                            </div>
                        </div>

                        {{-- Project structure --}}
                        <div>
                            <h3 class="text-[11px] font-semibold uppercase tracking-wider text-neutral mb-2">Project structure</h3>
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="w-20 text-neutral flex-shrink-0">Project</span>
                                    <span class="text-primary" x-text="data.project ? data.project.name : '—'"></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="w-20 text-neutral flex-shrink-0">Department</span>
                                    <span class="text-primary" x-text="data.department ? data.department.name : '—'"></span>
                                </div>
                                <template x-if="data.parent">
                                    <div class="flex items-center gap-3">
                                        <span class="w-20 text-neutral flex-shrink-0">Parent</span>
                                        <span class="text-secondary" x-text="data.parent.title"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Tags --}}
                        <template x-if="data.tags && data.tags.length">
                            <div>
                                <h3 class="text-[11px] font-semibold uppercase tracking-wider text-neutral mb-2">Labels</h3>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="t in data.tags" :key="t.id">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium"
                                              :style="`background:${t.color}20;color:${t.color}`"
                                              x-text="t.name"></span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Progress --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <h3 class="text-[11px] font-semibold uppercase tracking-wider text-neutral">Progress</h3>
                                <span class="text-xs font-bold" :class="data.progress > 100 ? 'text-danger' : 'text-secondary'" x-text="Math.round(data.progress) + '%'"></span>
                            </div>
                            <div class="h-2 bg-surface-alt rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all"
                                     :class="data.progress > 100 ? 'bg-danger' : 'bg-secondary'"
                                     :style="`width: ${Math.min(100, data.progress)}%`"></div>
                            </div>
                            <p class="text-[11px] text-neutral mt-1.5">
                                <span x-text="data.subtasks.done"></span> / <span x-text="data.subtasks.total"></span> subtasks done
                            </p>
                        </div>

                        {{-- Meta --}}
                        <div class="pt-3 border-t border-border text-[11px] text-neutral space-y-1">
                            <p>Created <span x-text="data.created_at"></span></p>
                            <p>Updated <span x-text="data.updated_at"></span></p>
                        </div>
                    </div>
                </template>
            </div>
        </aside>
    </div>
    </div>

    <script>
        function taskSummaryPanel() {
            const STATUS_META = {
                todo:        { label: 'To Do',       bg: '#F1F5F9', fg: '#475569' },
                in_progress: { label: 'In Progress', bg: '#DBEAFE', fg: '#1D4ED8' },
                review:      { label: 'Review',      bg: '#FEF3C7', fg: '#B45309' },
                done:        { label: 'Done',        bg: '#D1FAE5', fg: '#047857' },
                cancelled:   { label: 'Cancelled',   bg: '#FEE2E2', fg: '#B91C1C' },
            };
            const PRIORITY_COLOR = { urgent: '#DC2626', high: '#D97706', medium: '#2563EB', low: '#6B7280' };
            return {
                isOpen: false,
                loading: false,
                errorMsg: '',
                data: null,
                statusLabel(s){ return STATUS_META[s]?.label || s; },
                statusBg(s){ return STATUS_META[s]?.bg || '#F1F5F9'; },
                statusFg(s){ return STATUS_META[s]?.fg || '#475569'; },
                priorityColor(p){ return PRIORITY_COLOR[p] || '#6B7280'; },
                close() { this.isOpen = false; },
                async open(taskId) {
                    this.isOpen = true;
                    this.loading = true;
                    this.errorMsg = '';
                    this.data = null;
                    try {
                        const res = await fetch(`/tasks/${taskId}/summary`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        this.data = await res.json();
                    } catch (e) {
                        this.errorMsg = 'Không tải được thông tin task.';
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
        document.addEventListener('alpine:init', () => {
            if (window.Alpine && typeof Alpine.data === 'function') {
                Alpine.data('taskSummaryPanel', taskSummaryPanel);
            }
        });
    </script>
</x-layouts.app>
