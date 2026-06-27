@php $current = $current ?? request()->route()->getName(); @endphp
<div class="inline-flex items-center gap-0.5 bg-surface-alt rounded-lg p-0.5 border border-border">
    @php
        $views = [
            'tasks.list'     => ['label' => 'List',     'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
            'tasks.board'    => ['label' => 'Board',    'icon' => 'M9 3v18m6-18v18M3 8h18M3 16h18'],
            'tasks.calendar' => ['label' => 'Calendar', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            'tasks.timeline' => ['label' => 'Gantt',    'icon' => 'M3 6h10M3 12h6M3 18h14M16 4l4 4-4 4M12 14l4 4-4 4'],
        ];
    @endphp
    @foreach($views as $route => $info)
        @php $active = $current === $route; @endphp
        <a href="{{ route($route) }}"
           class="inline-flex items-center justify-center w-8 h-7 rounded-md transition {{ $active ? 'bg-white text-primary shadow-sm' : 'text-neutral hover:text-primary hover:bg-white/60' }}"
           title="{{ $info['label'] }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"/></svg>
        </a>
    @endforeach
</div>
