<x-layouts.app title="Edit Task">
    <div class="mb-6">
        <nav class="text-xs text-neutral mb-2">
            <a href="{{ route('tasks.board') }}" class="hover:text-secondary">Tasks</a>
            <span class="mx-1">/</span>
            <a href="{{ route('tasks.show', $task) }}" class="hover:text-secondary">{{ Str::limit($task->title, 30) }}</a>
            <span class="mx-1">/</span>
            <span class="text-primary">Edit</span>
        </nav>
        <h1 class="text-2xl font-bold text-primary">Edit Task</h1>
    </div>

    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form action="{{ route('tasks.update', $task) }}" method="POST" x-data="{ priority: '{{ old('priority', $task->priority) }}' }" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf @method('PUT')
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-border p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Task Title</label>
                    <input type="text" name="title" value="{{ old('title', $task->title) }}" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg focus:border-secondary outline-none transition" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Description</label>
                    <textarea name="description" rows="4" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition resize-y">{{ old('description', $task->description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg focus:border-secondary outline-none transition">
                            @foreach(['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $task->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Progress</label>
                        <div class="px-3 py-2.5 text-sm border border-border rounded-lg bg-surface-alt text-neutral flex items-center justify-between">
                            <span>{{ (float) $task->progress }}%</span>
                            <span class="text-[10px]">Tự tính từ weight subtask</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $task->start_date?->format('Y-m-d')) }}" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg focus:border-secondary outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Visibility</label>
                        <select name="visibility" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg focus:border-secondary outline-none transition">
                            <option value="public" {{ old('visibility', $task->visibility) === 'public' ? 'selected' : '' }}>Public to Project</option>
                            <option value="private" {{ old('visibility', $task->visibility) === 'private' ? 'selected' : '' }}>Private</option>
                            <option value="team" {{ old('visibility', $task->visibility) === 'team' ? 'selected' : '' }}>Team Only</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex gap-2">
                <a href="{{ route('tasks.show', $task) }}" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg text-center hover:bg-surface-alt transition">Cancel</a>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Save Changes</button>
            </div>

            <div class="bg-white rounded-xl border border-border p-5 space-y-4">
                <h3 class="text-sm font-semibold text-primary">Attributes</h3>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Assignee</label>
                    <select name="assignee_id" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <option value="">Unassigned</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('assignee_id', $task->assignee_id) == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Priority</label>
                    <input type="hidden" name="priority" :value="priority">
                    <div class="flex border border-border rounded-lg overflow-hidden">
                        @foreach(['low', 'medium', 'high', 'urgent'] as $p)
                            <button type="button" @click="priority = '{{ $p }}'" :class="priority === '{{ $p }}' ? 'bg-primary text-white' : 'text-neutral hover:bg-surface-alt'" class="flex-1 py-1.5 text-xs font-medium capitalize transition">{{ $p }}</button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <option value="">Select department...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $task->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Tags & Labels</label>
                    <div class="flex flex-wrap gap-1.5">
                        @php $taskTagIds = old('tags', $task->tags->pluck('id')->toArray()); @endphp
                        @foreach($tags as $tag)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="hidden peer" {{ in_array($tag->id, $taskTagIds) ? 'checked' : '' }}>
                                <span class="text-[10px] font-medium px-2 py-1 rounded transition peer-checked:ring-2 peer-checked:ring-offset-1" style="background: {{ $tag->color }}20; color: {{ $tag->color }}; --tw-ring-color: {{ $tag->color }}">{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </form>

    {{-- Sub-Tasks (outside main form to avoid nested-form issues) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="lg:col-span-2">
            {{-- Tailwind safelist --}}
            <div style="display:none" aria-hidden="true" class="bg-tertiary border-tertiary line-through text-neutral text-primary hover:border-secondary"></div>

            <div class="bg-white rounded-xl border border-border p-5">
                <h3 class="text-sm font-semibold text-primary mb-3">
                    Sub-Tasks (<span id="editSubtaskCount">{{ $task->subtasks->count() }}</span>)
                </h3>

                <div id="editSubtaskList" class="space-y-2">
                    @foreach($task->subtasks as $subtask)
                        @php $done = $subtask->status === 'done'; @endphp
                        <div class="edit-subtask-row group flex items-center gap-2 p-2 rounded-lg hover:bg-surface-alt transition"
                             data-subtask-id="{{ $subtask->id }}"
                             data-status="{{ $subtask->status }}"
                             data-orig-title="{{ $subtask->title }}"
                             data-orig-weight="{{ (int) $subtask->weight }}">
                            <button type="button" class="edit-subtask-toggle w-5 h-5 rounded border-2 flex items-center justify-center transition flex-shrink-0 {{ $done ? 'bg-tertiary border-tertiary' : 'border-border hover:border-secondary' }}">
                                <svg class="edit-subtask-check w-3 h-3 text-white" style="{{ $done ? '' : 'display:none' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <input type="text" class="edit-subtask-title flex-1 px-2 py-1.5 text-sm bg-transparent border border-transparent rounded hover:border-border focus:border-secondary focus:bg-white outline-none transition {{ $done ? 'line-through text-neutral' : 'text-primary' }}"
                                   value="{{ $subtask->title }}" maxlength="200">
                            <input type="number" class="edit-subtask-weight w-16 px-2 py-1.5 text-xs bg-transparent border border-transparent rounded hover:border-border focus:border-secondary focus:bg-white outline-none transition text-neutral text-right"
                                   value="{{ (int) $subtask->weight }}" min="0" max="100" placeholder="%">
                            <span class="text-[10px] text-neutral">%</span>
                            @if($canManage)
                                <button type="button" class="edit-subtask-delete p-1 rounded hover:bg-red-50 opacity-60 hover:opacity-100 transition flex-shrink-0" title="Xóa subtask">
                                    <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                <p id="editSubtaskEmpty" class="text-xs text-neutral text-center py-3" @if($task->subtasks->count() > 0) style="display:none" @endif>Chưa có subtask</p>

                <form id="editSubtaskAddForm" class="mt-3 pt-3 border-t border-border flex gap-2">
                    <input type="text" id="editSubtaskTitle" placeholder="Thêm subtask mới..." maxlength="200" required
                           class="flex-1 px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                    <input type="number" id="editSubtaskWeight" min="0" max="100" placeholder="%"
                           class="w-16 px-2 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                    <button type="submit" id="editSubtaskAddBtn"
                            class="px-4 py-2 bg-secondary text-white text-sm font-medium rounded-lg hover:bg-secondary-dark transition disabled:opacity-50">Thêm</button>
                </form>
                <p id="editSubtaskStatus" class="text-[11px] mt-2" style="display:none"></p>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-xl border border-danger/20 p-5">
                <h3 class="text-sm font-semibold text-danger mb-2">Danger Zone</h3>
                <p class="text-xs text-neutral mb-3">Permanently delete this task and all its subtasks, comments.</p>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-danger border border-danger/30 rounded-lg hover:bg-red-50 transition">Delete Task</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const storeUrl = @json(route('tasks.subtasks.store', $task));
            const baseUrl = @json(url('tasks'));
            const list = document.getElementById('editSubtaskList');
            const empty = document.getElementById('editSubtaskEmpty');
            const countEl = document.getElementById('editSubtaskCount');
            const statusEl = document.getElementById('editSubtaskStatus');

            let statusTimer;
            function flash(msg, ok = true) {
                statusEl.textContent = msg;
                statusEl.style.display = msg ? 'block' : 'none';
                statusEl.className = 'text-[11px] mt-2 ' + (ok ? 'text-tertiary' : 'text-danger');
                clearTimeout(statusTimer);
                if (msg && ok) statusTimer = setTimeout(() => { statusEl.style.display = 'none'; }, 2000);
            }

            function applyStatusVisuals(row, status) {
                const done = status === 'done';
                row.dataset.status = status;
                const btn = row.querySelector('.edit-subtask-toggle');
                const check = row.querySelector('.edit-subtask-check');
                const titleInput = row.querySelector('.edit-subtask-title');
                if (done) {
                    btn.classList.add('bg-tertiary', 'border-tertiary');
                    btn.classList.remove('border-border', 'hover:border-secondary');
                    check.style.display = '';
                    titleInput.classList.add('line-through', 'text-neutral');
                    titleInput.classList.remove('text-primary');
                } else {
                    btn.classList.remove('bg-tertiary', 'border-tertiary');
                    btn.classList.add('border-border', 'hover:border-secondary');
                    check.style.display = 'none';
                    titleInput.classList.remove('line-through', 'text-neutral');
                    titleInput.classList.add('text-primary');
                }
            }

            async function patchSubtask(id, payload) {
                const res = await fetch(baseUrl + '/' + id + '/subtask-fields', {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });
                if (!res.ok) throw new Error('Request failed');
                return res.json();
            }

            // Toggle done
            list.addEventListener('click', async function(e) {
                const delBtn = e.target.closest('.edit-subtask-delete');
                if (delBtn) {
                    const row = delBtn.closest('.edit-subtask-row');
                    if (!row || row.dataset.pending === '1') return;
                    if (!confirm('Xóa subtask này?')) return;
                    row.dataset.pending = '1';
                    row.style.opacity = '0.4';
                    try {
                        const res = await fetch(baseUrl + '/' + row.dataset.subtaskId, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!res.ok) throw new Error('Request failed');
                        row.remove();
                        const remaining = list.querySelectorAll('.edit-subtask-row').length;
                        countEl.textContent = remaining;
                        empty.style.display = remaining === 0 ? 'block' : 'none';
                        flash('Đã xóa');
                    } catch (err) {
                        row.dataset.pending = '';
                        row.style.opacity = '';
                        flash('Xóa thất bại', false);
                    }
                    return;
                }

                const toggleBtn = e.target.closest('.edit-subtask-toggle');
                if (!toggleBtn) return;
                const row = toggleBtn.closest('.edit-subtask-row');
                if (!row || row.dataset.pending === '1') return;
                const prev = row.dataset.status;
                const next = prev === 'done' ? 'todo' : 'done';
                applyStatusVisuals(row, next);
                row.dataset.pending = '1';
                try {
                    await patchSubtask(row.dataset.subtaskId, { status: next });
                    flash('Đã lưu');
                } catch (err) {
                    applyStatusVisuals(row, prev);
                    flash('Cập nhật thất bại', false);
                } finally {
                    row.dataset.pending = '';
                }
            });

            // Inline edit on blur — title
            list.addEventListener('blur', async function(e) {
                const target = e.target;
                if (target.classList.contains('edit-subtask-title')) {
                    const row = target.closest('.edit-subtask-row');
                    const newVal = target.value.trim();
                    const origVal = row.dataset.origTitle;
                    if (!newVal) { target.value = origVal; return; }
                    if (newVal === origVal) return;
                    try {
                        await patchSubtask(row.dataset.subtaskId, { title: newVal });
                        row.dataset.origTitle = newVal;
                        flash('Đã lưu title');
                    } catch (err) {
                        target.value = origVal;
                        flash('Lưu title thất bại', false);
                    }
                } else if (target.classList.contains('edit-subtask-weight')) {
                    const row = target.closest('.edit-subtask-row');
                    const raw = target.value.trim();
                    const newVal = raw === '' ? 0 : Math.max(0, Math.min(100, parseInt(raw, 10) || 0));
                    target.value = newVal;
                    const origVal = parseInt(row.dataset.origWeight, 10);
                    if (newVal === origVal) return;
                    try {
                        await patchSubtask(row.dataset.subtaskId, { weight: newVal });
                        row.dataset.origWeight = newVal;
                        flash('Đã lưu weight');
                    } catch (err) {
                        target.value = origVal;
                        flash('Lưu weight thất bại', false);
                    }
                }
            }, true);

            // Enter on title/weight = blur to save
            list.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;
                if (e.target.classList.contains('edit-subtask-title') || e.target.classList.contains('edit-subtask-weight')) {
                    e.preventDefault();
                    e.target.blur();
                }
            });

            // Add new subtask
            document.getElementById('editSubtaskAddForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const titleInput = document.getElementById('editSubtaskTitle');
                const weightInput = document.getElementById('editSubtaskWeight');
                const btn = document.getElementById('editSubtaskAddBtn');
                const title = titleInput.value.trim();
                if (!title) return;
                btn.disabled = true;
                btn.textContent = '...';
                try {
                    const res = await fetch(storeUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ title: title, weight: weightInput.value === '' ? 0 : parseInt(weightInput.value, 10) }),
                    });
                    if (!res.ok) throw new Error('Request failed');
                    const data = await res.json();
                    appendRow(data.subtask);
                    titleInput.value = '';
                    weightInput.value = '';
                    flash('Đã thêm');
                } catch (err) {
                    flash('Thêm thất bại', false);
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'Thêm';
                }
            });

            const editCanManage = {{ $canManage ? 'true' : 'false' }};
            function appendRow(s) {
                const row = document.createElement('div');
                row.className = 'edit-subtask-row group flex items-center gap-2 p-2 rounded-lg hover:bg-surface-alt transition';
                row.dataset.subtaskId = s.id;
                row.dataset.status = s.status;
                row.dataset.origTitle = s.title;
                row.dataset.origWeight = Math.round(s.weight);
                const deleteBtn = editCanManage ? `
                    <button type="button" class="edit-subtask-delete p-1 rounded hover:bg-red-50 opacity-60 hover:opacity-100 transition flex-shrink-0" title="Xóa subtask">
                        <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>` : '';
                row.innerHTML = `
                    <button type="button" class="edit-subtask-toggle w-5 h-5 rounded border-2 flex items-center justify-center transition flex-shrink-0 border-border hover:border-secondary">
                        <svg class="edit-subtask-check w-3 h-3 text-white" style="display:none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <input type="text" class="edit-subtask-title flex-1 px-2 py-1.5 text-sm bg-transparent border border-transparent rounded hover:border-border focus:border-secondary focus:bg-white outline-none transition text-primary" maxlength="200">
                    <input type="number" class="edit-subtask-weight w-16 px-2 py-1.5 text-xs bg-transparent border border-transparent rounded hover:border-border focus:border-secondary focus:bg-white outline-none transition text-neutral text-right" min="0" max="100" placeholder="%">
                    <span class="text-[10px] text-neutral">%</span>
                    ${deleteBtn}
                `;
                row.querySelector('.edit-subtask-title').value = s.title;
                row.querySelector('.edit-subtask-weight').value = Math.round(s.weight);
                list.appendChild(row);
                empty.style.display = 'none';
                countEl.textContent = list.querySelectorAll('.edit-subtask-row').length;
            }
        })();
    </script>
</x-layouts.app>
