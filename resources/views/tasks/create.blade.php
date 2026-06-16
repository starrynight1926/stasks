<x-layouts.app title="Create New Task">
    <div class="mb-6">
        <nav class="text-xs text-neutral mb-2">
            <a href="{{ route('tasks.board') }}" class="hover:text-secondary">Tasks</a>
            <span class="mx-1">/</span>
            <span class="text-primary">Create New Task</span>
        </nav>
        <h1 class="text-2xl font-bold text-primary">Create New Task</h1>
        <p class="text-sm text-neutral mt-1">Define task details and breakdown structure</p>
    </div>

    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form action="{{ route('tasks.store') }}" method="POST" x-data="{
        priority: '{{ old('priority', 'high') }}',
        selectedTags: {{ json_encode(old('tags', [])) }},
        subtasks: {{ json_encode(old('subtasks', [['name' => '', 'weight' => 0]])) }}
    }" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-border p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Task Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Implement WebGL Renderer for Progress Module" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Description</label>
                    <div class="border border-border rounded-lg overflow-hidden">
                        <div class="flex items-center gap-1 px-3 py-2 bg-surface-alt border-b border-border">
                            <button type="button" class="p-1 rounded hover:bg-white transition"><svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h12"/></svg></button>
                            <button type="button" class="p-1 rounded hover:bg-white transition font-bold text-neutral text-sm">B</button>
                            <button type="button" class="p-1 rounded hover:bg-white transition italic text-neutral text-sm">I</button>
                            <button type="button" class="p-1 rounded hover:bg-white transition"><svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h7"/></svg></button>
                        </div>
                        <textarea name="description" rows="4" placeholder="Describe the requirements and success criteria..." class="w-full px-3 py-2.5 text-sm outline-none resize-y">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg focus:border-secondary outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Task Visibility</label>
                        <select name="visibility" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg focus:border-secondary outline-none transition">
                            <option value="public">Public to Project</option>
                            <option value="private">Private</option>
                            <option value="team">Team Only</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-primary">Dependencies</h3>
                </div>
                <select name="dependencies[]" multiple class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" size="3">
                    @foreach($tasks as $t)
                        <option value="{{ $t->id }}">{{ $t->title }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-neutral mt-1">Hold Ctrl to select multiple. Predecessors define Gantt chart logic.</p>
            </div>

            <div class="bg-white rounded-xl border border-border p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-primary">Sub-Tasks & Weighting</h3>
                    <button type="button" @click="subtasks.push({ name: '', weight: 0 })" class="text-xs text-secondary hover:underline">+ Add Sub-task</button>
                </div>
                <div class="space-y-2.5">
                    <template x-for="(subtask, index) in subtasks" :key="index">
                        <div class="flex items-center gap-3 p-3 bg-surface-alt rounded-lg">
                            <svg class="w-4 h-4 text-neutral flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                            <input type="text" x-model="subtask.name" :name="'subtasks['+index+'][name]'" class="flex-1 text-sm bg-transparent outline-none" placeholder="Sub-task name">
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <input type="number" x-model="subtask.weight" :name="'subtasks['+index+'][weight]'" min="0" max="100" class="w-12 text-sm text-center border border-border rounded px-1 py-0.5 outline-none focus:border-secondary">
                                <span class="text-xs text-neutral">%</span>
                            </div>
                            <button type="button" @click="subtasks.splice(index, 1)" class="p-1 rounded hover:bg-red-50 transition">
                                <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex gap-2">
                <a href="{{ route('tasks.board') }}" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg text-center hover:bg-surface-alt transition">Cancel</a>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Save Task</button>
            </div>

            <div class="bg-white rounded-xl border border-border p-5 space-y-4">
                <h3 class="text-sm font-semibold text-primary">Attributes</h3>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Assignee</label>
                    <select name="assignee_id" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <option value="">Select assignee...</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('assignee_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
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
                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <option value="">Select department...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Tags & Labels</label>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($tags as $tag)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="hidden peer" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                <span class="text-[10px] font-medium px-2 py-1 rounded transition peer-checked:ring-2 peer-checked:ring-offset-1" style="background: {{ $tag->color }}20; color: {{ $tag->color }}; --tw-ring-color: {{ $tag->color }}">{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-5">
                <h3 class="text-sm font-semibold text-primary mb-3">Attachments</h3>
                <div class="border-2 border-dashed border-border rounded-lg p-6 text-center hover:border-secondary transition cursor-pointer">
                    <svg class="w-8 h-8 text-neutral mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-xs text-neutral">Drop files here or click to upload</p>
                </div>
            </div>
        </div>
    </form>
</x-layouts.app>
