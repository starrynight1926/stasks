<x-layouts.app title="Department Management">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Thông tin phòng ban</h1>
            <p class="text-sm text-neutral mt-1">Quản lý Phòng ban — Cơ cấu tổ chức</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="exportFile('{{ route('export.departments') }}')" class="inline-flex items-center gap-2 px-3 py-2 border border-border text-sm font-medium text-primary rounded-lg hover:bg-surface-alt transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export
            </button>
            <button onclick="document.getElementById('importDeptsModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-3 py-2 border border-border text-sm font-medium text-primary rounded-lg hover:bg-surface-alt transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import
            </button>
            <button onclick="document.getElementById('createDeptModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Department
            </button>
        </div>
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

    {{-- Department Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($departments as $dept)
            <div class="bg-white rounded-xl border border-border p-5 hover:shadow-md transition-shadow" x-data="{ editing: false }">
                {{-- View Mode --}}
                <div x-show="!editing">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background: {{ $dept->color }}">
                            {{ strtoupper(substr($dept->code, 0, 2)) }}
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-semibold text-primary">{{ $dept->name }}</h3>
                            <p class="text-xs text-neutral">{{ $dept->code }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <button @click="editing = true" class="p-1.5 rounded-lg hover:bg-surface-alt transition" title="Edit">
                                <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('departments.destroy', $dept) }}" method="POST" onsubmit="return confirm('Delete this department?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 transition" title="Delete">
                                    <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($dept->description)
                        <p class="text-xs text-neutral mb-4">{{ Str::limit($dept->description, 120) }}</p>
                    @endif

                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="text-center p-2 bg-surface-alt rounded-lg">
                            <p class="text-lg font-bold text-primary">{{ $dept->members_count ?? $dept->member_count }}</p>
                            <p class="text-[10px] text-neutral">Members</p>
                        </div>
                        <div class="text-center p-2 bg-surface-alt rounded-lg">
                            <p class="text-lg font-bold text-primary">{{ $dept->active_projects }}</p>
                            <p class="text-[10px] text-neutral">Projects</p>
                        </div>
                        <div class="text-center p-2 bg-surface-alt rounded-lg">
                            <p class="text-lg font-bold" style="color: {{ $dept->color }}">{{ $dept->performance_score }}%</p>
                            <p class="text-[10px] text-neutral">Performance</p>
                        </div>
                    </div>

                    @if($dept->head_name)
                        <div class="flex items-center gap-2 pt-3 border-t border-border-light">
                            <div class="w-7 h-7 rounded-full bg-secondary flex items-center justify-center text-white text-[10px] font-semibold">
                                {{ strtoupper(collect(explode(' ', $dept->head_name))->map(fn($p) => $p[0] ?? '')->take(2)->join('')) }}
                            </div>
                            <div>
                                <p class="text-xs font-medium text-primary">{{ $dept->head_name }}</p>
                                <p class="text-[10px] text-neutral">Department Head</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Edit Mode --}}
                <form x-show="editing" action="{{ route('departments.update', $dept) }}" method="POST" class="space-y-3">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-neutral uppercase mb-1">Name</label>
                            <input type="text" name="name" value="{{ $dept->name }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral uppercase mb-1">Code</label>
                            <input type="text" name="code" value="{{ $dept->code }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition resize-none">{{ $dept->description }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-neutral uppercase mb-1">Color</label>
                            <input type="color" name="color" value="{{ $dept->color }}" class="w-full h-10 rounded-lg cursor-pointer border-0">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral uppercase mb-1">Department Head</label>
                            <input type="text" name="head_name" value="{{ $dept->head_name }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
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
                <p class="text-sm text-neutral">Chưa có phòng ban nào</p>
            </div>
        @endforelse
    </div>

    {{-- Create Department Modal --}}
    <div id="createDeptModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-xl">
            <h2 class="text-lg font-bold text-primary mb-4">Create New Department</h2>
            <form action="{{ route('departments.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Department Name</label>
                        <input type="text" name="name" placeholder="e.g. Engineering" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Code</label>
                        <input type="text" name="code" placeholder="e.g. ENG" maxlength="10" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Description</label>
                    <textarea name="description" rows="2" placeholder="Department description..." class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Color</label>
                        <input type="color" name="color" value="#3B82F6" class="w-full h-12 rounded-lg cursor-pointer border-0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Department Head</label>
                        <input type="text" name="head_name" placeholder="Head of department" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('createDeptModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Create Department</button>
                </div>
            </form>
        </div>
    </div>
    {{-- Import Modal --}}
    <div id="importDeptsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-xl">
            <h2 class="text-lg font-bold text-primary mb-4">Import Phòng ban từ Excel</h2>
            <form action="{{ route('import.departments') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">File Excel (.xlsx)</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="w-full text-sm border border-border rounded-lg outline-none p-2 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-surface-alt file:text-primary hover:file:bg-secondary hover:file:text-white file:transition" required>
                    <p class="text-[10px] text-neutral mt-1">Cột bắt buộc: Name. Tùy chọn: Code, Description, Color, Head Name</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('importDeptsModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt transition">Hủy</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Import</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
