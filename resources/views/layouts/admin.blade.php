<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'SILPD') - Admin Desa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-feature-settings: "liga" 1, "cv02" 1, "cv03" 1, "cv04" 1;
        }

        /* ── Sidebar transition ── */
        #sidebar {
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                        transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: width;
        }
        #sidebar.collapsed { width: 72px; }

        /* Hide text labels when collapsed */
        #sidebar.collapsed .nav-label,
        #sidebar.collapsed .nav-section-label,
        #sidebar.collapsed .brand-text,
        #sidebar.collapsed .user-text { display: none; }

        /* Center nav items when collapsed */
        #sidebar.collapsed .nav-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }
        #sidebar.collapsed .nav-item .nav-icon { margin-right: 0; }

        /* Brand area when collapsed */
        #sidebar.collapsed .brand-area {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        /* Tooltip for collapsed state */
        #sidebar.collapsed .nav-item {
            position: relative;
        }
        #sidebar.collapsed .nav-item:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: #1e293b;
            color: white;
            font-size: 12px;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 6px;
            white-space: nowrap;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            pointer-events: none;
        }
        #sidebar.collapsed .nav-item:hover::before {
            content: '';
            position: absolute;
            left: calc(100% + 6px);
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #1e293b;
            z-index: 101;
        }

        /* Main content transition */
        #main-content {
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #main-content.sidebar-collapsed { margin-left: 72px; }

        /* Toggle button */
        #sidebar-toggle {
            transition: transform 0.25s ease;
        }
        #sidebar-toggle.rotated { transform: rotate(180deg); }

        /* Mobile overlay */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
        }
        #sidebar-overlay.active { display: block; }

        /* Active nav highlight */
        .nav-item.active {
            background: linear-gradient(135deg, rgba(99,102,241,0.35) 0%, rgba(79,70,229,0.2) 100%);
            color: white !important;
            border-left: 3px solid #818cf8;
        }
        .nav-item:not(.active):hover {
            background: rgba(255,255,255,0.07);
        }

        /* Scrollbar — Sidebar */
        #sidebar::-webkit-scrollbar { width: 4px; }
        #sidebar::-webkit-scrollbar-track { background: transparent; }
        #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.18); border-radius: 2px; }
        #sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.32); }

        /* Scrollbar — Main content (dark-toned, matches sidebar aesthetic) */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; border: 2px solid #1e293b; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        ::-webkit-scrollbar-corner { background: #1e293b; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); width: 256px !important; }
            #sidebar.mobile-open { transform: translateX(0); }
            #main-content { margin-left: 0 !important; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Overlay -->
        <div id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 text-white fixed h-screen overflow-y-auto flex flex-col z-50" style="background: linear-gradient(180deg, #0f172a 0%, #111827 100%);">

            <!-- Brand -->
            <div class="brand-area flex items-center gap-3 px-5 py-5 flex-shrink-0" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center text-lg" style="background: linear-gradient(135deg, #4e46e53f, #6365f164);">
                    🌾
                </div>
                <div class="brand-text min-w-0">
                    <p class="text-white font-semibold text-sm leading-tight tracking-tight">SILPD</p>
                    <p class="text-[11px] mt-0.5" style="color: rgba(255,255,255,0.5);">Admin Desa</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-5 overflow-y-auto">

                <!-- Utama -->
                <div>
                    <p class="nav-section-label px-3 text-[10px] font-semibold uppercase tracking-widest mb-1.5" style="color: rgba(255,255,255,0.35);">Utama</p>
                    <a href="{{ route('admin.dashboard') }}"
                       data-tooltip="Dashboard"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                              {{ request()->routeIs('admin.dashboard') ? 'active font-medium' : 'text-white/60' }}">
                        <svg class="nav-icon w-4.5 h-4.5 flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </div>

                <!-- Data Master -->
                <div>
                    <p class="nav-section-label px-3 text-[10px] font-semibold uppercase tracking-widest mb-1.5" style="color: rgba(255,255,255,0.35);">Data Master</p>
                    <div class="space-y-0.5">
                        <a href="{{ route('admin.petani.index') }}"
                           data-tooltip="Petani"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.petani.*') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            <span class="nav-label">Petani</span>
                        </a>
                        <a href="{{ route('admin.kelompok.index') }}"
                           data-tooltip="Kelompok Tani"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.kelompok.*') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                            <span class="nav-label">Kelompok Tani</span>
                        </a>
                        <a href="{{ route('admin.jenis-gabah.index') }}"
                           data-tooltip="Jenis Gabah"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.jenis-gabah.*') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                            <span class="nav-label">Jenis Gabah</span>
                        </a>
                        <a href="{{ route('admin.lumbung.index') }}"
                           data-tooltip="Lumbung"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.lumbung.*') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                            </svg>
                            <span class="nav-label">Lumbung</span>
                        </a>
                        <a href="{{ route('admin.pengelola.index') }}"
                           data-tooltip="Pengelola"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.pengelola.*') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                            </svg>
                            <span class="nav-label">Pengelola</span>
                        </a>
                    </div>
                </div>

                <!-- Operasional -->
                <div>
                    <p class="nav-section-label px-3 text-[10px] font-semibold uppercase tracking-widest mb-1.5" style="color: rgba(255,255,255,0.35);">Operasional</p>
                    <div class="space-y-0.5">
                        <a href="{{ route('admin.panen.index') }}"
                           data-tooltip="Input Panen"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.panen.*') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span class="nav-label">Input Panen</span>
                        </a>
                        <a href="{{ route('admin.instruksi.index') }}"
                           data-tooltip="Instruksi Simpan"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.instruksi.*') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>
                            <span class="nav-label">Instruksi Simpan</span>
                        </a>
                        <a href="{{ route('admin.permintaan.index') }}"
                           data-tooltip="Permintaan Ambil"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.permintaan.*') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span class="nav-label">Permintaan Ambil</span>
                        </a>
                        <a href="{{ route('admin.akun.index') }}"
                           data-tooltip="Manajemen Akun"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.akun.*') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z" />
                            </svg>
                            <span class="nav-label">Manajemen Akun</span>
                        </a>
                    </div>
                </div>

                <!-- Laporan -->
                <div>
                    <p class="nav-section-label px-3 text-[10px] font-semibold uppercase tracking-widest mb-1.5" style="color: rgba(255,255,255,0.35);">Laporan</p>
                    <div class="space-y-0.5">
                        <a href="{{ route('admin.laporan.stok') }}"
                           data-tooltip="Stok Gabah"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.laporan.stok') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                            </svg>
                            <span class="nav-label">Stok Gabah</span>
                        </a>
                        <a href="{{ route('admin.laporan.panen') }}"
                           data-tooltip="Laporan Panen"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.laporan.panen') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                            <span class="nav-label">Laporan Panen</span>
                        </a>
                        <a href="{{ route('admin.laporan.pengambilan') }}"
                           data-tooltip="Laporan Pengambilan"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.laporan.pengambilan') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                            </svg>
                            <span class="nav-label">Laporan Pengambilan</span>
                        </a>
                        <a href="{{ route('admin.laporan.rekap-petani') }}"
                           data-tooltip="Rekap Petani"
                           class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 cursor-pointer
                                  {{ request()->routeIs('admin.laporan.rekap-petani') ? 'active font-medium' : 'text-white/60' }}">
                            <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>
                            <span class="nav-label">Rekap Petani</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User + Logout -->
            <div class="flex-shrink-0 px-3 py-4" style="border-top: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1" style="background: rgba(255,255,255,0.05);">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        {{ strtoupper(substr(session('nama', 'A'), 0, 1)) }}
                    </div>
                    <div class="user-text min-w-0 flex-1">
                        <p class="text-white text-xs font-medium truncate">{{ session('nama', 'Admin') }}</p>
                        <p class="text-[11px]" style="color: rgba(255,255,255,0.4);">Administrator</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            data-tooltip="Keluar"
                            class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-white/50 hover:text-white transition-all duration-150 cursor-pointer">
                        <svg class="nav-icon flex-shrink-0" style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span class="nav-label">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="ml-64 flex-1 flex flex-col min-h-screen">
            <!-- Topbar -->
            <div class="bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center sticky top-0 z-30" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <div class="flex items-center gap-3">
                    <!-- Toggle Button -->
                    <button id="sidebar-toggle"
                            title="Toggle Sidebar"
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors focus:outline-none">
                        <svg id="toggle-icon-open" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                        </svg>
                    </button>
                    <!-- Breadcrumb -->
                    <div class="text-sm">
                        @yield('breadcrumb')
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-700 font-medium">{{ session('nama') }}</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold" style="background: rgba(99,102,241,0.12); color: #4f46e5;">
                        Admin
                    </span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-auto">
                <div class="p-6">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    @yield('scripts')

    <script>
        (function () {
            const sidebar   = document.getElementById('sidebar');
            const main      = document.getElementById('main-content');
            const toggle    = document.getElementById('sidebar-toggle');
            const overlay   = document.getElementById('sidebar-overlay');
            const isMobile  = () => window.innerWidth < 768;

            // ── Restore saved state ──
            const saved = localStorage.getItem('silpd_sidebar_collapsed');
            let collapsed = saved === 'true';

            function applyState(animate = true) {
                if (!animate) {
                    sidebar.style.transition = 'none';
                    main.style.transition = 'none';
                }
                if (isMobile()) {
                    sidebar.classList.remove('collapsed');
                    main.classList.remove('sidebar-collapsed');
                    if (collapsed) {
                        sidebar.classList.remove('mobile-open');
                        overlay.classList.remove('active');
                    } else {
                        sidebar.classList.add('mobile-open');
                        overlay.classList.add('active');
                    }
                } else {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                    if (collapsed) {
                        sidebar.classList.add('collapsed');
                        main.classList.add('sidebar-collapsed');
                    } else {
                        sidebar.classList.remove('collapsed');
                        main.classList.remove('sidebar-collapsed');
                    }
                }
                if (!animate) {
                    requestAnimationFrame(() => {
                        sidebar.style.transition = '';
                        main.style.transition = '';
                    });
                }
            }

            // Apply on load without animation
            applyState(false);

            // Toggle
            toggle.addEventListener('click', () => {
                collapsed = !collapsed;
                localStorage.setItem('silpd_sidebar_collapsed', collapsed);
                applyState(true);
            });

            // Close sidebar on overlay click (mobile)
            overlay.addEventListener('click', () => {
                collapsed = true;
                applyState(true);
            });

            // Responsive
            window.addEventListener('resize', () => applyState(false));
        })();
    </script>

    {{-- ===== NO-BACK NAVIGATION ===== --}}
    <script>
        (function () {
            // Tambahkan buffer entry ke history saat halaman pertama kali load
            history.pushState(null, '', window.location.href);

            window.addEventListener('popstate', function () {
                // go(1) memaksa browser maju kembali (melawan aksi Back)
                // pushState menambah buffer baru agar bekerja di Back berikutnya
                history.go(1);
                history.pushState(null, '', window.location.href);
            });
        })();
    </script>
</body>
</html>
