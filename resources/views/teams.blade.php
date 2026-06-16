<x-layouts.app title="Team Management">
    <div x-data="teamList()" class="space-y-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Thông tin nhân sự</h1>
            <p class="text-sm text-neutral mt-1">Quản lý Nhân sự — Đội ngũ dự án</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="exportFile('{{ route('export.members') }}')" class="inline-flex items-center gap-2 px-3 py-2 border border-border text-sm font-medium text-primary rounded-lg hover:bg-surface-alt transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export
            </button>
            <button onclick="document.getElementById('importMembersModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-3 py-2 border border-border text-sm font-medium text-primary rounded-lg hover:bg-surface-alt transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import
            </button>
            <button onclick="document.getElementById('createMemberModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Add Member
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

    {{-- Bulk Actions Bar --}}
    <div x-show="selected.length > 0" x-cloak
         class="flex items-center gap-3 px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-xl">
        <span class="text-sm font-medium text-secondary" x-text="selected.length + ' thành viên được chọn'"></span>
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

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-primary">{{ $members->count() }}</p>
                <p class="text-xs text-neutral">Total Members</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-primary">{{ $members->where('status', 'active')->count() }}</p>
                <p class="text-xs text-neutral">Active</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-border p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-violet-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-primary">{{ $members->pluck('department_id')->unique()->filter()->count() }}</p>
                <p class="text-xs text-neutral">Departments</p>
            </div>
        </div>
    </div>

    {{-- Member Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($members as $member)
            <div class="bg-white rounded-xl border border-border p-5 hover:shadow-md transition-shadow" :class="selected.includes({{ $member->id }}) ? 'ring-2 ring-secondary/50' : ''">
                <div class="flex items-start gap-3 mb-4">
                    <input type="checkbox" value="{{ $member->id }}" x-model.number="selected" class="w-4 h-4 mt-1 rounded border-border text-secondary focus:ring-secondary cursor-pointer">
                    <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-white text-sm font-bold">
                        {{ $member->initials() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-primary">{{ $member->name }}</h3>
                        <p class="text-xs text-neutral">{{ $member->role }}</p>
                        <p class="text-xs text-secondary">{{ $member->position }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full {{ $member->status === 'active' ? 'bg-tertiary' : 'bg-neutral-light' }}"></span>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-1 rounded hover:bg-surface-alt transition">
                                <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 top-8 w-36 bg-white rounded-lg border border-border shadow-lg py-1 z-10">
                                <a href="{{ route('teams.edit', $member) }}" class="block px-3 py-2 text-xs text-primary hover:bg-surface-alt transition">Edit</a>
                                <form action="{{ route('teams.destroy', $member) }}" method="POST" onsubmit="return confirm('Remove this member?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full text-left px-3 py-2 text-xs text-danger hover:bg-red-50 transition">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div class="flex items-center gap-2 text-xs">
                        <svg class="w-3.5 h-3.5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-neutral truncate">{{ $member->email }}</span>
                    </div>
                    @if($member->department)
                        <div class="flex items-center gap-2 text-xs">
                            <svg class="w-3.5 h-3.5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                            <span class="text-neutral">{{ $member->department->name }}</span>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-border-light">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] text-neutral">Workload</span>
                        <span class="text-[10px] font-medium text-primary">{{ $member->active_tasks }} tasks · {{ $member->workload_percent }}%</span>
                    </div>
                    <div class="h-1.5 bg-surface-alt rounded-full overflow-hidden">
                        @php $wlColor = $member->workload_percent > 80 ? 'bg-danger' : ($member->workload_percent > 50 ? 'bg-warning' : 'bg-secondary'); @endphp
                        <div class="{{ $wlColor }} h-full rounded-full transition-all" style="width: {{ $member->workload_percent }}%"></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-sm text-neutral">Chưa có thành viên nào</p>
            </div>
        @endforelse
    </div>

    </div>

    <script>
    function teamList() {
        return {
            selected: [],
            bulkDeleteSelected() {
                if (!confirm(`Xóa ${this.selected.length} thành viên? Hành động này không thể hoàn tác.`)) return;
                bulkAction('{{ route('teams.bulkDestroy') }}', this.selected, '{{ route('teams') }}');
            }
        };
    }
    </script>

    {{-- Create Member Modal --}}
    <div id="createMemberModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-xl">
            <h2 class="text-lg font-bold text-primary mb-4">Add New Member</h2>
            <form action="{{ route('teams.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Full Name</label>
                        <input type="text" name="name" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Email</label>
                        <input type="email" name="email" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Role</label>
                        <input type="text" name="role" placeholder="e.g. Frontend Dev" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Position</label>
                        <input type="text" name="position" placeholder="e.g. Senior Engineer" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Department</label>
                        <select name="department_id" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                            <option value="">Select...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Phone</label>
                        <input type="text" name="phone" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('createMemberModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Add Member</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Import Modal --}}
    <div id="importMembersModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-xl">
            <h2 class="text-lg font-bold text-primary mb-4">Import Thành viên từ Excel</h2>
            <form action="{{ route('import.members') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">File Excel (.xlsx)</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="w-full text-sm border border-border rounded-lg outline-none p-2 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-surface-alt file:text-primary hover:file:bg-secondary hover:file:text-white file:transition" required>
                    <p class="text-[10px] text-neutral mt-1">Cột bắt buộc: Name, Email. Tùy chọn: Role, Position, Department, Phone, Status</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('importMembersModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt transition">Hủy</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Import</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
