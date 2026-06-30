<x-layouts.app title="Quản lý Cơ sở">
    <div class="space-y-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary">Cơ sở</h1>
                <p class="text-sm text-neutral mt-1">Cơ sở / chi nhánh thuộc Công ty</p>
            </div>
            <button onclick="document.getElementById('createBranchModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-light transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm cơ sở
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-tertiary" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
        @endif

        <div class="bg-white border border-border rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-surface-alt">
                    <tr class="text-left text-xs font-semibold text-neutral uppercase">
                        <th class="px-4 py-3">Tên</th>
                        <th class="px-4 py-3">Mã</th>
                        <th class="px-4 py-3">Công ty</th>
                        <th class="px-4 py-3">SĐT / Địa chỉ</th>
                        <th class="px-4 py-3 text-center">Phòng ban</th>
                        <th class="px-4 py-3 text-center">Nhân sự</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $b)
                        <tr class="border-t border-border-light hover:bg-surface-alt/40" x-data="{ editing: false }">
                            <template x-if="!editing">
                                <td class="px-4 py-3 font-medium text-primary">{{ $b->name }}</td>
                            </template>
                            <template x-if="editing"><td class="px-4 py-3" colspan="6">
                                <form action="{{ route('org.branches.update', $b) }}" method="POST" class="grid grid-cols-6 gap-2 items-center">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $b->name }}" class="px-2 py-1.5 text-sm border border-border rounded outline-none focus:border-secondary col-span-2" required>
                                    <input type="text" name="code" value="{{ $b->code }}" placeholder="Mã" class="px-2 py-1.5 text-sm border border-border rounded outline-none focus:border-secondary">
                                    <select name="company_id" class="px-2 py-1.5 text-sm border border-border rounded outline-none focus:border-secondary">
                                        <option value="">— Công ty —</option>
                                        @foreach($companies as $co)<option value="{{ $co->id }}" @selected($b->company_id == $co->id)>{{ $co->name }}</option>@endforeach
                                    </select>
                                    <input type="text" name="phone" value="{{ $b->phone }}" placeholder="SĐT" class="px-2 py-1.5 text-sm border border-border rounded outline-none focus:border-secondary">
                                    <input type="text" name="address" value="{{ $b->address }}" placeholder="Địa chỉ" class="px-2 py-1.5 text-sm border border-border rounded outline-none focus:border-secondary">
                                    <div class="flex gap-1 justify-end col-span-6">
                                        <button type="button" @click="editing = false" class="px-3 py-1.5 text-xs font-medium text-neutral border border-border rounded hover:bg-surface-alt">Hủy</button>
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-primary rounded hover:bg-primary-light">Lưu</button>
                                    </div>
                                </form>
                            </td></template>
                            <template x-if="!editing">
                                <td class="px-4 py-3 text-neutral">{{ $b->code ?: '—' }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-4 py-3 text-neutral">{{ $b->company?->name ?? '—' }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-4 py-3 text-neutral text-xs">
                                    @if($b->phone)<div>{{ $b->phone }}</div>@endif
                                    @if($b->address)<div class="text-neutral-light">{{ Str::limit($b->address, 40) }}</div>@endif
                                </td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-4 py-3 text-center font-medium text-primary">{{ $b->departments_count }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-4 py-3 text-center font-medium text-primary">{{ $b->members_count }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button @click="editing = true" class="p-1.5 rounded hover:bg-surface-alt"><svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                        <form action="{{ route('org.branches.destroy', $b) }}" method="POST" onsubmit="return confirm('Xóa cơ sở này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded hover:bg-red-50"><svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                        </form>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-12 text-sm text-neutral">Chưa có cơ sở nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="createBranchModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-xl">
            <h2 class="text-lg font-bold text-primary mb-4">Thêm cơ sở</h2>
            <form action="{{ route('org.branches.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Tên *</label><input type="text" name="name" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary" required></div>
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Mã</label><input type="text" name="code" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary"></div>
                </div>
                <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Công ty</label>
                    <select name="company_id" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary">
                        <option value="">— Chọn công ty —</option>
                        @foreach($companies as $co)<option value="{{ $co->id }}">{{ $co->name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">SĐT</label><input type="text" name="phone" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary"></div>
                    <div><label class="block text-xs font-semibold text-neutral uppercase mb-1.5">Địa chỉ</label><input type="text" name="address" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary"></div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('createBranchModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg hover:bg-surface-alt">Hủy</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light">Tạo cơ sở</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
