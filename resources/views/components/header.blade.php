<header class="h-16 bg-cream border-b border-border flex items-center px-4 md:px-6 gap-4">

    {{-- Mobile hamburger --}}
    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-primary hover:text-sidebar transition-colors shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    @unless(request()->routeIs('dashboard') || request()->routeIs('kasir') || request()->routeIs('chatbot.*'))
    <div class="flex-1 flex justify-center">
        <div class="relative w-full max-w-xs md:w-80" x-data="{}">
            <span class="absolute inset-y-0 left-4 flex items-center text-muted">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </span>
            <input type="text" @input="window.dispatchEvent(new CustomEvent('live-search', { detail: $event.target.value }))"
                @keydown.enter="if($event.target.value.trim()) window.dispatchEvent(new CustomEvent('search-submit', { detail: $event.target.value.trim() }))"
                placeholder="Search"
                class="w-full pl-10 pr-5 py-2 text-sm bg-cream border border-border rounded-full
                       focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar/50
                       placeholder:text-muted/60 text-primary">
        </div>
    </div>
    @endunless

    {{-- User --}}
    <div class="relative ml-auto" x-data="{ open: false }">
        <button @click="open = !open"
            class="flex items-center gap-2.5 text-sm text-primary hover:opacity-80 transition-opacity">

            {{-- Avatar --}}
            <div class="w-9 h-9 rounded-full bg-primary/80 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-cream" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
                </svg>
            </div>

            {{-- Nama & Role --}}
            <div class="text-left leading-tight">
                <p class="font-semibold text-sm text-primary">{{ Auth::user()->name }}</p>
                <p class="text-xs text-muted">Kasir</p>
            </div>

            <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <div x-show="open" @click.outside="open = false" x-cloak
            class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-border py-1 z-50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </div>

</header>
