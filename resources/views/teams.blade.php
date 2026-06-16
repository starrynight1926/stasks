<x-layouts.app title="Team Management">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Thông tin nhân sự</h1>
            <p class="text-sm text-neutral mt-1">Quản lý Nhân sự — Đội ngũ dự án</p>
        </div>
        <button class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Add Member
        </button>
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
            <div class="bg-white rounded-xl border border-border p-5 hover:shadow-md transition-shadow">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-white text-sm font-bold">
                        {{ $member->initials() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-primary">{{ $member->name }}</h3>
                        <p class="text-xs text-neutral">{{ $member->role }}</p>
                        <p class="text-xs text-secondary">{{ $member->position }}</p>
                    </div>
                    <span class="w-2 h-2 rounded-full {{ $member->status === 'active' ? 'bg-tertiary' : 'bg-neutral-light' }} mt-1"></span>
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
</x-layouts.app>
