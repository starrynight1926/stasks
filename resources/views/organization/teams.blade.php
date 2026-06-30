<x-layouts.app title="Quản lý Đội nhóm">
    <div class="space-y-4" x-data="{ openCreate: false, managingTeam: null }">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary">Đội nhóm</h1>
                <p class="text-sm text-neutral mt-1">Nhóm nhân sự xuyên phòng ban</p>
            </div>
            <button @click="openCreate = true" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm đội nhóm
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-tertiary" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($teams as $team)
                <div class="bg-white rounded-xl border border-border p-5 hover:shadow-md transition" x-data="{ editing: false }">
                    <div x-show="!editing">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background: {{ $team->color }}">
                                {{ strtoupper(substr($team->code ?: $team->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-primary">{{ $team->name }}</h3>
                                <p class="text-xs text-neutral">{{ $team->company?->name ?? 'Chưa gán công ty' }}</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="editing = true" class="p-1.5 rounded-lg hover:bg-surface-alt" title="Sửa">
                                    <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('org.teams-group.destroy', $team) }}" method="POST" onsubmit="return confirm('Xóa đội nhóm này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50">
                                        <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($team->description)<p class="text-xs text-neutral mb-3">{{ Str::limit($team->description, 100) }}</p>@endif

                        <div class="pt-3 border-t border-border-light">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-neutral">{{ $team->members->count() }} thành viên</span>
                                <button @click="managingTeam = {{ $team->id }}" class="text-xs text-secondary font-medium hover:underline">Quản lý</button>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($team->members->take(8) as $m)
                                    <div class="w-7 h-7 rounded-full bg-secondary flex items-center justify-center text-white text-[10px] font-semibold" title="{{ $m->name }}">{{ $m->initials() }}</div>
                                @endforeach
                                @if($team->members->count() > 8)
                                    <div class="w-7 h-7 rounded-full bg-surface-alt flex items-center justify-center text-[10px] text-neutral font-semibold">+{{ $team->members->count() - 8 }}</div>
                                @endif
                                @if($team->members->isEmpty())
                                    <p class="text-[11px] text-neutral-light italic">Chưa có thành viên</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form x-show="editing" action="{{ route('org.teams-group.update', $team) }}" method="POST" class="space-y-3">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">Tên</label><input type="text" name="name" value="{{ $team->name }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:border-secondary outline-none" required></div>
                            <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">Mã</label><input type="text" name="code" value="{{ $team->code }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:border-secondary outline-none"></div>
                        </div>
                        <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">Công ty</label>
                            <select name="company_id" class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:border-secondary outline-none">
                                <option value="">— Không gán —</option>
                                @foreach($companies as $co)<option value="{{ $co->id }}" @selected($team->company_id == $co->id)>{{ $co->name }}</option>@endforeach
                            </select>
                        </div>
                        <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">Mô tả</label><textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:border-secondary outline-none resize-none">{{ $team->description }}</textarea></div>
                        <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">Màu</label><input type="color" name="color" value="{{ $team->color }}" class="w-full h-10 rounded-lg cursor-pointer border-0"></div>
                        <div class="flex gap-2 pt-2">
                            <button type="button" @click="editing = false" class="flex-1 px-3 py-2 text-xs font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt">Hủy</button>
                            <button type="submit" class="flex-1 px-3 py-2 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary-light">Lưu</button>
                        </div>
                    </form>

                    {{-- Manage members modal --}}
                    <div x-show="managingTeam === {{ $team->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="managingTeam = null">
                        <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 shadow-xl">
                            <h2 class="text-lg font-bold text-primary mb-4">Quản lý thành viên — {{ $team->name }}</h2>

                            <div class="mb-4">
                                <p class="text-xs font-semibold text-neutral uppercase mb-2">Đang trong đội ({{ $team->members->count() }})</p>
                                <div class="space-y-1 max-h-48 overflow-y-auto">
                                    @forelse($team->members as $m)
                                        <div class="flex items-center justify-between p-2 bg-surface-alt rounded-lg">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-secondary flex items-center justify-center text-white text-[10px] font-semibold">{{ $m->initials() }}</div>
                                                <div>
                                                    <p class="text-sm font-medium text-primary">{{ $m->name }}</p>
                                                    <p class="text-[10px] text-neutral">{{ $m->department?->name ?? '—' }}@if($m->pivot->role_in_team) · {{ $m->pivot->role_in_team }}@endif</p>
                                                </div>
                                            </div>
                                            <form action="{{ route('org.teams-group.detach', [$team, $m]) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-danger hover:underline">Gỡ</button>
                                            </form>
                                        </div>
                                    @empty
                                        <p class="text-xs text-neutral italic">Chưa có thành viên</p>
                                    @endforelse
                                </div>
                            </div>

                            <form action="{{ route('org.teams-group.attach', $team) }}" method="POST" class="space-y-3 pt-3 border-t border-border">
                                @csrf
                                <p class="text-xs font-semibold text-neutral uppercase">Thêm thành viên</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="col-span-2">
                                        <label class="block text-xs text-neutral mb-1">Chọn nhân sự (giữ Ctrl/Cmd để chọn nhiều)</label>
                                        <select name="member_ids[]" multiple size="6" class="w-full px-2 py-1.5 text-sm border border-border rounded-lg outline-none focus:border-secondary">
                                            @php $inTeamIds = $team->members->pluck('id')->all(); @endphp
                                            @foreach($members as $m)
                                                @if(!in_array($m->id, $inTeamIds))
                                                    <option value="{{ $m->id }}">{{ $m->name }} — {{ $m->department?->name ?? '—' }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-neutral mb-1">Vai trò trong đội</label>
                                        <input type="text" name="role_in_team" placeholder="VD: Leader" class="w-full px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary">
                                    </div>
                                    <div class="flex items-end"><button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light">Thêm</button></div>
                                </div>
                            </form>

                            <div class="mt-4 pt-3 border-t border-border text-right">
                                <button @click="managingTeam = null" class="px-4 py-2 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12"><p class="text-sm text-neutral">Chưa có đội nhóm nào</p></div>
            @endforelse
        </div>

        {{-- Create modal --}}
        <div x-show="openCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="openCreate = false">
            <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-xl">
                <h2 class="text-lg font-bold text-primary mb-4">Thêm đội nhóm</h2>
                <form action="{{ route('org.teams-group.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Tên *</label><input type="text" name="name" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary" required></div>
                        <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Mã</label><input type="text" name="code" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary"></div>
                    </div>
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Công ty</label>
                        <select name="company_id" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary">
                            <option value="">— Không gán —</option>
                            @foreach($companies as $co)<option value="{{ $co->id }}">{{ $co->name }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Mô tả</label><textarea name="description" rows="2" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary resize-none"></textarea></div>
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Màu</label><input type="color" name="color" value="#3B82F6" class="w-full h-12 rounded-lg cursor-pointer border-0"></div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="openCreate = false" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt">Hủy</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light">Tạo đội nhóm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
