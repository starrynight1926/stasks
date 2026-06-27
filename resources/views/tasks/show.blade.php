<x-layouts.app title="{{ $task->title }}">
    <div class="mb-6">
        <nav class="text-xs text-neutral mb-2">
            <a href="{{ route('tasks.board') }}" class="hover:text-secondary">Tasks</a>
            <span class="mx-1">/</span>
            <span class="text-primary">{{ $task->title }}</span>
        </nav>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-tertiary" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Task Header --}}
            <div class="bg-white rounded-xl border border-border p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-xl font-bold text-primary">{{ $task->title }}</h1>
                        <p class="text-xs text-neutral mt-1">Created {{ $task->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @php
                            $statusStyles = ['todo' => 'bg-gray-100 text-neutral', 'in_progress' => 'bg-blue-100 text-secondary', 'review' => 'bg-amber-100 text-amber-700', 'done' => 'bg-emerald-100 text-tertiary'];
                            $statusLabels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'];
                        @endphp
                        <span class="text-xs font-medium px-2.5 py-1 rounded {{ $statusStyles[$task->status] ?? '' }}">{{ $statusLabels[$task->status] ?? $task->status }}</span>
                        <a href="{{ route('tasks.edit', $task) }}" class="p-1.5 rounded-lg hover:bg-surface-alt transition" title="Edit Task">
                            <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <button onclick="deleteResource('{{ route('tasks.destroy', $task) }}', '{{ route('tasks.board') }}')" class="p-1.5 rounded-lg hover:bg-red-50 transition" title="Delete Task">
                            <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

                @if($task->description)
                    <div class="prose prose-sm max-w-none text-primary/80">
                        <p>{{ $task->description }}</p>
                    </div>
                @endif

                {{-- Quick Status Update --}}
                <div class="mt-4 flex items-center gap-2">
                    @foreach(['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $val => $label)
                        <form action="{{ route('tasks.updateStatus', $task) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $val }}">
                            <button type="submit" class="px-3 py-1 text-[10px] font-medium rounded-full border transition {{ $task->status === $val ? 'bg-secondary text-white border-secondary' : 'text-neutral border-border hover:border-secondary hover:text-secondary' }}">{{ $label }}</button>
                        </form>
                    @endforeach
                </div>

                <div class="mt-4 p-3 bg-surface-alt rounded-lg" x-data="{ progress: {{ (float) $task->progress }} }" x-on:task-progress.window="progress = $event.detail">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-medium text-primary">Progress</span>
                        <span class="text-xs font-bold" :class="progress > 100 ? 'text-danger' : 'text-secondary'" x-text="Math.round(progress) + '%'"></span>
                    </div>
                    <div class="h-2 bg-white rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all"
                             :class="progress > 100 ? 'bg-danger' : 'bg-secondary'"
                             :style="`width: ${Math.min(100, progress)}%`"></div>
                    </div>
                </div>
            </div>

            {{-- Tailwind safelist --}}
            <div style="display:none" aria-hidden="true" class="bg-tertiary border-tertiary line-through text-neutral text-primary hover:border-secondary bg-danger text-danger"></div>

            <style>
                .subtask-row { transition: background-color 0.15s; }
                .subtask-row:hover:not([data-lifecycle="done"]):not([data-lifecycle="done_late"]):not([data-lifecycle="cancelled"]) {
                    background-color: #F1F5F9;
                }
                .subtask-row[data-lifecycle="done"]      { background-color: #ECFDF5; }
                .subtask-row[data-lifecycle="done_late"] { background-color: #FFFBEB; }
                .subtask-row[data-lifecycle="cancelled"] { background-color: #FEF2F2; }
            </style>

            @php
                $lifecycleColor = [
                    'todo' => '#FFFFFF',
                    'in_progress' => '#3B82F6',
                    'done' => '#10B981',
                    'done_late' => '#F59E0B',
                    'cancelled' => '#EF4444',
                ];
                $lifecycleBorder = [
                    'todo' => '#CBD5E1',
                    'in_progress' => '#3B82F6',
                    'done' => '#10B981',
                    'done_late' => '#F59E0B',
                    'cancelled' => '#EF4444',
                ];
                $lifecycleLabel = [
                    'todo' => 'Plan',
                    'in_progress' => 'Đang làm',
                    'done' => 'Đã xong',
                    'done_late' => 'Trễ',
                    'cancelled' => 'Không hoàn thành',
                ];
            @endphp

            {{-- Sub-tasks --}}
            <div class="bg-white rounded-xl border border-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-primary">
                        Sub-Tasks (<span id="subtaskCount">{{ $task->subtasks->count() }}</span>)
                    </h3>
                </div>

                <div id="subtaskList" class="space-y-2">
                    @foreach($task->subtasks as $subtask)
                        @php
                            $lc = $subtask->lifecycleState();
                            $bg = $lifecycleColor[$lc];
                            $bd = $lifecycleBorder[$lc];
                            $isDone = $lc === 'done' || $lc === 'done_late';
                            $isCancelled = $lc === 'cancelled';
                            $dueOverdue = $subtask->due_date && !$isDone && !$isCancelled && $subtask->due_date->isPast();
                        @endphp
                        <div class="subtask-row group flex items-center gap-2 p-2.5 rounded-lg"
                             data-subtask-id="{{ $subtask->id }}"
                             data-status="{{ $subtask->status }}"
                             data-lifecycle="{{ $lc }}"
                             data-due="{{ $subtask->due_date?->format('Y-m-d') ?? '' }}"
                             data-done-at="{{ $subtask->done_at?->toIso8601String() ?? '' }}"
                             data-weight="{{ (float) $subtask->weight }}"
                             data-reason="{{ $subtask->cancel_reason ?? '' }}">
                            <button type="button" class="subtask-status w-5 h-5 rounded-full border-2 flex items-center justify-center transition flex-shrink-0"
                                    style="background:{{ $bg }};border-color:{{ $bd }};"
                                    title="{{ $lifecycleLabel[$lc] }} — click để chuyển trạng thái">
                                @if($isDone)
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @elseif($lc === 'in_progress')
                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6"/></svg>
                                @elseif($isCancelled)
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                            </button>

                            <div class="flex-1 flex items-center gap-2 min-w-0">
                                <span class="subtask-title flex-1 min-w-0 text-sm truncate {{ ($isDone || $isCancelled) ? 'line-through text-neutral' : 'text-primary' }}">{{ $subtask->title }}</span>
                                <span class="subtask-reason text-[11px] text-danger italic truncate flex-shrink"
                                      style="max-width:45%; {{ (!$isCancelled || !$subtask->cancel_reason) ? 'display:none;' : '' }}">
                                    (lý do hủy: <span class="subtask-reason-text">{{ $subtask->cancel_reason ?? '' }}</span>)
                                </span>
                            </div>

                            @if($subtask->assignee)
                                <div class="w-6 h-6 rounded-full bg-secondary flex items-center justify-center text-white text-[10px] font-semibold flex-shrink-0">{{ $subtask->assignee->initials() }}</div>
                            @endif
                            @if($subtask->weight > 0)
                                <span class="text-[10px] text-neutral flex-shrink-0">{{ rtrim(rtrim(number_format((float)$subtask->weight, 2, '.', ''), '0'), '.') }}%</span>
                            @endif

                            @if(!$isCancelled)
                                <button type="button" class="subtask-cancel p-1 rounded hover:bg-red-50 opacity-60 hover:opacity-100 transition flex-shrink-0" title="Đánh dấu không hoàn thành">
                                    <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M5.6 5.6l12.8 12.8"/></svg>
                                </button>
                            @endif
                            @if($canManage)
                                <button type="button" class="subtask-delete p-1 rounded hover:bg-red-50 opacity-60 hover:opacity-100 transition flex-shrink-0" title="Xóa subtask">
                                    <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            @endif
                            <button type="button" class="subtask-info w-5 h-5 rounded-full border border-border bg-white text-neutral hover:bg-secondary hover:text-white hover:border-secondary flex items-center justify-center text-[10px] font-bold transition flex-shrink-0" title="Xem chi tiết">?</button>
                        </div>
                    @endforeach
                </div>

                <p id="subtaskEmpty" class="text-xs text-neutral text-center py-3" @if($task->subtasks->count() > 0) style="display:none" @endif>No subtasks yet</p>

                <form id="subtaskAddForm" class="mt-3 pt-3 border-t border-border">
                    <div class="flex gap-2">
                        <input type="text" id="subtaskTitle" placeholder="Add a subtask..." maxlength="200" required
                               class="flex-1 px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <input type="number" id="subtaskWeight" min="0" max="100" placeholder="%"
                               class="w-16 px-2 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <button type="submit" id="subtaskAddBtn"
                                class="px-4 py-2 bg-secondary text-white text-sm font-medium rounded-lg hover:bg-secondary-dark transition disabled:opacity-50">Add</button>
                    </div>
                    <div class="mt-2 flex items-center gap-3">
                        <label class="inline-flex items-center gap-1.5 text-xs text-neutral cursor-pointer select-none">
                            <input type="checkbox" id="subtaskDueToggle" class="rounded border-border text-secondary focus:ring-secondary">
                            Due date
                        </label>
                        <input type="date" id="subtaskDueInput" style="display:none"
                               class="px-2 py-1 text-xs border border-border rounded-md outline-none focus:border-secondary transition">
                    </div>
                </form>
                <p id="subtaskError" class="text-[11px] text-danger mt-1" style="display:none"></p>

                {{-- Cancel modal --}}
                <div id="cancelModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-xl p-5 max-w-sm w-full">
                        <h3 class="text-sm font-semibold text-primary mb-2">Đánh dấu không hoàn thành</h3>
                        <p class="text-xs text-neutral mb-3">Vui lòng nhập nguyên nhân:</p>
                        <textarea id="cancelReason" rows="3" maxlength="500" placeholder="Ví dụ: Yêu cầu thay đổi, không còn cần thiết..."
                                  class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition resize-y"></textarea>
                        <div class="flex gap-2 mt-3 justify-end">
                            <button type="button" id="cancelModalClose" class="px-3 py-1.5 text-xs text-neutral hover:text-primary transition">Đóng</button>
                            <button type="button" id="cancelModalSubmit" class="px-3 py-1.5 text-xs text-white bg-danger rounded-lg hover:opacity-90 transition">Xác nhận hủy</button>
                        </div>
                    </div>
                </div>

                {{-- Info modal --}}
                <div id="infoModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-xl p-5 max-w-md w-full">
                        <div class="flex items-start justify-between mb-3 gap-3">
                            <h3 id="infoTitle" class="text-sm font-semibold text-primary break-words flex-1"></h3>
                            <button type="button" id="infoClose" class="text-neutral hover:text-primary transition flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="text-neutral w-20 flex-shrink-0">Trạng thái:</span>
                                <span id="infoStatus" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium"></span>
                            </div>
                            <div id="infoDueRow" class="flex items-center gap-2">
                                <span class="text-neutral w-20 flex-shrink-0">Due date:</span>
                                <span id="infoDue" class="text-primary"></span>
                            </div>
                            <div id="infoDoneRow" class="flex items-center gap-2">
                                <span class="text-neutral w-20 flex-shrink-0">Hoàn thành:</span>
                                <span id="infoDone" class="text-primary"></span>
                            </div>
                            <div id="infoWeightRow" class="flex items-center gap-2">
                                <span class="text-neutral w-20 flex-shrink-0">Weight:</span>
                                <span id="infoWeight" class="text-primary"></span>
                            </div>
                            <div id="infoReasonRow" class="border-t border-border pt-2 mt-2">
                                <span class="text-neutral block mb-1">Lý do hủy:</span>
                                <p id="infoReason" class="text-danger italic break-words whitespace-pre-wrap"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                window.SubtaskLifecycle = (function() {
                    const COLORS = {
                        todo:         { bg: '#FFFFFF', bd: '#CBD5E1' },
                        in_progress:  { bg: '#3B82F6', bd: '#3B82F6' },
                        done:         { bg: '#10B981', bd: '#10B981' },
                        done_late:    { bg: '#F59E0B', bd: '#F59E0B' },
                        cancelled:    { bg: '#EF4444', bd: '#EF4444' },
                    };
                    function compute(status, doneAtIso, dueIso) {
                        if (status === 'cancelled') return 'cancelled';
                        if (status === 'done') {
                            if (dueIso && doneAtIso) {
                                const due = new Date(dueIso + 'T23:59:59');
                                const done = new Date(doneAtIso);
                                if (done > due) return 'done_late';
                            }
                            return 'done';
                        }
                        if (status === 'in_progress') return 'in_progress';
                        return 'todo';
                    }
                    function svgFor(lc) {
                        if (lc === 'done' || lc === 'done_late') return '<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
                        if (lc === 'in_progress') return '<svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6"/></svg>';
                        if (lc === 'cancelled') return '<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>';
                        return '';
                    }
                    return { COLORS, compute, svgFor };
                })();

                (function() {
                    const csrf = document.querySelector('meta[name="csrf-token"]').content;
                    const storeUrl = @json(route('tasks.subtasks.store', $task));
                    const statusBaseUrl = @json(url('tasks'));
                    const list = document.getElementById('subtaskList');
                    const empty = document.getElementById('subtaskEmpty');
                    const countEl = document.getElementById('subtaskCount');
                    const errorEl = document.getElementById('subtaskError');
                    const canManage = {{ $canManage ? 'true' : 'false' }};
                    const LC = window.SubtaskLifecycle;

                    function showError(msg) {
                        errorEl.textContent = msg;
                        errorEl.style.display = msg ? 'block' : 'none';
                    }

                    function dispatchSubtaskState() {
                        const items = Array.from(list.querySelectorAll('.subtask-row')).map(r => ({
                            id: r.dataset.subtaskId,
                            lifecycle: r.dataset.lifecycle,
                            status: r.dataset.status,
                            weight: parseFloat(r.dataset.weight || '0'),
                        }));
                        window.dispatchEvent(new CustomEvent('subtasks-changed', { detail: items }));
                    }

                    function applyVisual(row, lc) {
                        row.dataset.lifecycle = lc;
                        const btn = row.querySelector('.subtask-status');
                        const c = LC.COLORS[lc];
                        btn.style.background = c.bg;
                        btn.style.borderColor = c.bd;
                        btn.innerHTML = LC.svgFor(lc);

                        const title = row.querySelector('.subtask-title');
                        const strike = (lc === 'done' || lc === 'done_late' || lc === 'cancelled');
                        if (strike) { title.classList.add('line-through', 'text-neutral'); title.classList.remove('text-primary'); }
                        else { title.classList.remove('line-through', 'text-neutral'); title.classList.add('text-primary'); }

                        const cancelBtn = row.querySelector('.subtask-cancel');
                        if (cancelBtn) cancelBtn.style.display = lc === 'cancelled' ? 'none' : '';

                        const reasonEl = row.querySelector('.subtask-reason');
                        if (reasonEl) {
                            if (lc === 'cancelled' && row.dataset.reason) {
                                reasonEl.querySelector('.subtask-reason-text').textContent = row.dataset.reason;
                                reasonEl.style.display = '';
                            } else {
                                reasonEl.style.display = 'none';
                            }
                        }

                    }

                    function nextStatus(current) {
                        // cycle: todo -> in_progress -> done -> todo
                        // cancelled/done_late -> reset to todo
                        if (current === 'todo') return 'in_progress';
                        if (current === 'in_progress') return 'done';
                        return 'todo';
                    }

                    function updateParentProgress(progress) {
                        if (progress === null || progress === undefined) return;
                        window.dispatchEvent(new CustomEvent('task-progress', { detail: progress }));
                    }

                    async function patchStatus(row, newStatus) {
                        const res = await fetch(statusBaseUrl + '/' + row.dataset.subtaskId + '/status', {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ status: newStatus }),
                        });
                        if (!res.ok) throw new Error('Request failed');
                        return res.json();
                    }

                    const infoModal = document.getElementById('infoModal');
                    const lcInfo = {
                        todo: { label: 'Plan', bg: '#F1F5F9', fg: '#475569' },
                        in_progress: { label: 'Đang làm', bg: '#DBEAFE', fg: '#1D4ED8' },
                        done: { label: 'Đã xong', bg: '#D1FAE5', fg: '#047857' },
                        done_late: { label: 'Trễ', bg: '#FEF3C7', fg: '#B45309' },
                        cancelled: { label: 'Không hoàn thành', bg: '#FEE2E2', fg: '#B91C1C' },
                    };
                    function openInfoModal(row) {
                        const lc = row.dataset.lifecycle || 'todo';
                        const info = lcInfo[lc];
                        document.getElementById('infoTitle').textContent = row.querySelector('.subtask-title').textContent.trim();
                        const stEl = document.getElementById('infoStatus');
                        stEl.textContent = info.label;
                        stEl.style.background = info.bg;
                        stEl.style.color = info.fg;
                        const due = row.dataset.due;
                        document.getElementById('infoDueRow').style.display = due ? 'flex' : 'none';
                        if (due) document.getElementById('infoDue').textContent = due.split('-').reverse().join('/');
                        const doneAt = row.dataset.doneAt;
                        document.getElementById('infoDoneRow').style.display = doneAt ? 'flex' : 'none';
                        if (doneAt) {
                            const d = new Date(doneAt);
                            document.getElementById('infoDone').textContent = d.toLocaleString('vi-VN');
                        }
                        const w = parseFloat(row.dataset.weight || '0');
                        document.getElementById('infoWeightRow').style.display = w > 0 ? 'flex' : 'none';
                        if (w > 0) document.getElementById('infoWeight').textContent = w + '%';
                        const reason = row.dataset.reason;
                        document.getElementById('infoReasonRow').style.display = (lc === 'cancelled' && reason) ? 'block' : 'none';
                        if (reason) document.getElementById('infoReason').textContent = reason;
                        infoModal.classList.remove('hidden');
                        infoModal.classList.add('flex');
                    }
                    function closeInfoModal() {
                        infoModal.classList.add('hidden');
                        infoModal.classList.remove('flex');
                    }
                    document.getElementById('infoClose').addEventListener('click', closeInfoModal);
                    infoModal.addEventListener('click', (e) => { if (e.target === infoModal) closeInfoModal(); });

                    list.addEventListener('click', async function(e) {
                        const infoBtn = e.target.closest('.subtask-info');
                        if (infoBtn) {
                            const row = infoBtn.closest('.subtask-row');
                            if (row) openInfoModal(row);
                            return;
                        }

                        const delBtn = e.target.closest('.subtask-delete');
                        if (delBtn) {
                            const row = delBtn.closest('.subtask-row');
                            if (!row || row.dataset.pending === '1') return;
                            if (!confirm('Xóa subtask này?')) return;
                            row.dataset.pending = '1';
                            row.style.opacity = '0.4';
                            showError('');
                            try {
                                const res = await fetch(statusBaseUrl + '/' + row.dataset.subtaskId, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                                });
                                if (!res.ok) throw new Error('Request failed');
                                const data = await res.json();
                                row.remove();
                                const remaining = list.querySelectorAll('.subtask-row').length;
                                countEl.textContent = remaining;
                                empty.style.display = remaining === 0 ? 'block' : 'none';
                                updateParentProgress(data.parent_progress);
                                dispatchSubtaskState();
                            } catch (err) {
                                row.dataset.pending = '';
                                row.style.opacity = '';
                                showError('Xóa thất bại.');
                            }
                            return;
                        }

                        const cancelBtn = e.target.closest('.subtask-cancel');
                        if (cancelBtn) {
                            const row = cancelBtn.closest('.subtask-row');
                            openCancelModal(row);
                            return;
                        }

                        const statusBtn = e.target.closest('.subtask-status');
                        if (!statusBtn) return;
                        const row = statusBtn.closest('.subtask-row');
                        if (!row || row.dataset.pending === '1') return;

                        const prev = row.dataset.status;
                        const prevLc = row.dataset.lifecycle;
                        const next = nextStatus(prev);
                        row.dataset.status = next;
                        if (next === 'done') {
                            row.dataset.doneAt = new Date().toISOString();
                        } else {
                            row.dataset.doneAt = '';
                        }
                        const newLc = LC.compute(next, row.dataset.doneAt, row.dataset.due);
                        applyVisual(row, newLc);
                        row.dataset.pending = '1';
                        showError('');
                        try {
                            const data = await patchStatus(row, next);
                            if (data.lifecycle) applyVisual(row, data.lifecycle);
                            updateParentProgress(data.parent_progress);
                            dispatchSubtaskState();
                        } catch (err) {
                            row.dataset.status = prev;
                            applyVisual(row, prevLc);
                            showError('Cập nhật thất bại.');
                        } finally {
                            row.dataset.pending = '';
                        }
                    });

                    // Cancel modal
                    const cancelModal = document.getElementById('cancelModal');
                    const cancelReason = document.getElementById('cancelReason');
                    let cancelTargetRow = null;
                    function openCancelModal(row) {
                        cancelTargetRow = row;
                        cancelReason.value = '';
                        cancelModal.classList.remove('hidden');
                        cancelModal.classList.add('flex');
                        setTimeout(() => cancelReason.focus(), 50);
                    }
                    function closeCancelModal() {
                        cancelModal.classList.add('hidden');
                        cancelModal.classList.remove('flex');
                        cancelTargetRow = null;
                    }
                    document.getElementById('cancelModalClose').addEventListener('click', closeCancelModal);
                    cancelModal.addEventListener('click', (e) => { if (e.target === cancelModal) closeCancelModal(); });
                    document.getElementById('cancelModalSubmit').addEventListener('click', async function() {
                        const reason = cancelReason.value.trim();
                        if (!reason) { cancelReason.focus(); return; }
                        if (!cancelTargetRow) return;
                        const row = cancelTargetRow;
                        try {
                            const res = await fetch(statusBaseUrl + '/' + row.dataset.subtaskId + '/cancel', {
                                method: 'PATCH',
                                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                                body: JSON.stringify({ reason }),
                            });
                            if (!res.ok) throw new Error('Request failed');
                            const data = await res.json();
                            row.dataset.status = 'cancelled';
                            row.dataset.reason = reason;
                            applyVisual(row, 'cancelled');
                            updateParentProgress(data.parent_progress);
                            dispatchSubtaskState();
                            closeCancelModal();
                        } catch (err) {
                            showError('Hủy thất bại.');
                        }
                    });

                    // Due date checkbox toggle
                    const dueToggle = document.getElementById('subtaskDueToggle');
                    const dueInput = document.getElementById('subtaskDueInput');
                    dueToggle.addEventListener('change', function() {
                        if (this.checked) {
                            dueInput.style.display = '';
                            dueInput.focus();
                        } else {
                            dueInput.style.display = 'none';
                            dueInput.value = '';
                        }
                    });

                    document.getElementById('subtaskAddForm').addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const titleInput = document.getElementById('subtaskTitle');
                        const weightInput = document.getElementById('subtaskWeight');
                        const addBtn = document.getElementById('subtaskAddBtn');
                        const title = titleInput.value.trim();
                        if (!title) return;
                        const duePicked = dueToggle.checked && dueInput.value ? dueInput.value : null;
                        addBtn.disabled = true;
                        addBtn.textContent = '...';
                        showError('');
                        try {
                            const res = await fetch(storeUrl, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                                body: JSON.stringify({ title, weight: weightInput.value === '' ? 0 : parseInt(weightInput.value, 10), due_date: duePicked }),
                            });
                            if (!res.ok) throw new Error('Request failed');
                            const data = await res.json();
                            appendSubtask(data.subtask);
                            titleInput.value = '';
                            weightInput.value = '';
                            dueToggle.checked = false;
                            dueInput.value = '';
                            dueInput.style.display = 'none';
                            updateParentProgress(data.parent_progress);
                            dispatchSubtaskState();
                        } catch (err) {
                            showError('Không thể thêm subtask.');
                        } finally {
                            addBtn.disabled = false;
                            addBtn.textContent = 'Add';
                        }
                    });

                    function appendSubtask(s) {
                        const row = document.createElement('div');
                        row.className = 'subtask-row group flex items-center gap-2 p-2.5 rounded-lg';
                        row.dataset.subtaskId = s.id;
                        row.dataset.status = s.status;
                        row.dataset.lifecycle = 'todo';
                        row.dataset.due = s.due_date || '';
                        row.dataset.doneAt = '';
                        row.dataset.weight = s.weight;
                        row.dataset.reason = '';
                        const weightLabel = (Math.round(s.weight * 100) / 100).toString();
                        const deleteBtn = canManage ? `
                            <button type="button" class="subtask-delete p-1 rounded hover:bg-red-50 opacity-60 hover:opacity-100 transition flex-shrink-0" title="Xóa subtask">
                                <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>` : '';
                        row.innerHTML = `
                            <button type="button" class="subtask-status w-5 h-5 rounded-full border-2 flex items-center justify-center transition flex-shrink-0" style="background:#FFFFFF;border-color:#CBD5E1;" title="Plan — click để chuyển trạng thái"></button>
                            <div class="flex-1 flex items-center gap-2 min-w-0">
                                <span class="subtask-title flex-1 min-w-0 text-sm truncate text-primary"></span>
                                <span class="subtask-reason text-[11px] text-danger italic truncate flex-shrink" style="display:none; max-width:45%;">(lý do hủy: <span class="subtask-reason-text"></span>)</span>
                            </div>
                            ${s.weight > 0 ? `<span class="text-[10px] text-neutral flex-shrink-0">${weightLabel}%</span>` : ''}
                            <button type="button" class="subtask-cancel p-1 rounded hover:bg-red-50 opacity-60 hover:opacity-100 transition flex-shrink-0" title="Đánh dấu không hoàn thành">
                                <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M5.6 5.6l12.8 12.8"/></svg>
                            </button>
                            ${deleteBtn}
                            <button type="button" class="subtask-info w-5 h-5 rounded-full border border-border bg-white text-neutral hover:bg-secondary hover:text-white hover:border-secondary flex items-center justify-center text-[10px] font-bold transition flex-shrink-0" title="Xem chi tiết">?</button>
                        `;
                        row.querySelector('.subtask-title').textContent = s.title;
                        list.appendChild(row);
                        empty.style.display = 'none';
                        countEl.textContent = list.querySelectorAll('.subtask-row').length;
                    }

                    dispatchSubtaskState();
                })();
            </script>

            {{-- Comments --}}
            @php
                $memberNames = $members->pluck('name')->values()->all();
                $renderCommentBody = function ($text) use ($memberNames) {
                    $escaped = e($text);
                    if (empty($memberNames)) return nl2br($escaped);
                    usort($memberNames, fn($a, $b) => strlen($b) - strlen($a));
                    foreach ($memberNames as $name) {
                        $escapedName = e($name);
                        $pattern = '/@' . preg_quote($escapedName, '/') . '\b/u';
                        $escaped = preg_replace($pattern, '<span class="inline-block px-1.5 py-0.5 rounded bg-blue-100 text-secondary font-medium">@' . $escapedName . '</span>', $escaped);
                    }
                    return nl2br($escaped);
                };
            @endphp

            <div class="bg-white rounded-xl border border-border p-5">
                <h3 class="text-sm font-semibold text-primary mb-4">Activity & Comments</h3>
                <div class="space-y-4">
                    @forelse($task->comments as $comment)
                        @php
                            $authorName = $comment->member?->name ?? $comment->author_name ?? 'Unknown';
                            $authorInitials = strtoupper(collect(explode(' ', $authorName))->map(fn ($p) => $p[0] ?? '')->take(2)->join(''));
                        @endphp
                        <div class="flex gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-secondary flex-shrink-0 flex items-center justify-center text-white text-[10px] font-semibold">
                                {{ $authorInitials ?: '?' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-medium text-primary">{{ $authorName }}</span>
                                    <span class="text-[10px] text-neutral">{{ $comment->created_at->diffForHumans() }}</span>
                                    @if($canManage)
                                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="opacity-60 hover:opacity-100 transition" onsubmit="return confirm('Delete comment?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-[10px] text-danger hover:underline">Delete</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="text-sm text-primary/80 whitespace-pre-wrap break-words">{!! $renderCommentBody($comment->body) !!}</div>
                                @if($comment->files->isNotEmpty())
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach($comment->files as $cf)
                                            <a href="{{ route('files.show', $cf) }}" target="_blank"
                                               class="inline-flex items-center gap-1.5 px-2 py-1 bg-surface-alt hover:bg-surface rounded border border-border text-xs text-primary transition">
                                                <svg class="w-3.5 h-3.5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span class="truncate max-w-[160px]">{{ $cf->original_name }}</span>
                                                <span class="text-[10px] text-neutral">{{ $cf->sizeForHumans() }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-neutral text-center py-4">No comments yet</p>
                    @endforelse
                </div>

                <div class="mt-4 pt-4 border-t border-border" x-data="commentBox({ members: {{ Js::from($memberNames) }} })">
                    <form action="{{ route('comments.store', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-2" @submit="onSubmit($event)">
                        @csrf
                        <div class="relative">
                            <textarea name="body" x-model="body" x-ref="bodyInput"
                                      @input="onInput($event)" @keydown="onKeyDown($event)"
                                      placeholder="Viết comment... gõ @ để tag thành viên"
                                      rows="2" maxlength="2000"
                                      class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition resize-y"
                                      required></textarea>

                            <div x-show="showMention && filteredMembers.length > 0" x-cloak
                                 class="absolute z-20 left-3 bottom-full mb-1 bg-white border border-border rounded-lg shadow-lg py-1 min-w-[180px] max-h-48 overflow-auto">
                                <template x-for="(m, i) in filteredMembers" :key="m">
                                    <button type="button" @click="pickMention(m)" @mouseenter="mentionIndex = i"
                                            :class="i === mentionIndex ? 'bg-surface-alt' : ''"
                                            class="w-full text-left px-3 py-1.5 text-sm text-primary hover:bg-surface-alt transition">
                                        <span class="text-secondary font-medium">@</span><span x-text="m"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="cursor-pointer p-2 rounded-lg hover:bg-surface-alt transition" title="Đính kèm tệp">
                                <input type="file" name="attachments[]" multiple class="hidden" @change="onFilesPicked($event)">
                                <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            </label>
                            <template x-for="(f, i) in pickedFiles" :key="i">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-surface-alt rounded text-[11px] text-primary">
                                    <span class="truncate max-w-[140px]" x-text="f.name"></span>
                                </span>
                            </template>
                            <div class="flex-1"></div>
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">Send</button>
                        </div>
                    </form>
                    <p class="text-[10px] text-neutral mt-1">Posting as {{ session('user_name', 'Unknown') }}</p>
                </div>
            </div>

            <script>
                function commentBox(config) {
                    return {
                        members: config.members || [],
                        body: '',
                        showMention: false,
                        mentionQuery: '',
                        mentionStart: -1,
                        mentionIndex: 0,
                        filteredMembers: [],
                        pickedFiles: [],

                        onInput(e) {
                            const el = e.target;
                            const pos = el.selectionStart;
                            const text = el.value;
                            const upto = text.slice(0, pos);
                            const match = upto.match(/(?:^|\s)@([^\s@]*)$/);
                            if (match) {
                                this.mentionStart = pos - match[1].length - 1;
                                this.mentionQuery = match[1].toLowerCase();
                                this.filteredMembers = this.members
                                    .filter(m => m.toLowerCase().includes(this.mentionQuery))
                                    .slice(0, 6);
                                this.mentionIndex = 0;
                                this.showMention = this.filteredMembers.length > 0;
                            } else {
                                this.showMention = false;
                            }
                        },

                        onKeyDown(e) {
                            if (!this.showMention) return;
                            if (e.key === 'ArrowDown') {
                                e.preventDefault();
                                this.mentionIndex = (this.mentionIndex + 1) % this.filteredMembers.length;
                            } else if (e.key === 'ArrowUp') {
                                e.preventDefault();
                                this.mentionIndex = (this.mentionIndex - 1 + this.filteredMembers.length) % this.filteredMembers.length;
                            } else if (e.key === 'Enter' || e.key === 'Tab') {
                                e.preventDefault();
                                this.pickMention(this.filteredMembers[this.mentionIndex]);
                            } else if (e.key === 'Escape') {
                                this.showMention = false;
                            }
                        },

                        pickMention(name) {
                            const el = this.$refs.bodyInput;
                            const pos = el.selectionStart;
                            const before = this.body.slice(0, this.mentionStart);
                            const after = this.body.slice(pos);
                            const insert = '@' + name + ' ';
                            this.body = before + insert + after;
                            this.showMention = false;
                            this.$nextTick(() => {
                                const newPos = before.length + insert.length;
                                el.focus();
                                el.setSelectionRange(newPos, newPos);
                            });
                        },

                        onFilesPicked(e) {
                            this.pickedFiles = Array.from(e.target.files || []);
                        },

                        onSubmit(e) {
                            if (!this.body.trim()) e.preventDefault();
                        },
                    };
                }
            </script>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Progress chart (vanilla JS, no Alpine — SVG namespace safe) --}}
            <div class="bg-white rounded-xl border border-border p-5 relative">
                <h3 class="text-sm font-semibold text-primary mb-3">Tiến độ</h3>
                <div class="flex items-center gap-4">
                    <div class="relative" style="width:110px;height:110px;flex-shrink:0;">
                        <svg id="chartSvg" viewBox="0 0 42 42" class="w-full h-full -rotate-90">
                            <circle cx="21" cy="21" r="15.915" fill="none" stroke="#E2E8F0" stroke-width="4"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span id="chartPercent" class="text-lg font-bold text-primary">0%</span>
                            <span id="chartCount" class="text-[9px] text-neutral">0/0</span>
                        </div>
                    </div>
                    <div id="chartLegend" class="flex-1 space-y-1"></div>
                </div>
                <p id="chartEmpty" class="text-xs text-neutral text-center py-3" style="display:none">Chưa có subtask để hiển thị</p>
                <div id="chartTooltip" class="absolute pointer-events-none px-2 py-1 bg-primary text-white text-[10px] rounded shadow-lg z-20 whitespace-nowrap" style="display:none"></div>
            </div>

            <script>
                (function() {
                    const SVG_NS = 'http://www.w3.org/2000/svg';
                    const COLORS = { todo: '#CBD5E1', in_progress: '#3B82F6', done: '#10B981', done_late: '#F59E0B', cancelled: '#EF4444' };
                    const LABELS = { done: 'Đã xong', done_late: 'Trễ', in_progress: 'Đang làm', todo: 'Plan', cancelled: 'Không HT' };
                    const ORDER = ['done', 'done_late', 'in_progress', 'todo', 'cancelled'];

                    const svg = document.getElementById('chartSvg');
                    const percentEl = document.getElementById('chartPercent');
                    const countEl = document.getElementById('chartCount');
                    const legendEl = document.getElementById('chartLegend');
                    const emptyEl = document.getElementById('chartEmpty');
                    const tooltip = document.getElementById('chartTooltip');
                    const tooltipParent = tooltip.parentElement;

                    function onSegEnter(e) {
                        const c = e.currentTarget;
                        const k = c.dataset.key;
                        tooltip.innerHTML = `<span style="display:inline-block;width:8px;height:8px;border-radius:2px;background:${COLORS[k]};margin-right:4px;vertical-align:middle;"></span>${LABELS[k]}: ${c.dataset.count} (${c.dataset.percent}%)`;
                        tooltip.style.display = 'block';
                        c.setAttribute('stroke-width', '5.5');
                    }
                    function onSegMove(e) {
                        const rect = tooltipParent.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        tooltip.style.left = (x + 12) + 'px';
                        tooltip.style.top = (y - 22) + 'px';
                    }
                    function onSegLeave(e) {
                        tooltip.style.display = 'none';
                        e.currentTarget.setAttribute('stroke-width', '4');
                    }

                    function render(items) {
                        // Clear previous segments + legend
                        svg.querySelectorAll('circle.chart-seg').forEach(c => c.remove());
                        legendEl.innerHTML = '';

                        const counts = { todo: 0, in_progress: 0, done: 0, done_late: 0, cancelled: 0 };
                        let totalW = 0, doneW = 0, activeW = 0;
                        items.forEach(it => {
                            const lc = it.lifecycle || 'todo';
                            counts[lc] = (counts[lc] || 0) + 1;
                            const w = parseFloat(it.weight) || 0;
                            totalW += w;
                            if (lc !== 'cancelled') {
                                activeW += w;
                                if (lc === 'done' || lc === 'done_late') doneW += w;
                            }
                        });

                        const total = items.length;
                        const cancelled = counts.cancelled;
                        const active = total - cancelled;
                        const doneCount = counts.done + counts.done_late;

                        emptyEl.style.display = total === 0 ? 'block' : 'none';

                        // Center text: weight-based progress if any weight, else count-based
                        let pct;
                        if (activeW > 0) pct = Math.round(doneW);
                        else if (active > 0) pct = Math.round((doneCount / active) * 100);
                        else pct = 0;
                        percentEl.textContent = pct + '%';
                        percentEl.classList.toggle('text-danger', pct > 100);
                        percentEl.classList.toggle('text-primary', pct <= 100);
                        countEl.textContent = doneCount + '/' + active;

                        // Draw segments (count-based, matches legend)
                        if (total > 0) {
                            let offset = 0;
                            for (const k of ORDER) {
                                if (!counts[k]) continue;
                                const length = (counts[k] / total) * 100;
                                const c = document.createElementNS(SVG_NS, 'circle');
                                c.classList.add('chart-seg');
                                c.setAttribute('cx', '21');
                                c.setAttribute('cy', '21');
                                c.setAttribute('r', '15.915');
                                c.setAttribute('fill', 'none');
                                c.setAttribute('stroke-width', '4');
                                c.setAttribute('stroke', COLORS[k]);
                                c.setAttribute('stroke-dasharray', length + ' ' + (100 - length));
                                c.setAttribute('stroke-dashoffset', String(-offset));
                                c.style.cursor = 'pointer';
                                c.style.transition = 'stroke-width 0.15s';
                                c.dataset.key = k;
                                c.dataset.count = counts[k];
                                c.dataset.percent = length.toFixed(1);
                                c.addEventListener('mouseenter', onSegEnter);
                                c.addEventListener('mousemove', onSegMove);
                                c.addEventListener('mouseleave', onSegLeave);
                                svg.appendChild(c);
                                offset += length;
                            }
                        }

                        // Build legend
                        for (const k of ORDER) {
                            if (!counts[k]) continue;
                            const pctOfTotal = total > 0 ? (counts[k] / total) * 100 : 0;
                            const row = document.createElement('div');
                            row.className = 'flex items-center gap-2 text-[11px]';
                            row.innerHTML = `
                                <span class="w-2.5 h-2.5 rounded-sm flex-shrink-0" style="background:${COLORS[k]}"></span>
                                <span class="text-neutral flex-1">${LABELS[k]}</span>
                                <span class="font-medium text-primary">${counts[k]} <span class="text-neutral font-normal">(${pctOfTotal.toFixed(0)}%)</span></span>
                            `;
                            legendEl.appendChild(row);
                        }
                    }

                    function snapshot() {
                        const list = document.getElementById('subtaskList');
                        if (!list) return [];
                        return Array.from(list.querySelectorAll('.subtask-row')).map(r => ({
                            id: r.dataset.subtaskId,
                            lifecycle: r.dataset.lifecycle || 'todo',
                            status: r.dataset.status,
                            weight: parseFloat(r.dataset.weight || '0'),
                        }));
                    }

                    window.addEventListener('subtasks-changed', (e) => render(e.detail || []));
                    // Initial render from DOM
                    render(snapshot());
                })();
            </script>

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

            {{-- Files (all files attached to this task, including via comments) --}}
            <div class="bg-white rounded-xl border border-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-primary">Files <span class="text-xs text-neutral font-normal">({{ $allFiles->count() }})</span></h3>
                    <form action="{{ route('tasks.files.store', $task) }}" method="POST" enctype="multipart/form-data" id="taskFileUploadForm">
                        @csrf
                        <label class="cursor-pointer text-[11px] text-secondary hover:underline">
                            <input type="file" name="file" class="hidden" onchange="document.getElementById('taskFileUploadForm').submit()">
                            + Upload
                        </label>
                    </form>
                </div>
                @forelse($allFiles as $file)
                    <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-surface-alt transition group">
                        <div class="w-8 h-8 bg-surface-alt rounded flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <a href="{{ route('files.show', $file) }}" target="_blank" class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-primary truncate hover:text-secondary transition">{{ $file->original_name }}</p>
                            <p class="text-[10px] text-neutral">
                                {{ $file->sizeForHumans() }}
                                @if($file->comment_id)
                                    <span class="ml-1 text-[9px] px-1 rounded bg-blue-50 text-secondary">from comment</span>
                                @endif
                            </p>
                        </a>
                        <a href="{{ route('files.show', ['file' => $file, 'download' => 1]) }}"
                           class="p-1 rounded hover:bg-blue-50 opacity-60 hover:opacity-100 transition" title="Tải xuống">
                            <svg class="w-3 h-3 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                        @if($canManage)
                            <button type="button"
                                    onclick="if(confirm('Xóa file này?')) deleteResource('{{ route('files.destroy', $file) }}', window.location.href)"
                                    class="p-1 rounded hover:bg-red-50 opacity-60 hover:opacity-100 transition" title="Xóa">
                                <svg class="w-3 h-3 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="border-2 border-dashed border-border rounded-lg p-4 text-center">
                        <svg class="w-6 h-6 text-neutral mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-[10px] text-neutral">Chưa có file nào</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
