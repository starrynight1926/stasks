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

    <form x-data="{ subtasks: [{ name: 'Architecture Planning', weight: 20 }, { name: 'Implementation', weight: 50 }, { name: 'Testing & QA', weight: 30 }] }" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Basic Info --}}
            <div class="bg-white rounded-xl border border-border p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Task Title</label>
                    <input type="text" placeholder="e.g. Implement WebGL Renderer for Progress Module" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Description</label>
                    <div class="border border-border rounded-lg overflow-hidden">
                        <div class="flex items-center gap-1 px-3 py-2 bg-surface-alt border-b border-border">
                            <button type="button" class="p-1 rounded hover:bg-white transition"><svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h12"/></svg></button>
                            <button type="button" class="p-1 rounded hover:bg-white transition font-bold text-neutral text-sm">B</button>
                            <button type="button" class="p-1 rounded hover:bg-white transition italic text-neutral text-sm">I</button>
                            <button type="button" class="p-1 rounded hover:bg-white transition"><svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h7"/></svg></button>
                            <button type="button" class="p-1 rounded hover:bg-white transition"><svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/></svg></button>
                        </div>
                        <textarea rows="4" placeholder="Describe the requirements and success criteria..." class="w-full px-3 py-2.5 text-sm outline-none resize-y"></textarea>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Task Visibility</label>
                    <select class="w-full px-3 py-2.5 text-sm border border-border rounded-lg focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition">
                        <option>Public to Project</option>
                        <option>Private</option>
                        <option>Team Only</option>
                    </select>
                </div>
            </div>

            {{-- Dependencies --}}
            <div class="bg-white rounded-xl border border-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-primary">Dependencies</h3>
                    <button type="button" class="text-xs text-secondary hover:underline flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/></svg>
                        Link Predecessor
                    </button>
                </div>
                <p class="text-xs text-neutral">No predecessor tasks linked. Predecessors define Gantt chart logic.</p>
            </div>

            {{-- Sub-tasks & Weighting --}}
            <div class="bg-white rounded-xl border border-border p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-primary">Sub-Tasks & Weighting</h3>
                    <button type="button" @click="subtasks.push({ name: '', weight: 0 })" class="text-xs text-secondary hover:underline">+ Add Sub-task</button>
                </div>
                <div class="space-y-2.5">
                    <template x-for="(subtask, index) in subtasks" :key="index">
                        <div class="flex items-center gap-3 p-3 bg-surface-alt rounded-lg">
                            <svg class="w-4 h-4 text-neutral flex-shrink-0 cursor-grab" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                            <input type="text" x-model="subtask.name" class="flex-1 text-sm bg-transparent outline-none" placeholder="Sub-task name">
                            <div class="flex items-center gap-1.5 text-xs text-neutral">
                                <span>Assign</span>
                                <span>·</span>
                                <span>Set Date</span>
                                <span>·</span>
                                <span class="text-secondary cursor-pointer">Add Nested</span>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <input type="number" x-model="subtask.weight" min="0" max="100" class="w-12 text-sm text-center border border-border rounded px-1 py-0.5 outline-none focus:border-secondary">
                                <span class="text-xs text-neutral">%</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <a href="{{ route('tasks.board') }}" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg text-center hover:bg-surface-alt transition">Cancel</a>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Save Task</button>
            </div>

            {{-- Attributes --}}
            <div class="bg-white rounded-xl border border-border p-5 space-y-4">
                <h3 class="text-sm font-semibold text-primary">Attributes</h3>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Assignee</label>
                    <select class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <option value="">Select assignee...</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Priority</label>
                    <div class="flex border border-border rounded-lg overflow-hidden" x-data="{ priority: 'high' }">
                        @foreach(['low', 'medium', 'high', 'urgent'] as $p)
                            <button type="button" @click="priority = '{{ $p }}'" :class="priority === '{{ $p }}' ? 'bg-primary text-white' : 'text-neutral hover:bg-surface-alt'" class="flex-1 py-1.5 text-xs font-medium capitalize transition">{{ $p }}</button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Due Date</label>
                    <input type="date" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Department</label>
                    <select class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <option value="">Select department...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Tags & Labels</label>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($tags as $tag)
                            <span class="text-[10px] font-medium px-2 py-1 rounded cursor-pointer hover:opacity-80 transition" style="background: {{ $tag->color }}20; color: {{ $tag->color }}">{{ $tag->name }} ×</span>
                        @endforeach
                        <button type="button" class="w-6 h-6 rounded-full border border-dashed border-neutral flex items-center justify-center hover:border-secondary hover:text-secondary transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Attachments --}}
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
