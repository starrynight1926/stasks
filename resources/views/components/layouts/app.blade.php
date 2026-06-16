<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ProjectFlow - {{ $title ?? 'Quản lý dự án' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center text-white text-xs font-semibold ml-1">
                MC
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
                    <a href="#" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition text-neutral-light hover:bg-surface-alt hover:text-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Calendar
                    </a>
                </nav>

                <div class="mt-6 pt-4 border-t border-border">
                    <h3 class="text-xs font-semibold text-neutral uppercase tracking-wider mb-2 px-3">Management</h3>
                    <nav class="space-y-0.5">
                        <a href="{{ route('departments') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('departments') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Departments
                        </a>
                        <a href="{{ route('files') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('files') ? 'bg-blue-50 text-secondary font-medium' : 'text-neutral-light hover:bg-surface-alt hover:text-primary' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Files
                        </a>
                    </nav>
                </div>

                <div class="mt-6 pt-4 border-t border-border">
                    <a href="#" class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg text-neutral-light hover:bg-surface-alt hover:text-primary transition">
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
        });
    </script>
</body>
</html>
