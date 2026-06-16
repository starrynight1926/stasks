<x-layouts.app title="File Management">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Quản lý Tệp</h1>
            <p class="text-sm text-neutral mt-1">Quản lý Tệp tin — Lưu trữ tập trung</p>
        </div>
        <button class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            Upload Files
        </button>
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
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Name</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Size</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Project</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Uploaded By</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-neutral uppercase">Date</th>
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
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-12 text-sm text-neutral">Chưa có tệp tin nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
