<x-layouts.app title="Tag Management">
    <div x-data="tagList()" class="space-y-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Quản lý Tags</h1>
            <p class="text-sm text-neutral mt-1">Tags & Labels — Phân loại công việc</p>
        </div>
        <button onclick="document.getElementById('createTagModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Tag
        </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-tertiary" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Bulk Actions Bar --}}
    <div x-show="selected.length > 0" x-cloak
         class="flex items-center gap-3 px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-xl">
        <span class="text-sm font-medium text-secondary" x-text="selected.length + ' tag được chọn'"></span>
        <div class="flex items-center gap-2 ml-auto">
            <button @click="bulkDeleteSelected()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-danger border border-danger/30 rounded-lg hover:bg-red-50 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Xóa hàng loạt
            </button>
            <button @click="selected = []" class="p-1.5 rounded-lg hover:bg-surface-alt transition" title="Bỏ chọn">
                <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Tags Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($tags as $tag)
            <div class="bg-white rounded-xl border border-border p-5 hover:shadow-md transition-shadow" x-data="{ editing: false }" :class="selected.includes({{ $tag->id }}) ? 'ring-2 ring-secondary/50' : ''">
                {{-- View Mode --}}
                <div x-show="!editing">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" value="{{ $tag->id }}" x-model.number="selected" class="w-4 h-4 rounded border-border text-secondary focus:ring-secondary cursor-pointer">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: {{ $tag->color }}20">
                                <svg class="w-5 h-5" style="color: {{ $tag->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-primary">{{ $tag->name }}</h3>
                                <p class="text-[10px] text-neutral">{{ $tag->tasks_count }} tasks</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-6 h-6 rounded-full border-2 border-white shadow-sm" style="background: {{ $tag->color }}"></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-3 border-t border-border-light">
                        <span class="text-[10px] font-mono text-neutral">{{ $tag->color }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded font-medium" style="background: {{ $tag->color }}20; color: {{ $tag->color }}">{{ $tag->name }}</span>
                        <div class="ml-auto flex items-center gap-1">
                            <button @click="editing = true" class="p-1.5 rounded-lg hover:bg-surface-alt transition" title="Edit">
                                <svg class="w-3.5 h-3.5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Delete this tag?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 transition" title="Delete">
                                    <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Edit Mode --}}
                <form x-show="editing" action="{{ route('tags.update', $tag) }}" method="POST" class="space-y-3">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1">Name</label>
                        <input type="text" name="name" value="{{ $tag->name }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1">Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="color" value="{{ $tag->color }}" class="w-10 h-10 rounded cursor-pointer border-0">
                            <input type="text" value="{{ $tag->color }}" class="flex-1 px-3 py-2 text-sm font-mono border border-border rounded-lg outline-none" readonly>
                        </div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="editing = false" class="flex-1 px-3 py-2 text-xs font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt transition">Cancel</button>
                        <button type="submit" class="flex-1 px-3 py-2 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Save</button>
                    </div>
                </form>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-12 h-12 text-neutral/30 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                <p class="text-sm text-neutral">No tags yet</p>
            </div>
        @endforelse
    </div>

    </div>

    <script>
    function tagList() {
        return {
            selected: [],
            bulkDeleteSelected() {
                if (!confirm(`Xóa ${this.selected.length} tag? Hành động này không thể hoàn tác.`)) return;
                bulkAction('{{ route('tags.bulkDestroy') }}', this.selected, '{{ route('tags') }}');
            }
        };
    }
    </script>

    {{-- Create Tag Modal --}}
    <div id="createTagModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl border border-border w-full max-w-md p-6 shadow-xl">
            <h2 class="text-lg font-bold text-primary mb-4">Create New Tag</h2>
            <form action="{{ route('tags.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Tag Name</label>
                    <input type="text" name="name" placeholder="e.g. Frontend, Backend, Bug Fix..." class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="#3B82F6" id="newTagColor" oninput="document.getElementById('newTagColorText').value=this.value" class="w-12 h-12 rounded-lg cursor-pointer border-0">
                        <input type="text" id="newTagColorText" value="#3B82F6" class="flex-1 px-3 py-2.5 text-sm font-mono border border-border rounded-lg outline-none" readonly>
                        <div class="px-3 py-1.5 rounded text-xs font-medium" id="newTagPreview" style="background: #3B82F620; color: #3B82F6">Preview</div>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('createTagModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Create Tag</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
