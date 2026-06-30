<x-layouts.app title="Quản lý Công ty">
    <div x-data="{ selected: [] }" class="space-y-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary">Công ty</h1>
                <p class="text-sm text-neutral mt-1">Cơ cấu tổ chức — cấp cao nhất</p>
            </div>
            <button onclick="document.getElementById('createCompanyModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm công ty
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-tertiary" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($companies as $c)
                <div class="bg-white rounded-xl border border-border p-5 hover:shadow-md transition" x-data="{ editing: false }">
                    <div x-show="!editing">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr($c->code ?: $c->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-primary">{{ $c->name }}</h3>
                                <p class="text-xs text-neutral">{{ $c->code ?: '—' }}@if($c->tax_code) · MST: {{ $c->tax_code }}@endif</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="editing = true" class="p-1.5 rounded-lg hover:bg-surface-alt transition" title="Sửa">
                                    <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('org.companies.destroy', $c) }}" method="POST" onsubmit="return confirm('Xóa công ty này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 transition" title="Xóa">
                                        <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($c->description)
                            <p class="text-xs text-neutral mb-3">{{ Str::limit($c->description, 120) }}</p>
                        @endif
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="text-center p-2 bg-surface-alt rounded-lg">
                                <p class="text-lg font-bold text-primary">{{ $c->branches_count }}</p>
                                <p class="text-[10px] text-neutral">Cơ sở</p>
                            </div>
                            <div class="text-center p-2 bg-surface-alt rounded-lg">
                                <p class="text-lg font-bold text-primary">{{ $c->teams_count }}</p>
                                <p class="text-[10px] text-neutral">Đội nhóm</p>
                            </div>
                        </div>
                        <div class="text-xs text-neutral space-y-1">
                            @if($c->address)<p>📍 {{ $c->address }}</p>@endif
                            @if($c->phone)<p>📞 {{ $c->phone }}</p>@endif
                        </div>
                    </div>

                    <form x-show="editing" action="{{ route('org.companies.update', $c) }}" method="POST" class="space-y-3">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">Tên</label><input type="text" name="name" value="{{ $c->name }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:border-secondary outline-none" required></div>
                            <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">Mã</label><input type="text" name="code" value="{{ $c->code }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:border-secondary outline-none"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">MST</label><input type="text" name="tax_code" value="{{ $c->tax_code }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:border-secondary outline-none"></div>
                            <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">SĐT</label><input type="text" name="phone" value="{{ $c->phone }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:border-secondary outline-none"></div>
                        </div>
                        <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">Địa chỉ</label><input type="text" name="address" value="{{ $c->address }}" class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:border-secondary outline-none"></div>
                        <div><label class="block text-xs font-semibold text-neutral uppercase mb-1">Mô tả</label><textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-border rounded-lg focus:border-secondary outline-none resize-none">{{ $c->description }}</textarea></div>
                        <div class="flex gap-2 pt-2">
                            <button type="button" @click="editing = false" class="flex-1 px-3 py-2 text-xs font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt">Hủy</button>
                            <button type="submit" class="flex-1 px-3 py-2 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary-light">Lưu</button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="col-span-full text-center py-12"><p class="text-sm text-neutral">Chưa có công ty nào</p></div>
            @endforelse
        </div>
    </div>

    <div id="createCompanyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-xl">
            <h2 class="text-lg font-bold text-primary mb-4">Thêm công ty</h2>
            <form action="{{ route('org.companies.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Tên công ty *</label><input type="text" name="name" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary" required></div>
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Mã</label><input type="text" name="code" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Mã số thuế</label><input type="text" name="tax_code" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary"></div>
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">SĐT</label><input type="text" name="phone" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary"></div>
                </div>
                <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Địa chỉ</label><input type="text" name="address" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary"></div>
                <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Mô tả</label><textarea name="description" rows="2" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary resize-none"></textarea></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('createCompanyModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt">Hủy</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light">Tạo công ty</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
