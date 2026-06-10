@props(['route', 'label'])

@php
    $active = request()->routeIs($route);
@endphp

<a href="{{ route($route) }}" @click="sidebarOpen = false"
    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
        {{ $active
            ? 'bg-sidebar-active text-white'
            : 'text-cream/90 hover:bg-sidebar-hover hover:text-white' }}">
    {{ $slot }}
    <span>{{ $label }}</span>
</a>
