<x-layouts.app title="Edit Member">
    <div class="mb-6">
        <nav class="text-xs text-neutral mb-2">
            <a href="{{ route('teams') }}" class="hover:text-secondary">Teams</a>
            <span class="mx-1">/</span>
            <span class="text-primary">Edit {{ $teamMember->name }}</span>
        </nav>
        <h1 class="text-2xl font-bold text-primary">Edit Member</h1>
    </div>

    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div class="max-w-2xl">
        <form action="{{ route('teams.update', $teamMember) }}" method="POST" class="bg-white rounded-xl border border-border p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ $teamMember->name }}" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ $teamMember->email }}" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Role</label>
                    <input type="text" name="role" value="{{ $teamMember->role }}" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Position</label>
                    <input type="text" name="position" value="{{ $teamMember->position }}" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Department</label>
                    <select name="department_id" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition" required>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $teamMember->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ $teamMember->phone }}" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Cơ sở</label>
                    <select name="branch_id" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <option value="">— Không gán —</option>
                        @foreach($branches as $br)<option value="{{ $br->id }}" @selected($teamMember->branch_id == $br->id)>{{ $br->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Vai trò hệ thống</label>
                    <select name="role_id" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                        <option value="">— Không gán —</option>
                        @foreach($roles as $r)<option value="{{ $r->id }}" @selected($teamMember->role_id == $r->id)>{{ $r->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Username</label>
                    <input type="text" name="username" value="{{ $teamMember->username }}" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Mật khẩu (để trống nếu không đổi)</label>
                    <input type="text" name="password" placeholder="••••••" class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('teams') }}" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg text-center hover:bg-surface-alt transition">Cancel</a>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Save Changes</button>
            </div>
        </form>
    </div>
</x-layouts.app>
