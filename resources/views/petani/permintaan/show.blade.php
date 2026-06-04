@extends('layouts.petani')

@section('title', 'Detail Permintaan')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('petani.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <a href="{{ route('petani.permintaan.index') }}" class="hover:text-gray-700">Permintaan Pengambilan</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Detail</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Permintaan #{{ $permintaan->id_permintaan }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->format('d M Y H:i') }}</p>
    </div>
    <a href="{{ route('petani.permintaan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali
    </a>
</div>

<!-- Status Badge -->
<div class="mb-6">
    @if($permintaan->status === 'pending')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-50 text-amber-700">Pending</span>
    @elseif($permintaan->status === 'disetujui')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-50 text-indigo-700">Disetujui</span>
    @elseif($permintaan->status === 'selesai')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700">Selesai</span>
    @elseif($permintaan->status === 'ditolak')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-50 text-red-700">Ditolak</span>
    @endif
</div>

<!-- Flash Messages -->
@if(session('success'))
<div class="flex items-start gap-3 p-4 mb-6 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Detail Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Informasi Permintaan</h3>
    </div>

    <div class="p-6 space-y-6">
        <!-- Row 1: Jenis Gabah & Tanggal Masuk Gabah -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Jenis Gabah</p>
                <p class="text-sm text-gray-900 font-medium">{{ $permintaan->penyimpananGabah->detailPanen->jenisGabah->nama_jenis }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Tanggal Masuk Gabah</p>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($permintaan->penyimpananGabah->tanggal_masuk)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Row 2: Jumlah Diminta & Lumbung -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Jumlah Diminta</p>
                <p class="text-sm text-gray-900 font-semibold">{{ number_format($permintaan->detailPengambilan->sum('jumlah'), 2) }} kg</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Lumbung</p>
                <p class="text-sm text-gray-900">{{ $permintaan->penyimpananGabah->slotLumbung->lumbung->nama_lumbung }}</p>
            </div>
        </div>

        <!-- Row 3: Slot & Alasan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Slot</p>
                <p class="text-sm text-gray-900 font-medium">{{ $permintaan->penyimpananGabah->slotLumbung->kode_slot }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Alasan Pengambilan</p>
                <p class="text-sm text-gray-900">{{ $permintaan->detailPengambilan->first()?->alasan ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Status Tracker -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Status Permintaan</h3>
    </div>

    <div class="p-6">
        <div class="flex items-center justify-between">
            <!-- Diajukan -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-900 mt-2">Diajukan</p>
            </div>

            <!-- Line -->
            <div class="flex-1 h-1 bg-gray-200 mx-2"></div>

            <!-- Menunggu Persetujuan -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full {{ in_array($permintaan->status, ['pending', 'disetujui', 'selesai']) ? 'bg-emerald-100' : 'bg-gray-100' }} flex items-center justify-center">
                    @if(in_array($permintaan->status, ['disetujui', 'selesai']))
                        <svg class="w-5 h-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    @else
                        <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                    @endif
                </div>
                <p class="text-xs font-medium text-gray-900 mt-2">Menunggu</p>
            </div>

            <!-- Line -->
            <div class="flex-1 h-1 bg-gray-200 mx-2"></div>

            <!-- Disetujui -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full {{ in_array($permintaan->status, ['disetujui', 'selesai']) ? 'bg-emerald-100' : 'bg-gray-100' }} flex items-center justify-center">
                    @if(in_array($permintaan->status, ['selesai']))
                        <svg class="w-5 h-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    @else
                        <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                    @endif
                </div>
                <p class="text-xs font-medium text-gray-900 mt-2">Disetujui</p>
            </div>

            <!-- Line -->
            <div class="flex-1 h-1 bg-gray-200 mx-2"></div>

            <!-- Selesai -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full {{ $permintaan->status === 'selesai' ? 'bg-emerald-100' : 'bg-gray-100' }} flex items-center justify-center">
                    @if($permintaan->status === 'selesai')
                        <svg class="w-5 h-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    @else
                        <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                    @endif
                </div>
                <p class="text-xs font-medium text-gray-900 mt-2">Selesai</p>
            </div>
        </div>
    </div>
</div>

<!-- Batalkan Permintaan (jika pending) -->
@if($permintaan->status === 'pending')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Aksi</h3>
    </div>

    <div class="p-6">
        <form action="{{ route('petani.permintaan.batal', $permintaan->id_permintaan) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors" onclick="return confirm('Yakin ingin membatalkan permintaan ini?')">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Batalkan Permintaan
            </button>
        </form>
    </div>
</div>
@elseif($permintaan->status === 'ditolak')
<!-- Alasan Penolakan (jika ditolak) -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Alasan Penolakan</h3>
    </div>

    <div class="p-6">
        <p class="text-sm text-gray-900">{{ $permintaan->detailPengambilan->first()?->alasan_penolakan ?? 'Tidak ada alasan yang diberikan' }}</p>
    </div>
</div>
@endif
@endsection
