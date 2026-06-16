<x-layouts.app title="Calendar">
    <div x-data="calendarView()" class="space-y-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary">Lịch công việc</h1>
                <p class="text-sm text-neutral mt-1">Tasks theo ngày — dạng lịch</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="prevMonth()" class="p-2 rounded-lg hover:bg-surface-alt transition">
                    <svg class="w-5 h-5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <h2 class="text-lg font-semibold text-primary min-w-[180px] text-center" x-text="monthLabel"></h2>
                <button @click="nextMonth()" class="p-2 rounded-lg hover:bg-surface-alt transition">
                    <svg class="w-5 h-5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button @click="goToday()" class="ml-2 px-3 py-1.5 text-xs font-medium text-secondary border border-secondary/30 rounded-lg hover:bg-blue-50 transition">Hôm nay</button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-border overflow-hidden">
            {{-- Weekday headers --}}
            <div class="grid grid-cols-7 border-b border-border bg-surface-alt">
                <template x-for="day in ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']">
                    <div class="px-2 py-2.5 text-center text-xs font-semibold text-neutral uppercase" x-text="day"></div>
                </template>
            </div>

            {{-- Calendar grid --}}
            <div class="grid grid-cols-7">
                <template x-for="(cell, i) in cells" :key="i">
                    <div class="min-h-[110px] border-b border-r border-border-light p-1.5 transition"
                         :class="{ 'bg-blue-50/40': cell.isToday, 'opacity-40': !cell.currentMonth }">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium px-1.5 py-0.5 rounded-full"
                                  :class="cell.isToday ? 'bg-secondary text-white' : 'text-neutral'"
                                  x-text="cell.day"></span>
                        </div>
                        <div class="space-y-0.5">
                            <template x-for="task in getTasksForDate(cell.date)" :key="task.id">
                                <a :href="'/tasks/' + task.id"
                                   class="block text-[11px] px-1.5 py-1 rounded truncate transition hover:opacity-80"
                                   :class="priorityClass(task.priority)"
                                   :title="task.title"
                                   x-text="task.title"></a>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-4 px-2">
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded bg-red-100 border border-red-200"></div><span class="text-[10px] text-neutral">Urgent</span></div>
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded bg-amber-100 border border-amber-200"></div><span class="text-[10px] text-neutral">High</span></div>
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded bg-blue-100 border border-blue-200"></div><span class="text-[10px] text-neutral">Medium</span></div>
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded bg-gray-100 border border-gray-200"></div><span class="text-[10px] text-neutral">Low</span></div>
        </div>
    </div>

    <script>
    function calendarView() {
        const tasks = @json($calendarTasks);
        const today = new Date();

        return {
            month: today.getMonth(),
            year: today.getFullYear(),
            tasks,
            get monthLabel() {
                return new Date(this.year, this.month).toLocaleDateString('vi-VN', { month: 'long', year: 'numeric' });
            },
            get cells() {
                const first = new Date(this.year, this.month, 1);
                const last = new Date(this.year, this.month + 1, 0);
                let startDay = first.getDay() || 7;
                const cells = [];

                for (let i = startDay - 1; i > 0; i--) {
                    const d = new Date(this.year, this.month, 1 - i);
                    cells.push({ day: d.getDate(), date: this.fmt(d), currentMonth: false, isToday: false });
                }
                for (let d = 1; d <= last.getDate(); d++) {
                    const dt = new Date(this.year, this.month, d);
                    const dateStr = this.fmt(dt);
                    cells.push({ day: d, date: dateStr, currentMonth: true, isToday: dateStr === this.fmt(today) });
                }
                const remaining = 7 - (cells.length % 7);
                if (remaining < 7) {
                    for (let i = 1; i <= remaining; i++) {
                        const d = new Date(this.year, this.month + 1, i);
                        cells.push({ day: d.getDate(), date: this.fmt(d), currentMonth: false, isToday: false });
                    }
                }
                return cells;
            },
            fmt(d) {
                return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
            },
            getTasksForDate(date) {
                return this.tasks.filter(t => t.due_date === date);
            },
            priorityClass(p) {
                return { urgent: 'bg-red-100 text-red-700', high: 'bg-amber-100 text-amber-700', medium: 'bg-blue-100 text-blue-700', low: 'bg-gray-100 text-gray-600' }[p] || 'bg-gray-100 text-gray-600';
            },
            prevMonth() { if (this.month === 0) { this.month = 11; this.year--; } else { this.month--; } },
            nextMonth() { if (this.month === 11) { this.month = 0; this.year++; } else { this.month++; } },
            goToday() { this.month = today.getMonth(); this.year = today.getFullYear(); }
        };
    }
    </script>
</x-layouts.app>
