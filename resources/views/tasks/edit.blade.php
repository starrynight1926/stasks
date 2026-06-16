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
                        <input type="number" name="progress" value="{{ old('progress', $task->progress) }}" min="0" max="100" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg focus:border-secondary outline-none transition">
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

    {{-- Delete Task (outside main form to avoid nested form bug) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-0">
        <div class="lg:col-span-2"></div>
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
</x-layouts.app>
