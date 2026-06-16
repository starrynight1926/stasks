<x-layouts.app title="Department Management">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Thông tin phòng ban</h1>
            <p class="text-sm text-neutral mt-1">Quản lý Phòng ban — Cơ cấu tổ chức</p>
        </div>
        <button class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Department
        </button>
    </div>

    {{-- Department Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($departments as $dept)
            <div class="bg-white rounded-xl border border-border p-5 hover:shadow-md transition-shadow">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background: {{ $dept->color }}">
                        {{ strtoupper(substr($dept->code, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-primary">{{ $dept->name }}</h3>
                        <p class="text-xs text-neutral">{{ $dept->code }}</p>
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
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-sm text-neutral">Chưa có phòng ban nào</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>
