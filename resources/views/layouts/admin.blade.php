<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem Persuratan') }} — @yield('title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-100 font-sans text-neutral-900 antialiased">

    @php
        // Dosen & Mahasiswa pakai navbar; sisanya (staff) pakai sidebar
        $isNavbarMode = auth()->user()->hasAnyRole(['Dosen', 'Mahasiswa']);
    @endphp

    @if ($isNavbarMode)
        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- MODE NAVBAR (Dosen & Mahasiswa)                        --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div x-data="{ mobileMenuOpen: false }" class="min-h-screen flex flex-col">

            {{-- NAVBAR --}}
            <nav class="sticky top-0 z-40 border-b border-neutral-200 bg-white shadow-sm">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">

                        {{-- KIRI: Logo + Hamburger --}}
                        <div class="flex items-center gap-3">
                            <button
                                @click="mobileMenuOpen = !mobileMenuOpen"
                                class="inline-flex items-center justify-center rounded-md p-2 text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 lg:hidden"
                                aria-label="Toggle menu"
                            >
                                <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-base text-white font-bold">
                                    FT
                                </div>
                                <div class="hidden sm:block">
                                    <p class="text-sm font-bold text-neutral-900 leading-tight">Persuratan</p>
                                    <p class="text-xs text-neutral-500 leading-tight">Fakultas Teknik</p>
                                </div>
                            </a>
                        </div>

                        {{-- TENGAH: Menu Desktop --}}
                        <div class="hidden lg:flex lg:items-center lg:gap-1">
                            <a
                                href="{{ route('dashboard') }}"
                                class="rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-base text-white' : 'text-neutral-700 hover:bg-neutral-100' }}"
                            >
                                Dashboard
                            </a>

                            <a
                                href="{{ route('pengajuan.index') }}"
                                class="rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('pengajuan.*') ? 'bg-primary-base text-white' : 'text-neutral-700 hover:bg-neutral-100' }}"
                            >
                                Pengajuan Surat
                            </a>
                        </div>

                        {{-- KANAN: Notifikasi + Profil --}}
                        <div class="flex items-center gap-2">
                            <a
                                href="{{ route('notifications.index') }}"
                                class="relative inline-flex items-center justify-center rounded-md p-2 text-neutral-600 hover:bg-neutral-100"
                                title="Notifikasi"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @php $unread = auth()->user()->unreadNotifications->count(); @endphp
                                @if ($unread > 0)
                                    <span class="absolute top-1 right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger-base px-1 text-[10px] font-bold text-white">
                                        {{ $unread > 99 ? '99+' : $unread }}
                                    </span>
                                @endif
                            </a>

                            <div class="relative" x-data="{ open: false }">
                                <button
                                    @click="open = !open"
                                    @click.outside="open = false"
                                    class="flex items-center gap-2 rounded-md px-2 py-1.5 hover:bg-neutral-100"
                                >
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-light text-sm font-semibold text-primary-base">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <div class="hidden sm:block text-left">
                                        <p class="text-xs font-semibold text-neutral-900 leading-tight">{{ auth()->user()->name }}</p>
                                        <p class="text-[10px] text-neutral-500 leading-tight">{{ auth()->user()->roles->first()?->name ?? 'User' }}</p>
                                    </div>
                                </button>

                                <div
                                    x-show="open"
                                    x-transition
                                    x-cloak
                                    class="absolute right-0 mt-2 w-56 rounded-md border border-neutral-200 bg-white py-1 shadow-lg"
                                >
                                    <div class="border-b border-neutral-100 px-4 py-2">
                                        <p class="text-xs font-semibold text-neutral-900">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-neutral-500">{{ auth()->user()->email }}</p>
                                    </div>
                                    <a href="{{ route('signature.create') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                                        Tanda Tangan Digital
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-danger-base hover:bg-danger-background">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Menu Drawer --}}
                    <div
                        x-show="mobileMenuOpen"
                        x-transition
                        x-cloak
                        class="border-t border-neutral-200 py-2 lg:hidden"
                    >
                        <a
                            href="{{ route('dashboard') }}"
                            class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-base text-white' : 'text-neutral-700 hover:bg-neutral-100' }}"
                        >
                            Dashboard
                        </a>
                        <a
                            href="{{ route('pengajuan.index') }}"
                            class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('pengajuan.*') ? 'bg-primary-base text-white' : 'text-neutral-700 hover:bg-neutral-100' }}"
                        >
                            Pengajuan Surat
                        </a>
                    </div>
                </div>
            </nav>

            {{-- MAIN CONTENT (Navbar Mode) --}}
            <main class="flex-1">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="mb-4 rounded-md border border-success-border bg-success-background px-4 py-3 text-sm text-success-text">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-md border border-danger-border bg-danger-background px-4 py-3 text-sm text-danger-text">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            {{-- FOOTER --}}
            <footer class="border-t border-neutral-200 bg-white py-4">
                <div class="mx-auto max-w-7xl px-4 text-center text-xs text-neutral-500 sm:px-6 lg:px-8">
                    &copy; {{ date('Y') }} Fakultas Teknik - Universitas Tiga Serangkai
                </div>
            </footer>
        </div>

    @else
        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- MODE SIDEBAR (Admin Fakultas, BAAK, Penandatangan, SA) --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div class="flex min-h-screen" x-data="{ sidebarOpen: true }">

            {{-- SIDEBAR --}}
            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-30 w-64 transform bg-primary-pressed text-white transition-transform duration-200 lg:translate-x-0"
            >
                <div class="flex h-16 items-center gap-2 border-b border-white/10 px-4">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 font-bold">FT</div>
                    <div>
                        <p class="text-sm font-bold leading-tight">Persuratan</p>
                        <p class="text-xs text-white/60 leading-tight">Fakultas Teknik</p>
                    </div>
                </div>

                <nav class="mt-4 space-y-1 px-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-base' : 'text-white/80 hover:bg-white/10' }}">
                        Dashboard
                    </a>

                    @role('Admin Fakultas|Dosen|Mahasiswa|Super Admin')
                        <a href="{{ route('pengajuan.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('pengajuan.*') ? 'bg-primary-base' : 'text-white/80 hover:bg-white/10' }}">
                            Pengajuan Surat
                        </a>
                    @endrole

                    @role('BAAK|Super Admin')
                        <a href="{{ route('baak.antrian') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('baak.*') ? 'bg-primary-base' : 'text-white/80 hover:bg-white/10' }}">
                            Nomor & Verifikasi
                        </a>
                    @endrole

                    @role('Penandatangan|Super Admin')
                        <a href="{{ route('ttd.antrian') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('ttd.*') ? 'bg-primary-base' : 'text-white/80 hover:bg-white/10' }}">
                            Tanda Tangan
                        </a>
                    @endrole

                    @role('Super Admin|Admin Fakultas|BAAK')
                        <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('laporan.*') ? 'bg-primary-base' : 'text-white/80 hover:bg-white/10' }}">
                            Laporan
                        </a>
                    @endrole

                    @role('Super Admin')
                        <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-white/50">Master Data</p>
                        <a href="{{ route('jenis-surat.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('jenis-surat.*') ? 'bg-primary-base' : 'text-white/80 hover:bg-white/10' }}">
                            Jenis Surat
                        </a>
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('users.*') ? 'bg-primary-base' : 'text-white/80 hover:bg-white/10' }}">
                            Manajemen User
                        </a>
                    @endrole
                </nav>
            </aside>

            {{-- MAIN AREA --}}
            <div class="flex flex-1 flex-col lg:pl-64">
                {{-- TOP BAR --}}
                <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-neutral-200 bg-white px-4 sm:px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden rounded-md p-2 text-neutral-600 hover:bg-neutral-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="ml-auto flex items-center gap-2">
                        <a href="{{ route('notifications.index') }}" class="relative rounded-md p-2 text-neutral-600 hover:bg-neutral-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @php $unread = auth()->user()->unreadNotifications->count(); @endphp
                            @if ($unread > 0)
                                <span class="absolute top-1 right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger-base px-1 text-[10px] font-bold text-white">
                                    {{ $unread > 99 ? '99+' : $unread }}
                                </span>
                            @endif
                        </a>

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 rounded-md px-2 py-1.5 hover:bg-neutral-100">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-light text-sm font-semibold text-primary-base">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden text-sm font-medium text-neutral-700 md:inline">{{ auth()->user()->name }}</span>
                            </button>
                            <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-56 rounded-md border border-neutral-200 bg-white py-1 shadow-lg">
                                <div class="border-b border-neutral-100 px-4 py-2">
                                    <p class="text-xs font-semibold text-neutral-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-neutral-500">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('signature.create') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Tanda Tangan Digital</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-danger-base hover:bg-danger-background">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- CONTENT --}}
                <main class="flex-1 p-4 sm:p-6">
                    @if (session('success'))
                        <div class="mb-4 rounded-md border border-success-border bg-success-background px-4 py-3 text-sm text-success-text">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-md border border-danger-border bg-danger-background px-4 py-3 text-sm text-danger-text">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    @endif

</body>
</html>