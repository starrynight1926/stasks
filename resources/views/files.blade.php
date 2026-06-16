<x-layouts.app title="File Management">
    <div x-data="fileList()" class="space-y-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Quản lý Tệp</h1>
            <p class="text-sm text-neutral mt-1">Quản lý Tệp tin — Lưu trữ tập trung</p>
        </div>
        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            Upload Files
        </button>
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
        <span class="text-sm font-medium text-secondary" x-text="selected.length + ' file được chọn'"></span>
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

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl border border-border p-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="flex-1 relative">
                <svg class="w-4 h-4 text-neutral absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Search files..." class="w-full pl-9 pr-4 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
            </div>
            <select class="px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                <option>All Types</option>
                <option>Documents</option>
                <option>Images</option>
                <option>Spreadsheets</option>
            </select>
        </div>
    </div>

    {{-- File List --}}
    <div class="bg-white rounded-xl border border-border overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border bg-surface-alt">
                    <th class="w-10 px-3 py-3">
                        <input type="checkbox" @change="toggleAll($event)" :checked="allSelected" class="w-4 h-4 rounded border-border text-secondary focus:ring-secondary cursor-pointer">
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Name</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Size</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Project</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Uploaded By</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Date</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-neutral uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                    @php
                        $typeIcons = [
                            'document' => ['icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'color' => 'text-secondary bg-blue-50'],
                            'image' => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'text-tertiary bg-emerald-50'],
                            'spreadsheet' => ['icon' => 'M3 10h18M3 14h18M12 3v18', 'color' => 'text-tertiary bg-emerald-50'],
                        ];
                        $typeInfo = $typeIcons[$file->type] ?? $typeIcons['document'];
                    @endphp
                    <tr class="border-b border-border-light hover:bg-surface-alt/50 transition">
                        <td class="w-10 px-3 py-3">
                            <input type="checkbox" value="{{ $file->id }}" x-model.number="selected" class="w-4 h-4 rounded border-border text-secondary focus:ring-secondary cursor-pointer">
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $typeInfo['color'] }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $typeInfo['icon'] }}"/></svg>
                                </div>
                                <span class="text-sm font-medium text-primary">{{ $file->original_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3"><span class="text-xs text-neutral capitalize">{{ $file->type }}</span></td>
                        <td class="px-4 py-3"><span class="text-xs text-neutral">{{ $file->sizeForHumans() }}</span></td>
                        <td class="px-4 py-3"><span class="text-xs text-neutral">{{ $file->project?->name ?? '—' }}</span></td>
                        <td class="px-4 py-3">
                            @if($file->uploader)
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-secondary flex items-center justify-center text-white text-[8px] font-semibold">{{ $file->uploader->initials() }}</div>
                                    <span class="text-xs text-primary">{{ $file->uploader->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-neutral">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3"><span class="text-xs text-neutral">{{ $file->created_at->format('M d, Y') }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="deleteResource('{{ route('files.destroy', $file) }}', '{{ route('files') }}')" class="p-1.5 rounded-lg hover:bg-red-50 transition" title="Delete">
                                <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-12 text-sm text-neutral">Chưa có tệp tin nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    </div>

    <script>
    function fileList() {
        return {
            selected: [],
            fileIds: @json($files->pluck('id')),
            get allSelected() {
                return this.fileIds.length > 0 && this.selected.length === this.fileIds.length;
            },
            toggleAll(e) {
                this.selected = e.target.checked ? [...this.fileIds] : [];
            },
            bulkDeleteSelected() {
                if (!confirm(`Xóa ${this.selected.length} file? Hành động này không thể hoàn tác.`)) return;
                bulkAction('{{ route('files.bulkDestroy') }}', this.selected, '{{ route('files') }}');
            }
        };
    }
    </script>

    {{-- Upload Modal --}}
    <div id="uploadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-xl">
            <h2 class="text-lg font-bold text-primary mb-4">Upload File</h2>
            <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">File</label>
                    <input type="file" name="file" class="w-full text-sm border border-border rounded-lg outline-none p-2 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-surface-alt file:text-primary hover:file:bg-secondary hover:file:text-white file:transition" required>
                    <p class="text-[10px] text-neutral mt-1">Max 20MB</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Project</label>
                    <select name="project_id" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <option value="">No project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Uploaded By</label>
                    <select name="uploaded_by" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Upload</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
