<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ProjectFlow - {{ $title ?? 'Quản lý dự án' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
            --color-primary: #0F172A;
            --color-primary-light: #1E293B;
            --color-secondary: #3B82F6;
            --color-secondary-light: #60A5FA;
            --color-secondary-dark: #2563EB;
            --color-tertiary: #10B981;
            --color-tertiary-light: #34D399;
            --color-neutral: #64748B;
            --color-neutral-light: #94A3B8;
            --color-surface: #F8FAFC;
            --color-surface-alt: #F1F5F9;
            --color-border: #E2E8F0;
            --color-border-light: #F1F5F9;
            --color-danger: #EF4444;
            --color-warning: #F59E0B;
            --color-info: #3B82F6;
            --color-success: #10B981;
        }
        @layer base {
            body {
                font-family: var(--font-sans);
                color: var(--color-primary);
                background: var(--color-surface);
            }
            button:not(:disabled),
            [role="button"]:not(:disabled),
            label:has(input[type="checkbox"]:not(:disabled)),
            label:has(input[type="radio"]:not(:disabled)),
            label:has(input[type="file"]:not(:disabled)),
            summary {
                cursor: pointer;
            }
            button:disabled {
                cursor: not-allowed;
            }
            [x-cloak] { display: none !important; }
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-surface" x-data="{ sidebarOpen: true }">
    {{-- Top Navigation --}}
    <header class="fixed top-0 left-0 right-0 z-50 h-14 bg-white border-b border-border flex items-center px-4">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="p-1.5 rounded-lg hover:bg-surface-alt transition">
                <svg class="w-5 h-5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <a href="/" class="flex items-center gap-2">
                <div class="w-7 h-7 bg-secondary rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>
                </div>
                <span class="text-lg font-bold text-primary">ProjectFlow</span>
            </a>
        </div>

        <nav class="hidden md:flex items-center gap-1 ml-8">
            <a href="{{ route('dashboard') }}" class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('dashboard') ? 'text-secondary bg-blue-50' : 'text-neutral hover:text-primary hover:bg-surface-alt' }}">Dashboard</a>
            <a href="{{ route('tasks.board') }}" class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('tasks.*') ? 'text-secondary bg-blue-50' : 'text-neutral hover:text-primary hover:bg-surface-alt' }}">Tasks</a>
            <a href="{{ route('teams') }}" class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('teams') ? 'text-secondary bg-blue-50' : 'text-neutral hover:text-primary hover:bg-surface-alt' }}">Teams</a>
        </nav>

        <div class="ml-auto flex items-center gap-2">
            <button class="p-2 rounded-lg hover:bg-surface-alt transition relative">
                <svg class="w-5 h-5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full"></span>
            </button>
            <button class="p-2 rounded-lg hover:bg-surface-alt transition">
                <svg class="w-5 h-5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
            <div class="relative" x-data="{ userMenu: false }">
                <button @click="userMenu = !userMenu" class="flex items-center gap-2 p-1 rounded-lg hover:bg-surface-alt transition">
                    <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center text-white text-xs font-semibold">
                        {{ substr(session('user_name', 'U'), 0, 2) }}
                    </div>
                    <span class="text-sm font-medium text-primary hidden sm:block">{{ session('user_name', 'User') }}</span>
                </button>
                <div x-show="userMenu" @click.away="userMenu = false" x-cloak class="absolute right-0 top-12 w-56 bg-white rounded-xl border border-border shadow-lg py-1 z-50">
                    <div class="px-3 py-2 border-b border-border-light">
                        <p class="text-sm font-semibold text-primary">{{ session('user_name', 'User') }}</p>
                        <p class="text-[10px] text-neutral">
                            @if(session('is_admin'))
                                Administrator
                            @else
                                {{ session('role_name', 'Member') }}
                            @endif
                        </p>
                    </div>
                    @if(session('member_id'))
                        <a href="{{ route('account.password') }}" class="w-full text-left px-3 py-2 text-xs text-primary hover:bg-surface-alt transition flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Đổi mật khẩu
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-xs text-danger hover:bg-red-50 transition flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="flex pt-14">
        {{-- Left Sidebar --}}
        <aside x-show="sidebarOpen" x-transition:enter="transition-transform duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" class="fixed left-0 top-14 bottom-0 w-56 bg-white border-r border-border z-40 overflow-y-auto">
            <div class="p-4">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-secondary rounded-lg flex items-center justify-center text-white text-xs font-bold">CE</div>
                    <div>
                        <div class="text-sm font-semibold text-primary">Core Engineering</div>
                        <div class="text-xs text-neutral">Product Team</div>
                    </div>
                </div>

                <nav class="space-y-0.5">
                    <a href="{{ route('tasks.board') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('tasks.board') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
                        Board
                    </a>
                    <a href="{{ route('tasks.timeline') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('tasks.timeline') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Timeline
                    </a>
                    <a href="{{ route('tasks.list') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('tasks.list') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        List
                    </a>
                    <a href="{{ route('tasks.calendar') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('tasks.calendar') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Calendar
                    </a>
                </nav>

                <div class="mt-6 pt-4 border-t border-border" x-data="{ orgOpen: {{ request()->routeIs('org.*','departments','teams') ? 'true' : 'true' }} }">
                    <button @click="orgOpen = !orgOpen" class="w-full flex items-center justify-between px-3 mb-2">
                        <h3 class="text-xs font-semibold text-neutral uppercase tracking-wider">Organizations</h3>
                        <svg class="w-3.5 h-3.5 text-neutral transition-transform" :class="orgOpen ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <nav x-show="orgOpen" x-cloak class="space-y-0.5">
                        <a href="{{ route('org.companies.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('org.companies.*') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Công ty
                        </a>
                        <a href="{{ route('org.branches.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('org.branches.*') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11M8 14v3m4-3v3m4-3v3"/></svg>
                            Cơ sở
                        </a>
                        <a href="{{ route('departments') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('departments') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Phòng ban
                        </a>
                        <a href="{{ route('teams') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('teams') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Nhân sự
                        </a>
                        <a href="{{ route('org.roles.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('org.roles.*') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Vai trò & Quyền
                        </a>
                        <a href="{{ route('org.teams-group.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('org.teams-group.*') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Đội nhóm
                        </a>
                    </nav>
                </div>

                <div class="mt-6 pt-4 border-t border-border">
                    <h3 class="text-xs font-semibold text-neutral uppercase tracking-wider mb-2 px-3">Resources</h3>
                    <nav class="space-y-0.5">
                        <a href="{{ route('files') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('files') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Files
                        </a>
                        <a href="{{ route('tags') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('tags') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                            Tags
                        </a>
                    </nav>
                </div>

                <div class="mt-6 pt-4 border-t border-border">
                    <a href="{{ route('tasks.archive') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('tasks.archive') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        Archive
                    </a>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 transition-all duration-200" :class="sidebarOpen ? 'ml-56' : 'ml-0'">
            <div class="p-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', { open: false, component: null, data: {} });
            Alpine.store('ctx', {
                open: false, x: 0, y: 0,
                taskUrl: null, editUrl: null, deleteUrl: null, archiveUrl: null, redirectUrl: null,
                show(e, opts) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.taskUrl = opts.taskUrl || null;
                    this.editUrl = opts.editUrl || null;
                    this.deleteUrl = opts.deleteUrl || null;
                    this.archiveUrl = opts.archiveUrl || null;
                    this.redirectUrl = opts.redirectUrl || null;
                    const mw = 180, mh = 180;
                    this.x = Math.min(e.clientX, window.innerWidth - mw - 8);
                    this.y = Math.min(e.clientY, window.innerHeight - mh - 8);
                    this.open = true;
                },
                close() { this.open = false; },
                doDelete() {
                    this.open = false;
                    deleteResource(this.deleteUrl, this.redirectUrl);
                },
                doArchive() {
                    this.open = false;
                    archiveResource(this.archiveUrl, this.redirectUrl);
                }
            });
        });

        function exportFile(url) {
            fetch(url).then(r => {
                const name = r.headers.get('content-disposition')?.match(/filename=(.+)/)?.[1] || 'export.xlsx';
                return r.blob().then(b => { const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = name; a.click(); URL.revokeObjectURL(a.href); });
            });
        }

        function archiveResource(url, redirectUrl) {
            fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            }).then(() => { window.location.href = redirectUrl || window.location.href; });
        }

        function bulkAction(url, ids, redirectUrl) {
            if (!ids.length) return;
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids }),
            }).then(() => { window.location.href = redirectUrl || window.location.href; });
        }

        function deleteResource(url, redirectUrl) {
            if (!confirm('Bạn có chắc muốn xóa?')) return;
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            }).then(() => {
                window.location.href = redirectUrl || '/tasks/board';
            });
        }

        document.addEventListener('click', () => {
            if (Alpine.store('ctx')) Alpine.store('ctx').close();
        });
    </script>

    {{-- Global Context Menu --}}
    <div x-data x-show="$store.ctx.open" x-cloak
         :style="`top: ${$store.ctx.y}px; left: ${$store.ctx.x}px;`"
         class="fixed z-[100] bg-white rounded-xl border border-border shadow-xl py-1.5 min-w-[170px]"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <template x-if="$store.ctx.taskUrl">
            <a :href="$store.ctx.taskUrl" class="flex items-center gap-2.5 px-3 py-2 text-sm text-primary hover:bg-surface-alt transition rounded-lg mx-1">
                <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Xem chi tiết
            </a>
        </template>
        <template x-if="$store.ctx.editUrl">
            <a :href="$store.ctx.editUrl" class="flex items-center gap-2.5 px-3 py-2 text-sm text-primary hover:bg-surface-alt transition rounded-lg mx-1">
                <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Chỉnh sửa
            </a>
        </template>
        <template x-if="$store.ctx.archiveUrl">
            <button @click="$store.ctx.doArchive()" class="flex items-center gap-2.5 px-3 py-2 text-sm text-primary hover:bg-surface-alt transition w-full text-left rounded-lg mx-1" style="width: calc(100% - 8px)">
                <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Lưu trữ
            </button>
        </template>
        <template x-if="$store.ctx.deleteUrl">
            <div>
                <div class="border-t border-border my-1 mx-2"></div>
                <button @click="$store.ctx.doDelete()" class="flex items-center gap-2.5 px-3 py-2 text-sm text-danger hover:bg-red-50 transition w-full text-left rounded-lg mx-1" style="width: calc(100% - 8px)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Xóa
                </button>
            </div>
        </template>
    </div>
</body>
</html>
