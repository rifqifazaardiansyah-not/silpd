<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SILPD') - Admin Desa</title>
    
    <!-- Verdana Health Design System -->
    <link rel="stylesheet" href="{{ asset('css/verdana-health.css') }}">
    
    <!-- Tailwind CSS for utilities -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'vh-navy': '#0F172A',
                        'vh-slate': '#64748B',
                        'vh-sage': '#059669',
                        'vh-bg': '#F8FAFC',
                    },
                    fontFamily: {
                        'headline': ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                        'body': ['DM Sans', 'system-ui', 'sans-serif'],
                        'mono': ['Fira Code', 'Courier New', 'monospace'],
                    },
                }
            }
        }
    </script>
</head>
<body class="vh-bg-page">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 vh-bg-navy text-white fixed h-screen overflow-y-auto">
            <!-- Brand -->
            <div class="px-6 py-6 border-b border-white/10">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-2xl">
                        🌾
                    </div>
                    <div>
                        <span class="vh-h3 text-white block leading-none">SILPD</span>
                        <span class="vh-caption text-white/60 block mt-1">Sistem Lumbung Padi</span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="px-3 py-6 space-y-6">
                <!-- Utama -->
                <div>
                    <p class="px-3 text-white/50 vh-caption uppercase tracking-wider mb-2">Utama</p>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                              {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                </div>

                <!-- Data Master -->
                <div>
                    <p class="px-3 text-white/50 vh-caption uppercase tracking-wider mb-2">Data Master</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.petani.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.petani.*') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Petani
                        </a>
                        <a href="{{ route('admin.kelompok.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.kelompok.*') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Kelompok Tani
                        </a>
                        <a href="{{ route('admin.jenis-gabah.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.jenis-gabah.*') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Jenis Gabah
                        </a>
                        <a href="{{ route('admin.lumbung.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.lumbung.*') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Lumbung
                        </a>
                        <a href="{{ route('admin.pengelola.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.pengelola.*') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Pengelola
                        </a>
                    </div>
                </div>

                <!-- Operasional -->
                <div>
                    <p class="px-3 text-white/50 vh-caption uppercase tracking-wider mb-2">Operasional</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.panen.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.panen.*') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Input Panen
                        </a>
                        <a href="{{ route('admin.instruksi.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.instruksi.*') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Instruksi Simpan
                        </a>
                        <a href="{{ route('admin.permintaan.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.permintaan.*') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Permintaan Ambil
                        </a>
                        <a href="{{ route('admin.akun.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.akun.*') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Manajemen Akun
                        </a>
                    </div>
                </div>

                <!-- Laporan -->
                <div>
                    <p class="px-3 text-white/50 vh-caption uppercase tracking-wider mb-2">LAPORAN</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.laporan.stok') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.laporan.stok') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Stok Gabah
                        </a>
                        <a href="{{ route('admin.laporan.panen') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.laporan.panen') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Laporan Panen
                        </a>
                        <a href="{{ route('admin.laporan.pengambilan') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.laporan.pengambilan') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Laporan Pengambilan
                        </a>
                        <a href="{{ route('admin.laporan.rekap-petani') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm transition-all
                                  {{ request()->routeIs('admin.laporan.rekap-petani') ? 'bg-white/15 text-white font-medium shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Rekap Petani
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Logout Button -->
            <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-white/10 bg-vh-navy">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg vh-body-sm text-white/70 hover:bg-white/10 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col">
            <!-- Topbar -->
            <div class="vh-bg-surface border-b" style="border-color: var(--vh-border);">
                <div class="px-6 py-4 flex justify-between items-center">
                    <div class="vh-body-sm" style="color: var(--vh-slate);">
                        @yield('breadcrumb')
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="vh-body-sm font-medium" style="color: var(--vh-navy);">{{ session('nama') }}</span>
                        <span class="vh-chip vh-chip-filter-active">
                            Admin
                        </span>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-auto">
                <div class="p-8">
                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div class="vh-alert vh-alert-success mb-6">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--vh-success-text);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="vh-body-sm font-medium" style="color: var(--vh-success-text);">Berhasil</p>
                            <p class="vh-body-sm" style="color: var(--vh-success-text);">{{ session('success') }}</p>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="vh-alert vh-alert-error mb-6">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--vh-error-text);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="vh-body-sm font-medium" style="color: var(--vh-error-text);">Error</p>
                            <p class="vh-body-sm" style="color: var(--vh-error-text);">{{ session('error') }}</p>
                        </div>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div class="vh-alert vh-alert-warning mb-6">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--vh-warning-text);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="vh-body-sm font-medium" style="color: var(--vh-warning-text);">Peringatan</p>
                            <p class="vh-body-sm" style="color: var(--vh-warning-text);">{{ session('warning') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="vh-alert vh-alert-error mb-6">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--vh-error-text);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="vh-body-sm font-medium" style="color: var(--vh-error-text);">Terdapat kesalahan:</p>
                            <ul class="mt-2 space-y-1">
                                @foreach($errors->all() as $error)
                                <li class="vh-body-sm" style="color: var(--vh-error-text);">• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    @yield('scripts')
</body>
</html>
