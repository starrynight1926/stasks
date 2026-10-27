<x-layouts.app title="Quản trị Doanh nghiệp">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản trị Doanh nghiệp</h1>
        <p class="text-sm text-neutral mt-1">Admin hệ thống: tạo công ty và chỉ định Admin vận hành.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="bg-white rounded-xl border border-border p-5 mb-6">
        <h3 class="text-sm font-semibold text-primary mb-3">+ Tạo công ty mới</h3>
        <form action="{{ route('enterprise.companies.store') }}" method="POST" class="flex gap-2">
            @csrf
            <input type="text" name="name" placeholder="Tên công ty" required class="flex-1 px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary">
            <input type="text" name="code" placeholder="Mã (tùy chọn)" class="w-32 px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Tạo</button>
        </form>
        <p class="text-[11px] text-neutral mt-2">Hệ thống tự tạo 1 chi nhánh "Trụ sở" cho công ty mới.</p>
    </div>

    <div class="bg-white rounded-xl border border-border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-surface-alt text-xs text-neutral uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-2">Công ty</th>
                    <th class="text-left px-4 py-2">Chi nhánh</th>
                    <th class="text-left px-4 py-2">Admin vận hành hiện tại</th>
                    <th class="text-left px-4 py-2">Chỉ định / Thay</th>
                </tr>
            </thead>
            <tbody>
            @forelse($companies as $co)
                @php $firstBranch = $co->branches->first(); $mgr = $firstBranch ? ($managers[$firstBranch->id] ?? null) : null; @endphp
                <tr class="border-t border-border-light">
                    <td class="px-4 py-3 font-medium text-primary">{{ $co->name }}</td>
                    <td class="px-4 py-3 text-neutral">{{ $firstBranch?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-neutral">{{ $mgr?->name ?? '— chưa chỉ định —' }}</td>
                    <td class="px-4 py-3">
                        <form action="{{ route('enterprise.ops.assign', $co) }}" method="POST" class="flex gap-2">
                            @csrf
                            <select name="member_id" class="flex-1 px-2 py-1 text-xs border border-border rounded outline-none focus:border-secondary">
                                <option value="">Chọn user...</option>
                                @foreach($candidates as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-secondary rounded hover:bg-secondary/90">Gán</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-neutral">Chưa có công ty nào.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
