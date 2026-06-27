<x-layouts.app title="Đổi mật khẩu">
    <div class="max-w-md mx-auto">
        <div class="mb-6">
            <nav class="text-xs text-neutral mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-secondary">Dashboard</a>
                <span class="mx-1">/</span>
                <span class="text-primary">Đổi mật khẩu</span>
            </nav>
            <h1 class="text-xl font-bold text-primary">Đổi mật khẩu</h1>
            <p class="text-xs text-neutral mt-1">Đang đăng nhập: <span class="font-medium text-primary">{{ session('user_name') }}</span></p>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-tertiary">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-danger">
                @foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach
            </div>
        @endif

        <form action="{{ route('account.password.update') }}" method="POST" class="bg-white rounded-xl border border-border p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" required autofocus
                       class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Mật khẩu mới</label>
                <input type="password" name="new_password" required minlength="4"
                       class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
                <p class="text-[10px] text-neutral mt-1">Tối thiểu 4 ký tự.</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral uppercase tracking-wider mb-1.5">Nhập lại mật khẩu mới</label>
                <input type="password" name="new_password_confirmation" required minlength="4"
                       class="w-full px-3 py-2.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition">
            </div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('dashboard') }}" class="flex-1 px-4 py-2.5 text-sm font-medium text-neutral border border-border rounded-lg text-center hover:bg-surface-alt transition">Hủy</a>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-light transition">Lưu mật khẩu</button>
            </div>
        </form>
    </div>
</x-layouts.app>
