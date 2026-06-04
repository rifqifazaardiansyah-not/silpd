@extends('layouts.pengelola')

@section('title', 'Detail Instruksi')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('pengelola.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <a href="{{ route('pengelola.instruksi.index') }}" class="hover:text-gray-700">Instruksi Penyimpanan</a>
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
    <a href="{{ route('pengelola.instruksi.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali
    </a>
</div>

<!-- Status Badge -->
<div class="mb-6">
    @if($instruksi->status === 'pending')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-50 text-amber-700">Pending</span>
    @elseif($instruksi->status === 'selesai')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700">Selesai</span>
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

@if($errors->any())
<div class="p-4 mb-6 bg-red-50 border border-red-200 rounded-lg">
    <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Detail Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Informasi Instruksi</h3>
    </div>

    <div class="p-6 space-y-6">
        <!-- Row 1: Petani & Kelompok Tani -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Petani</p>
                <p class="text-sm text-gray-900 font-medium">{{ $instruksi->detailPanen->panen->petani->nama_petani }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Kelompok Tani</p>
                <p class="text-sm text-gray-900">{{ $instruksi->detailPanen->panen->petani->kelompokTani->nama_kelompok ?? '-' }}</p>
            </div>
        </div>

        <!-- Row 2: Jenis Gabah & Tanggal Panen -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Jenis Gabah</p>
                <p class="text-sm text-gray-900 font-medium">{{ $instruksi->detailPanen->jenisGabah->nama_jenis }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Tanggal Panen</p>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($instruksi->detailPanen->panen->tanggal_panen)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Row 3: Jumlah Panen & Untuk Lumbung (3%) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Jumlah Panen</p>
                <p class="text-sm text-gray-900">{{ number_format($instruksi->detailPanen->jumlah_panen, 2) }} kg</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Untuk Lumbung (3%)</p>
                <p class="text-sm text-gray-900 font-semibold">{{ number_format($instruksi->jumlah, 2) }} kg</p>
            </div>
        </div>

        <!-- Row 4: Lumbung Tujuan & Slot -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Lumbung Tujuan</p>
                <p class="text-sm text-gray-900">{{ $instruksi->slotLumbung->lumbung->nama_lumbung }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Slot</p>
                <p class="text-sm text-gray-900 font-medium">{{ $instruksi->slotLumbung->kode_slot }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Kapasitas Slot -->
<div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
    <div class="flex gap-3">
        <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25.75 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <div class="text-sm text-indigo-800">
            <p class="font-medium">Kapasitas Slot</p>
            <p class="text-indigo-700 mt-1">Tersedia: {{ number_format($instruksi->slotLumbung->kapasitas_tersedia, 2) }} kg | Dibutuhkan: {{ number_format($instruksi->jumlah, 2) }} kg</p>
        </div>
    </div>
</div>

<!-- Konfirmasi Form (jika pending) -->
@if($instruksi->status === 'pending')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Konfirmasi Penyimpanan</h3>
    </div>

    <form action="{{ route('pengelola.instruksi.konfirmasi', $instruksi->id_instruksi) }}" method="POST" class="p-6 space-y-5">
        @csrf

        <!-- Tanggal Masuk -->
        <div>
            <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1.5">
                Tanggal Masuk <span class="text-red-500">*</span>
            </label>
            <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">
            @error('tanggal_masuk')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Catatan -->
        <div>
            <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (Opsional)</label>
            <textarea id="catatan" name="catatan" rows="3" placeholder="Catatan penyimpanan…" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">{{ old('catatan') }}</textarea>
            @error('catatan')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Konfirmasi Penyimpanan
            </button>
            <a href="{{ route('pengelola.instruksi.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@elseif($instruksi->status === 'selesai')
<!-- Penyimpanan Info (jika selesai) -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Informasi Penyimpanan</h3>
    </div>

    <div class="p-6 space-y-4">
        @php
            // Ambil penyimpanan dari instruksi (one-to-one relationship)
            $penyimpanan = $instruksi->penyimpananGabah;
        @endphp

        @if($penyimpanan)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Tanggal Masuk</p>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($penyimpanan->tanggal_masuk)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Jumlah Disimpan</p>
                <p class="text-sm text-gray-900 font-semibold">{{ number_format($penyimpanan->jumlah_masuk, 2) }} kg</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Stok Saat Ini</p>
                <p class="text-sm text-gray-900">{{ number_format($penyimpanan->jumlah, 2) }} kg</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Status</p>
                @if($penyimpanan->status === 'tersimpan')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Tersimpan</span>
                @elseif($penyimpanan->status === 'diambil')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700">Diambil</span>
                @elseif($penyimpanan->status === 'habis')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-50 text-gray-700">Habis</span>
                @endif
            </div>
        </div>

        @if($penyimpanan->jumlah_masuk != $penyimpanan->jumlah)
        <div class="pt-4 border-t border-gray-100">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex gap-2 items-start">
                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <p class="text-xs text-blue-800">
                        <strong>Sudah ada pengambilan:</strong> Dari {{ number_format($penyimpanan->jumlah_masuk, 2) }} kg yang disimpan, sudah diambil {{ number_format($penyimpanan->jumlah_masuk - $penyimpanan->jumlah, 2) }} kg.
                    </p>
                </div>
            </div>
        </div>
        @endif
        @else
        <p class="text-sm text-gray-500">Data penyimpanan tidak ditemukan.</p>
        @endif
    </div>
</div>
@endif
@endsection
