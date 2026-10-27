<x-layouts.app title="Quản trị công ty {{ $company->name }}">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary">{{ $company->name }}</h1>
        <p class="text-sm text-neutral mt-1">Admin vận hành: quản lý phòng ban và nhân sự của công ty.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Departments --}}
        <div class="bg-white rounded-xl border border-border p-5">
            <h3 class="text-sm font-semibold text-primary mb-3">Phòng ban</h3>

            <form action="{{ route('enterprise.departments.store') }}" method="POST" class="flex gap-2 mb-4">
                @csrf
                <input type="text" name="name" placeholder="Tên phòng ban" required class="flex-1 px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary">
                <select name="branch_id" required class="px-3 py-2 text-sm border border-border rounded-lg outline-none focus:border-secondary">
                    @foreach($company->branches as $br)
                        <option value="{{ $br->id }}">{{ $br->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">+</button>
            </form>

            <ul class="divide-y divide-border-light">
                @forelse($departments as $d)
                    <li class="py-2 flex justify-between text-sm">
                        <span class="text-primary">{{ $d->name }}</span>
                        <span class="text-xs text-neutral">{{ $members->where('department_id', $d->id)->count() }} nhân sự</span>
                    </li>
                @empty
                    <li class="py-4 text-center text-sm text-neutral">Chưa có phòng ban.</li>
                @endforelse
            </ul>
        </div>

        {{-- Members --}}
        <div class="bg-white rounded-xl border border-border p-5">
            <h3 class="text-sm font-semibold text-primary mb-3">Nhân sự ({{ $members->count() }})</h3>

            <div class="space-y-2 max-h-[500px] overflow-y-auto">
                @foreach($members as $m)
                    <form action="{{ route('enterprise.members.assign') }}" method="POST" class="flex items-center gap-2 p-2 bg-surface-alt rounded">
                        @csrf
                        <input type="hidden" name="member_id" value="{{ $m->id }}">
                        <span class="flex-1 text-sm text-primary truncate" title="{{ $m->name }}">{{ $m->name }}</span>
                        <select name="department_id" class="px-2 py-1 text-xs border border-border rounded outline-none focus:border-secondary bg-white">
                            <option value="">— Không phòng —</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ $m->department_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <select name="level" class="px-2 py-1 text-xs border border-border rounded outline-none focus:border-secondary bg-white">
                            @foreach(['manager' => 'Manager', 'lead' => 'Lead', 'member' => 'Member'] as $k => $v)
                                <option value="{{ $k }}" {{ $m->level == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-2 py-1 text-xs font-medium text-white bg-secondary rounded hover:bg-secondary/90">Lưu</button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
