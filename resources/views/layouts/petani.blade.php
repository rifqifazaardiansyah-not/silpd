<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SILPD') - Portal Petani</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-feature-settings: "liga" 1, "cv02" 1, "cv03" 1, "cv04" 1;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 text-white fixed h-screen overflow-y-auto" style="background-color: #0F172A;">
            <!-- Brand -->
            <div class="px-6 py-6" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-2xl">🌾</span>
                    <span class="text-lg font-semibold tracking-tight">SILPD</span>
                </div>
                <p class="text-sm" style="color: rgba(255, 255, 255, 0.7);">Portal Petani</p>
            </div>

            <!-- Navigation -->
            <nav class="px-3 py-6 space-y-3">
                <a href="{{ route('petani.dashboard') }}"
                   class="block px-3 py-2 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('petani.dashboard') ? 'bg-white/20 text-white font-medium' : 'text-white/70 hover:bg-white/10' }}">
                    Dashboard
                </a>
                <a href="{{ route('petani.stok.index') }}"
                   class="block px-3 py-2 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('petani.stok.*') ? 'bg-white/20 text-white font-medium' : 'text-white/70 hover:bg-white/10' }}">
                    Stok Gabah Saya
                </a>
                <a href="{{ route('petani.permintaan.index') }}"
                   class="block px-3 py-2 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('petani.permintaan.*') ? 'bg-white/20 text-white font-medium' : 'text-white/70 hover:bg-white/10' }}">
                    Permintaan Pengambilan
                </a>
            </nav>

            <!-- Logout Button -->
            <div class="absolute bottom-6 left-3 right-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full px-3 py-2 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white transition-colors text-left">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col">
            <!-- Topbar -->
            <div class="bg-white border-b border-gray-200 px-6 py-3 flex justify-between items-center">
                <div>
                    @yield('breadcrumb')
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-700">{{ session('nama') }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium" style="background-color: rgba(5, 150, 105, 0.15); color: #059669;">
                        Petani
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
        // Preserve sidebar scroll position on navigation
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const scrollPos = sessionStorage.getItem('sidebarScrollPos');
            
            // Restore scroll position
            if (scrollPos !== null) {
                sidebar.scrollTop = parseInt(scrollPos);
                sessionStorage.removeItem('sidebarScrollPos');
            }
        });
        
        // Save scroll position before navigation
        document.querySelectorAll('aside a').forEach(link => {
            link.addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                sessionStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
            });
        });
    </script>
</body>
</html>
