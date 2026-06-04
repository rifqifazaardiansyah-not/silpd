@extends('layouts.admin')

@section('title', 'Detail Instruksi')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <a href="{{ route('admin.instruksi.index') }}" class="hover:text-gray-700">Instruksi Simpan</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Detail</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Instruksi #{{ $instruksi->id_instruksi }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($instruksi->created_at)->format('d M Y H:i') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.instruksi.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>

<!-- Status Badge -->
<div class="mb-6">
    @if($instruksi->status === 'pending')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-50 text-amber-700">Pending</span>
    @elseif($instruksi->status === 'selesai')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700">Selesai</span>
    @endif
</div>

<!-- Detail Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Informasi Instruksi</h3>
    </div>

    <div class="p-6 space-y-6">
        <!-- Petani Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Petani</p>
                <p class="text-sm text-gray-900">{{ $instruksi->detailPanen->panen->petani->nama_petani }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Kelompok Tani</p>
                <p class="text-sm text-gray-900">{{ $instruksi->detailPanen->panen->petani->kelompokTani->nama_kelompok ?? '-' }}</p>
            </div>
        </div>

        <!-- Gabah Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Jenis Gabah</p>
                <p class="text-sm text-gray-900">{{ $instruksi->detailPanen->jenisGabah->nama_jenis }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Jumlah Panen</p>
                <p class="text-sm text-gray-900">{{ number_format($instruksi->detailPanen->jumlah_panen) }} kg</p>
            </div>
        </div>

        <!-- Slot Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Lumbung Tujuan</p>
                <p class="text-sm text-gray-900">{{ $instruksi->slotLumbung->lumbung->nama_lumbung }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Slot</p>
                <p class="text-sm text-gray-900">{{ $instruksi->slotLumbung->kode_slot }}</p>
            </div>
        </div>

        <!-- Jumlah dan Tanggal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Untuk Lumbung</p>
                <p class="text-sm text-gray-900 font-semibold">{{ number_format($instruksi->jumlah) }} kg</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Tanggal Panen</p>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($instruksi->detailPanen->panen->tanggal_panen)->format('d M Y') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Pengelola Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Pengelola Lumbung</h3>
    </div>

    <div class="p-6">
        @forelse($instruksi->slotLumbung->lumbung->pengelola as $pengelola)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $pengelola->nama_pengelola }}</p>
                    <p class="text-xs text-gray-500">{{ $pengelola->no_hp }}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $pengelola->pivot->peran === 'pemilik_akun' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $pengelola->pivot->peran === 'pemilik_akun' ? 'Pemilik Akun' : 'Anggota' }}
                </span>
            </div>
        @empty
            <p class="text-sm text-gray-500">Tidak ada pengelola</p>
        @endforelse
    </div>
</div>

<!-- Penyimpanan Info (jika selesai) -->
@if($penyimpanan)
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Informasi Penyimpanan</h3>
    </div>

    <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Tanggal Masuk</p>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($penyimpanan->tanggal_masuk)->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Jumlah Aktual</p>
                <p class="text-sm text-gray-900">{{ number_format($penyimpanan->jumlah_aktual) }} kg</p>
            </div>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Catatan</p>
            <p class="text-sm text-gray-900">{{ $penyimpanan->catatan ?? '-' }}</p>
        </div>
    </div>
</div>
@endif

<!-- Actions -->
@if($instruksi->status === 'pending')
<div class="flex gap-2">
    <a href="{{ route('admin.instruksi.pindah-slot', $instruksi->id_instruksi) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.16 3.5H6a2.25 2.25 0 00-2.25 2.25v12.5A2.25 2.25 0 006 20.25h12a2.25 2.25 0 002.25-2.25V5.75A2.25 2.25 0 0018 3.5h-1.16m-7 4v6m0 0v6m0-6h6m-6 0H9.84" />
        </svg>
        Pindah Slot
    </a>
    <form action="{{ route('admin.instruksi.destroy', $instruksi->id_instruksi) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors" onclick="return confirm('Yakin ingin menghapus instruksi ini?')">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L19.18 2.318a2.25 2.25 0 00-2.163-1.318H5.183a2.25 2.25 0 00-2.163 1.318L2.012 6.54m15.11 0v3.375c0 .621-.504 1.125-1.125 1.125H3.375c-.621 0-1.125-.504-1.125-1.125V6.54" />
            </svg>
            Hapus
        </button>
    </form>
</div>
@endif
@endsection
