{{-- Sidebar Navigation berdasarkan Role --}}

@if(session('role') === 'admin')
    {{-- SIDEBAR ADMIN --}}
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase small text-muted" style="cursor: default;">
                <i class="fas fa-leaf"></i> Manajemen Panen
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.panen.*') ? 'active' : '' }}" 
               href="{{ route('admin.panen.index') }}">
                <i class="fas fa-list"></i> Data Panen
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase small text-muted" style="cursor: default;">
                <i class="fas fa-warehouse"></i> Manajemen Lumbung
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.lumbung.*') ? 'active' : '' }}" 
               href="{{ route('admin.lumbung.index') }}">
                <i class="fas fa-building"></i> Lumbung
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.slot-lumbung.*') ? 'active' : '' }}" 
               href="{{ route('admin.slot-lumbung.index') }}">
                <i class="fas fa-th"></i> Slot Lumbung
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.instruksi.*') ? 'active' : '' }}" 
               href="{{ route('admin.instruksi.index') }}">
                <i class="fas fa-clipboard-list"></i> Instruksi Penyimpanan
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase small text-muted" style="cursor: default;">
                <i class="fas fa-handshake"></i> Manajemen Petani
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.petani.*') ? 'active' : '' }}" 
               href="{{ route('admin.petani.index') }}">
                <i class="fas fa-user-tie"></i> Data Petani
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.kelompok-tani.*') ? 'active' : '' }}" 
               href="{{ route('admin.kelompok-tani.index') }}">
                <i class="fas fa-users"></i> Kelompok Tani
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase small text-muted" style="cursor: default;">
                <i class="fas fa-cog"></i> Pengaturan
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.jenis-gabah.*') ? 'active' : '' }}" 
               href="{{ route('admin.jenis-gabah.index') }}">
                <i class="fas fa-tag"></i> Jenis Gabah
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.pengelola.*') ? 'active' : '' }}" 
               href="{{ route('admin.pengelola.index') }}">
                <i class="fas fa-user-shield"></i> Pengelola
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}" 
               href="{{ route('admin.akun.index') }}">
                <i class="fas fa-user-secret"></i> Manajemen Akun
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase small text-muted" style="cursor: default;">
                <i class="fas fa-check-circle"></i> Permintaan
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.permintaan.*') ? 'active' : '' }}" 
               href="{{ route('admin.permintaan.index') }}">
                <i class="fas fa-inbox"></i> Permintaan Pengambilan
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase small text-muted" style="cursor: default;">
                <i class="fas fa-chart-bar"></i> Laporan
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}" 
               href="{{ route('admin.laporan.index') }}">
                <i class="fas fa-file-csv"></i> Laporan
            </a>
        </li>
    </ul>

@elseif(session('role') === 'pengelola')
    {{-- SIDEBAR PENGELOLA --}}
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pengelola.dashboard') ? 'active' : '' }}" 
               href="{{ route('pengelola.dashboard') }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase small text-muted" style="cursor: default;">
                <i class="fas fa-inbox"></i> Instruksi
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('pengelola.instruksi.*') ? 'active' : '' }}" 
               href="{{ route('pengelola.instruksi.index') }}">
                <i class="fas fa-clipboard-list"></i> Instruksi Penyimpanan
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase small text-muted" style="cursor: default;">
                <i class="fas fa-warehouse"></i> Pengelolaan Stok
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('pengelola.stok.*') ? 'active' : '' }}" 
               href="{{ route('pengelola.stok.index') }}">
                <i class="fas fa-list"></i> Stok Lumbung
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('pengelola.pengeluaran.*') ? 'active' : '' }}" 
               href="{{ route('pengelola.pengeluaran.index') }}">
                <i class="fas fa-arrow-up"></i> Pengeluaran Gabah
            </a>
        </li>
    </ul>

@elseif(session('role') === 'petani')
    {{-- SIDEBAR PETANI --}}
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('petani.dashboard') ? 'active' : '' }}" 
               href="{{ route('petani.dashboard') }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase small text-muted" style="cursor: default;">
                <i class="fas fa-warehouse"></i> Stok Gabah Saya
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('petani.stok.*') ? 'active' : '' }}" 
               href="{{ route('petani.stok.index') }}">
                <i class="fas fa-list"></i> Stok Saya
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase small text-muted" style="cursor: default;">
                <i class="fas fa-hand-holding-heart"></i> Permintaan
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('petani.permintaan.*') ? 'active' : '' }}" 
               href="{{ route('petani.permintaan.index') }}">
                <i class="fas fa-inbox"></i> Permintaan Pengambilan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ps-4 {{ request()->routeIs('petani.permintaan.create') ? 'active' : '' }}" 
               href="{{ route('petani.permintaan.create') }}">
                <i class="fas fa-plus-circle"></i> Buat Permintaan
            </a>
        </li>
    </ul>

@endif
