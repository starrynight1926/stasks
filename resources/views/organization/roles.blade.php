<x-layouts.app title="Vai trò & Quyền">
    <div class="space-y-4" x-data="{ editingRole: null, openCreate: false }">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary">Vai trò & Quyền</h1>
                <p class="text-sm text-neutral mt-1">Mỗi nhân sự có một vai trò; vai trò quyết định quyền truy cập</p>
            </div>
            <button @click="openCreate = true" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm vai trò
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-tertiary" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($roles as $role)
                @php $roleKeys = $role->permissions->pluck('key')->all(); @endphp
                <div class="bg-white rounded-xl border border-border p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-semibold text-primary">{{ $role->name }}</h3>
                                @if($role->is_default)<span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-secondary font-medium">Mặc định</span>@endif
                            </div>
                            <p class="text-xs text-neutral mt-0.5">{{ $role->description ?: '—' }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <button @click="editingRole = {{ $role->id }}" class="p-1.5 rounded-lg hover:bg-surface-alt" title="Sửa">
                                <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('org.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Xóa vai trò này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50" title="Xóa">
                                    <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div class="text-center p-2 bg-surface-alt rounded-lg">
                            <p class="text-lg font-bold text-primary">{{ $role->members_count }}</p>
                            <p class="text-[10px] text-neutral">Nhân sự</p>
                        </div>
                        <div class="text-center p-2 bg-surface-alt rounded-lg">
                            <p class="text-lg font-bold text-primary">{{ $role->permissions->count() }}</p>
                            <p class="text-[10px] text-neutral">Quyền</p>
                        </div>
                    </div>

                    <details class="text-xs">
                        <summary class="cursor-pointer text-secondary font-medium">Xem danh sách quyền</summary>
                        <div class="mt-2 space-y-2">
                            @foreach($modules as $modKey => $mod)
                                @php
                                    $hasAny = collect($mod['permissions'])->keys()->intersect($roleKeys)->isNotEmpty();
                                @endphp
                                @if($hasAny)
                                    <div>
                                        <p class="font-semibold text-neutral">{{ $mod['label'] }}</p>
                                        <ul class="text-neutral-light pl-3">
                                            @foreach($mod['permissions'] as $pk => $pl)
                                                @if(in_array($pk, $roleKeys))<li>• {{ $pl }}</li>@endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </details>

                    {{-- Edit modal --}}
                    <div x-show="editingRole === {{ $role->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="editingRole = null">
                        <div class="bg-white rounded-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 shadow-xl">
                            <h2 class="text-lg font-bold text-primary mb-4">Chỉnh sửa vai trò: {{ $role->name }}</h2>
                            <form action="{{ route('org.roles.update', $role) }}" method="POST" class="space-y-4">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-2 gap-4">
                                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Tên</label><input type="text" name="name" value="{{ $role->name }}" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary" required></div>
                                    <div class="flex items-end"><label class="inline-flex items-center gap-2 text-sm text-primary"><input type="checkbox" name="is_default" value="1" @checked($role->is_default) class="w-4 h-4 rounded border-border text-secondary focus:ring-secondary"> Là vai trò mặc định</label></div>
                                </div>
                                <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Mô tả</label><textarea name="description" rows="2" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary resize-none">{{ $role->description }}</textarea></div>

                                <div>
                                    <label class="block text-xs font-semibold text-neutral uppercase mb-2">Quyền</label>
                                    <div class="space-y-3 max-h-96 overflow-y-auto border border-border rounded-lg p-3">
                                        @foreach($modules as $modKey => $mod)
                                            <div>
                                                <p class="text-sm font-semibold text-primary mb-1.5">{{ $mod['label'] }}</p>
                                                <div class="grid grid-cols-2 gap-1.5">
                                                    @foreach($mod['permissions'] as $pk => $pl)
                                                        <label class="inline-flex items-center gap-2 text-xs text-neutral">
                                                            <input type="checkbox" name="permission_keys[]" value="{{ $pk }}" @checked(in_array($pk, $roleKeys)) class="w-3.5 h-3.5 rounded border-border text-secondary focus:ring-secondary">
                                                            {{ $pl }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex gap-3 pt-2">
                                    <button type="button" @click="editingRole = null" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt">Hủy</button>
                                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light">Lưu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Create modal --}}
        <div x-show="openCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="openCreate = false">
            <div class="bg-white rounded-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 shadow-xl">
                <h2 class="text-lg font-bold text-primary mb-4">Thêm vai trò</h2>
                <form action="{{ route('org.roles.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Tên *</label><input type="text" name="name" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary" required></div>
                        <div class="flex items-end"><label class="inline-flex items-center gap-2 text-sm text-primary"><input type="checkbox" name="is_default" value="1" class="w-4 h-4 rounded border-border text-secondary focus:ring-secondary"> Là vai trò mặc định</label></div>
                    </div>
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Mô tả</label><textarea name="description" rows="2" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary resize-none"></textarea></div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral uppercase mb-2">Quyền</label>
                        <div class="space-y-3 max-h-96 overflow-y-auto border border-border rounded-lg p-3">
                            @foreach($modules as $modKey => $mod)
                                <div>
                                    <p class="text-sm font-semibold text-primary mb-1.5">{{ $mod['label'] }}</p>
                                    <div class="grid grid-cols-2 gap-1.5">
                                        @foreach($mod['permissions'] as $pk => $pl)
                                            <label class="inline-flex items-center gap-2 text-xs text-neutral">
                                                <input type="checkbox" name="permission_keys[]" value="{{ $pk }}" class="w-3.5 h-3.5 rounded border-border text-secondary focus:ring-secondary">
                                                {{ $pl }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="openCreate = false" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt">Hủy</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light">Tạo vai trò</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
