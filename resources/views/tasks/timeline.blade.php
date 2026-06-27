<x-layouts.app title="Gantt Chart">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-primary">Biểu đồ Gantt</h1>
            <p class="text-xs text-neutral mt-0.5">Tiến độ dự án — {{ $project?->name ?? 'All Projects' }}</p>
        </div>
        <div class="flex items-center gap-2">
            @include('tasks._view_switcher', ['current' => 'tasks.timeline'])
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-lg hover:bg-primary-light transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Task
            </a>
        </div>
    </div>

    @php
        // --- View mode + range setup ---
        $view = in_array(request('view'), ['week','month','quarter']) ? request('view') : 'week';
        $today = now()->startOfDay();

        $minStart = $tasks->whereNotNull('start_date')->min('start_date');
        $maxDue   = $tasks->whereNotNull('due_date')->max('due_date');
        $minDue   = $tasks->whereNotNull('due_date')->min('due_date');
        $maxStart = $tasks->whereNotNull('start_date')->max('start_date');
        $rangeStart = \Carbon\Carbon::parse($minStart ?? $minDue ?? $today);
        $rangeEnd   = \Carbon\Carbon::parse($maxDue ?? $maxStart ?? $today);

        // Snap range based on view mode & ensure includes today + some padding
        if ($view === 'week') {
            $rangeStart = $rangeStart->copy()->min($today)->subDays(7)->startOfWeek(\Carbon\Carbon::MONDAY);
            $rangeEnd   = $rangeEnd->copy()->max($today)->addDays(14)->endOfWeek(\Carbon\Carbon::SUNDAY);
        } elseif ($view === 'month') {
            $rangeStart = $rangeStart->copy()->min($today)->subMonth()->startOfMonth();
            $rangeEnd   = $rangeEnd->copy()->max($today)->addMonths(2)->endOfMonth();
        } else { // quarter
            $rangeStart = $rangeStart->copy()->min($today)->subMonths(3)->startOfQuarter();
            $rangeEnd   = $rangeEnd->copy()->max($today)->addMonths(6)->endOfQuarter();
        }

        $totalDays = $rangeStart->diffInDays($rangeEnd) + 1;

        // Cell pixel widths per day
        $dayPx = ['week' => 34, 'month' => 9, 'quarter' => 2.2][$view];
        $totalPx = (int) round($totalDays * $dayPx);

        // ---- Build header cells ----
        // Top header: month/quarter groups (and below: weeks/months/days depending on mode)
        $topCells = [];   // [['label'=>..., 'width'=>..., 'isCurrent'=>bool]]
        $subCells = [];   // [['label'=>..., 'sublabel'=>..., 'width'=>..., 'isToday'=>bool, 'isWeekend'=>bool]]

        if ($view === 'week') {
            // top = month groups, sub = day cells
            $cursor = $rangeStart->copy();
            $monthGroup = null;
            while ($cursor->lte($rangeEnd)) {
                $mKey = $cursor->format('Y-m');
                if (!$monthGroup || $monthGroup['key'] !== $mKey) {
                    if ($monthGroup) $topCells[] = $monthGroup;
                    $monthGroup = [
                        'key' => $mKey,
                        'label' => $cursor->isoFormat('MMMM YYYY'),
                        'days' => 0,
                        'isCurrent' => $cursor->isSameMonth($today),
                    ];
                }
                $monthGroup['days']++;
                $subCells[] = [
                    'main'    => (string)$cursor->day,
                    'sub'     => substr($cursor->isoFormat('dd'), 0, 2),
                    'days'    => 1,
                    'isToday' => $cursor->isSameDay($today),
                    'isWeekend' => $cursor->isWeekend(),
                    'date'    => $cursor->copy(),
                ];
                $cursor->addDay();
            }
            if ($monthGroup) $topCells[] = $monthGroup;
        } elseif ($view === 'month') {
            // top = month groups (each is full month), sub = ISO weeks
            // Build per-week cells first
            $cursor = $rangeStart->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
            $weeks = [];
            while ($cursor->lte($rangeEnd)) {
                $weekEnd = $cursor->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                $weeks[] = [
                    'start'   => $cursor->copy(),
                    'end'     => $weekEnd->copy(),
                    'wnum'    => $cursor->isoWeek,
                    'isToday' => $today->betweenIncluded($cursor, $weekEnd),
                ];
                $cursor->addWeek();
            }
            // build subCells from weeks (constrained to rangeStart/end)
            foreach ($weeks as $w) {
                $s = $w['start']->copy()->max($rangeStart);
                $e = $w['end']->copy()->min($rangeEnd);
                $days = $s->diffInDays($e) + 1;
                $subCells[] = [
                    'main' => 'W' . $w['wnum'],
                    'sub'  => $s->day . '-' . $e->day,
                    'days' => $days,
                    'isToday' => $w['isToday'],
                    'isWeekend' => false,
                    'date' => $s->copy(),
                ];
            }
            // build month groups
            $cursor = $rangeStart->copy();
            $monthGroup = null;
            while ($cursor->lte($rangeEnd)) {
                $mKey = $cursor->format('Y-m');
                if (!$monthGroup || $monthGroup['key'] !== $mKey) {
                    if ($monthGroup) $topCells[] = $monthGroup;
                    $monthGroup = [
                        'key' => $mKey,
                        'label' => $cursor->isoFormat('MMMM YYYY'),
                        'days' => 0,
                        'isCurrent' => $cursor->isSameMonth($today),
                    ];
                }
                $monthGroup['days']++;
                $cursor->addDay();
            }
            if ($monthGroup) $topCells[] = $monthGroup;
        } else { // quarter
            // top = quarter groups, sub = month names
            $cursor = $rangeStart->copy();
            $monthCells = [];
            while ($cursor->lte($rangeEnd)) {
                $mEnd = $cursor->copy()->endOfMonth()->min($rangeEnd);
                $days = $cursor->diffInDays($mEnd) + 1;
                $subCells[] = [
                    'main' => $cursor->isoFormat('MMM'),
                    'sub'  => '',
                    'days' => $days,
                    'isToday' => $cursor->isSameMonth($today),
                    'isWeekend' => false,
                    'date' => $cursor->copy(),
                ];
                $cursor = $mEnd->copy()->addDay();
            }
            $cursor = $rangeStart->copy();
            $qGroup = null;
            while ($cursor->lte($rangeEnd)) {
                $qKey = $cursor->year . '-Q' . $cursor->quarter;
                if (!$qGroup || $qGroup['key'] !== $qKey) {
                    if ($qGroup) $topCells[] = $qGroup;
                    $qStart = $cursor->copy()->startOfQuarter();
                    $qEnd = $cursor->copy()->endOfQuarter();
                    $qGroup = [
                        'key' => $qKey,
                        'label' => $qStart->isoFormat('MMM') . ' - ' . $qEnd->isoFormat('MMM YYYY'),
                        'days' => 0,
                        'isCurrent' => $today->betweenIncluded($qStart, $qEnd),
                    ];
                }
                $qGroup['days']++;
                $cursor->addDay();
            }
            if ($qGroup) $topCells[] = $qGroup;
        }

        // Today position in px
        $todayOffsetPx = null;
        if ($today->betweenIncluded($rangeStart, $rangeEnd)) {
            $todayOffsetPx = (int) round($rangeStart->diffInDays($today) * $dayPx);
        }

        // Status palette
        $statusPalette = [
            'todo'        => ['bg' => '#E2E8F0', 'fill' => '#64748B'],
            'in_progress' => ['bg' => '#DBEAFE', 'fill' => '#3B82F6'],
            'review'      => ['bg' => '#FEF3C7', 'fill' => '#F59E0B'],
            'done'        => ['bg' => '#D1FAE5', 'fill' => '#10B981'],
            'cancelled'   => ['bg' => '#FEE2E2', 'fill' => '#EF4444'],
        ];
    @endphp

    <div class="bg-white rounded-xl border border-border overflow-hidden">
        {{-- Top toolbar --}}
        <div class="flex items-center justify-between px-3 py-2 border-b border-border bg-surface-alt">
            <span class="text-[11px] text-neutral">{{ $tasks->count() }} task{{ $tasks->count() === 1 ? '' : 's' }}</span>
            <div class="flex items-center gap-1">
                @foreach(['week'=>'Week','month'=>'Month','quarter'=>'Quarter'] as $v=>$lbl)
                    <a href="{{ request()->url() }}?view={{ $v }}"
                       class="px-3 py-1 text-[11px] font-medium rounded-md transition {{ $view === $v ? 'bg-primary text-white' : 'text-neutral hover:bg-white' }}">{{ $lbl }}</a>
                @endforeach
                <button type="button" id="ganttToday" class="ml-2 px-3 py-1 text-[11px] font-medium text-secondary border border-secondary/30 rounded-md hover:bg-blue-50 transition">Today</button>
            </div>
        </div>

        <div class="flex">
            {{-- LEFT: Task names (resizable) --}}
            <div id="taskNameCol" class="relative flex-shrink-0 border-r border-border bg-white" style="width: 280px;">
                <div class="bg-surface-alt border-b border-border" style="height: 56px;">
                    <div class="px-4 h-full flex items-end pb-2">
                        <span class="text-[11px] font-semibold text-neutral uppercase tracking-wider">Task</span>
                    </div>
                </div>
                @forelse($tasks as $task)
                    @php $palette = $statusPalette[$task->status] ?? $statusPalette['in_progress']; @endphp
                    <div class="px-4 h-12 flex items-center border-b border-border-light hover:bg-blue-50/30 transition gap-2">
                        <div class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: {{ $palette['fill'] }}"></div>
                        <a href="{{ route('tasks.show', $task) }}" class="text-[13px] text-primary truncate hover:text-secondary transition flex-1">
                            <span class="text-[10px] text-neutral font-mono mr-1">T{{ $task->id }}</span>{{ $task->title }}
                        </a>
                    </div>
                @empty
                    <div class="px-4 py-12 text-center text-sm text-neutral">Chưa có task nào.</div>
                @endforelse
                <div id="taskNameResizer" class="absolute top-0 right-0 h-full w-1.5 cursor-col-resize hover:bg-secondary/60 active:bg-secondary transition z-10" title="Kéo để chỉnh độ rộng"></div>
            </div>

            {{-- RIGHT: Scrollable timeline (flex-fluid columns) --}}
            <div class="flex-1 overflow-x-auto" id="ganttScroll">
                <div style="min-width: {{ $totalPx }}px; position: relative;">
                    {{-- Header row 1: top groups --}}
                    <div class="flex bg-surface-alt border-b border-border-light" style="height: 28px;">
                        @foreach($topCells as $tc)
                            <div class="text-left text-[12px] font-semibold text-primary border-r border-border flex items-center px-3 gap-2"
                                 style="flex: {{ $tc['days'] }} {{ $tc['days'] }} 0; min-width: {{ (int) round($tc['days'] * $dayPx) }}px;">
                                <span>{{ $tc['label'] }}</span>
                                @if($tc['isCurrent'])
                                    <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded bg-secondary text-white">Current</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Header row 2: sub cells --}}
                    <div class="flex bg-white border-b border-border" style="height: 28px;">
                        @foreach($subCells as $sc)
                            <div class="flex items-center justify-center gap-1 border-r border-border-light/60 px-1"
                                 style="flex: {{ $sc['days'] }} {{ $sc['days'] }} 0; min-width: {{ (int) round($sc['days'] * $dayPx) }}px; {{ $sc['isWeekend'] ? 'background:#F8FAFC;' : '' }}">
                                @if($sc['isToday'] && $view === 'week')
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-secondary text-white text-[10px] font-bold">{{ $sc['main'] }}</span>
                                @elseif($sc['isToday'])
                                    <span class="px-1.5 py-0.5 rounded bg-secondary text-white text-[10px] font-semibold">{{ $sc['main'] }}</span>
                                @else
                                    @if(!empty($sc['sub']))
                                        <span class="text-[9px] text-neutral-light uppercase">{{ $sc['sub'] }}</span>
                                    @endif
                                    <span class="text-[11px] font-medium text-primary">{{ $sc['main'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Background grid overlay: vertical dividers + weekend tint (spans body, below tasks) --}}
                    <div class="absolute flex pointer-events-none" style="left:0; right:0; top:56px; bottom:0;">
                        @foreach($subCells as $sc)
                            <div class="border-r border-border-light/70"
                                 style="flex: {{ $sc['days'] }} {{ $sc['days'] }} 0; min-width: {{ (int) round($sc['days'] * $dayPx) }}px; {{ $sc['isWeekend'] ? 'background:#F8FAFC;' : '' }}">
                            </div>
                        @endforeach
                    </div>

                    {{-- Today column highlight (full height absolute overlay, % positioned) --}}
                    @if(!is_null($todayOffsetPx))
                        @php
                            $highlightDays = 1;
                            $highlightStart = $today;
                            if ($view === 'month') {
                                $highlightStart = $today->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                                $highlightDays = max(1, $highlightStart->diffInDays($today->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)) + 1);
                            } elseif ($view === 'quarter') {
                                $highlightStart = $today->copy()->startOfMonth();
                                $highlightDays = max(1, $highlightStart->diffInDays($today->copy()->endOfMonth()) + 1);
                            }
                            $highlightOffsetDays = $rangeStart->diffInDays($highlightStart);
                            $highlightLeftPct = ($highlightOffsetDays / $totalDays) * 100;
                            $highlightWidthPct = ($highlightDays / $totalDays) * 100;
                        @endphp
                        <div id="todayMarker" class="absolute pointer-events-none"
                             style="left: {{ $highlightLeftPct }}%; width: {{ $highlightWidthPct }}%; top: 56px; bottom: 0; background: rgba(59,130,246,0.08); border-left: 1px solid rgba(59,130,246,0.4); border-right: 1px solid rgba(59,130,246,0.4);"></div>
                    @endif

                    {{-- Task rows --}}
                    @foreach($tasks as $task)
                        @php
                            $taskStart = $task->start_date ? \Carbon\Carbon::parse($task->start_date) : null;
                            $taskEnd   = $task->due_date ? \Carbon\Carbon::parse($task->due_date) : null;
                            $hasRange  = $taskStart && $taskEnd;
                            $leftPct = 0; $widthPct = 0;
                            if ($hasRange) {
                                $offset = $rangeStart->diffInDays($taskStart);
                                $duration = max(0, $taskStart->diffInDays($taskEnd)) + 1;
                                $leftPct = ($offset / $totalDays) * 100;
                                $widthPct = ($duration / $totalDays) * 100;
                            }
                            $palette = $statusPalette[$task->status] ?? $statusPalette['in_progress'];
                            $progress = max(0, min(100, (float)$task->progress));
                        @endphp
                        <div class="relative h-12 border-b border-border-light hover:bg-blue-50/10 transition">
                            @if($hasRange)
                                <a href="{{ route('tasks.show', $task) }}"
                                   class="absolute top-1/2 -translate-y-1/2 h-6 rounded-full overflow-hidden flex items-center hover:shadow-md transition"
                                   style="left: {{ $leftPct }}%; width: {{ $widthPct }}%; background: {{ $palette['bg'] }};"
                                   title="T{{ $task->id }} {{ $task->title }} ({{ $taskStart->format('d/m') }} → {{ $taskEnd->format('d/m') }}, {{ $progress }}%)">
                                    @if($progress > 0)
                                        <div class="absolute inset-y-0 left-0 rounded-full" style="width: {{ $progress }}%; background: {{ $palette['fill'] }};"></div>
                                    @endif
                                    @if($view !== 'quarter')
                                        <span class="relative px-3 text-[11px] font-medium truncate" style="color: {{ $progress >= 50 ? '#ffffff' : $palette['fill'] }};">{{ $task->title }}</span>
                                    @endif
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const col = document.getElementById('taskNameCol');
            const handle = document.getElementById('taskNameResizer');
            if (col && handle) {
                const MIN = 160, MAX = 600;
                const saved = parseInt(localStorage.getItem('ganttTaskColWidth') || '0', 10);
                if (saved >= MIN && saved <= MAX) col.style.width = saved + 'px';
                let dragging = false, startX = 0, startW = 0;
                handle.addEventListener('mousedown', (e) => { dragging = true; startX = e.clientX; startW = col.offsetWidth; document.body.style.cursor = 'col-resize'; document.body.style.userSelect = 'none'; e.preventDefault(); });
                document.addEventListener('mousemove', (e) => { if (!dragging) return; const w = Math.max(MIN, Math.min(MAX, startW + (e.clientX - startX))); col.style.width = w + 'px'; });
                document.addEventListener('mouseup', () => { if (!dragging) return; dragging = false; document.body.style.cursor = ''; document.body.style.userSelect = ''; localStorage.setItem('ganttTaskColWidth', col.offsetWidth); });
                handle.addEventListener('dblclick', () => { col.style.width = '280px'; localStorage.setItem('ganttTaskColWidth', '280'); });
            }

            const wrap = document.getElementById('ganttScroll');
            const todayBtn = document.getElementById('ganttToday');
            function scrollToToday() {
                if (!wrap) return;
                const marker = document.getElementById('todayMarker');
                if (!marker) return;
                if (wrap.scrollWidth <= wrap.clientWidth) return; // no scroll needed
                const rect = marker.getBoundingClientRect();
                const wrapRect = wrap.getBoundingClientRect();
                const leftWithinScroll = (rect.left - wrapRect.left) + wrap.scrollLeft;
                wrap.scrollLeft = Math.max(0, leftWithinScroll - wrap.clientWidth / 2);
            }
            if (todayBtn) todayBtn.addEventListener('click', scrollToToday);
            // Auto on load
            setTimeout(scrollToToday, 0);
        })();
    </script>
</x-layouts.app>
