@extends('layouts.pengelola')

@section('title', 'Detail Pengeluaran')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('pengelola.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <a href="{{ route('pengelola.pengeluaran.index') }}" class="hover:text-gray-700">Pengeluaran Gabah</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Detail</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Permintaan #{{ $permintaan->id_permintaan }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->format('d M Y') }} • {{ $permintaan->petani->nama_petani }}</p>
    </div>
    <a href="{{ route('pengelola.pengeluaran.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali
    </a>
</div>

<!-- Status Badge -->
<div class="mb-6">
    @if($permintaan->status === 'disetujui')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-50 text-indigo-700">Disetujui</span>
    @elseif($permintaan->status === 'selesai')
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
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Informasi Permintaan</h3>
    </div>

    <div class="p-6 space-y-6">
        <!-- Row 1: Petani & Kelompok Tani -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Petani</p>
                <p class="text-sm text-gray-900 font-medium">{{ $permintaan->petani->nama_petani }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Kelompok Tani</p>
                <p class="text-sm text-gray-900">{{ $permintaan->petani->kelompokTani->nama_kelompok ?? '-' }}</p>
            </div>
        </div>

        <!-- Row 2: Jenis Gabah & Jumlah Diminta -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Jenis Gabah</p>
                <p class="text-sm text-gray-900 font-medium">{{ $permintaan->penyimpananGabah->detailPanen->jenisGabah->nama_jenis }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Jumlah Diminta</p>
                <p class="text-sm text-gray-900 font-semibold">{{ number_format($permintaan->detailPengambilan->sum('jumlah'), 2) }} kg</p>
            </div>
        </div>

        <!-- Row 3: Lumbung & Slot -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Lumbung</p>
                <p class="text-sm text-gray-900">{{ $permintaan->penyimpananGabah->slotLumbung->lumbung->nama_lumbung }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Slot</p>
                <p class="text-sm text-gray-900 font-medium">{{ $permintaan->penyimpananGabah->slotLumbung->kode_slot }}</p>
            </div>
        </div>

        <!-- Row 4: Tanggal Masuk Gabah -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Tanggal Masuk Gabah</p>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($permintaan->penyimpananGabah->tanggal_masuk)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Alasan -->
        <div class="pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Alasan Pengambilan</p>
            <p class="text-sm text-gray-900">{{ $permintaan->detailPengambilan->first()?->alasan ?? '-' }}</p>
        </div>
    </div>
</div>

<!-- Stok Preview -->
<div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
    <div class="flex gap-3">
        <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25.75 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <div class="text-sm text-indigo-800">
            <p class="font-medium">Preview Stok Setelah Pengeluaran</p>
            @php
                $jumlahDiminta = $permintaan->detailPengambilan->sum('jumlah');
                $stokSaatIni = $permintaan->penyimpananGabah->jumlah;
                $sisaStok = $stokSaatIni - $jumlahDiminta;
            @endphp
            <p class="text-indigo-700 mt-1">
                Stok saat ini: <strong>{{ number_format($stokSaatIni, 2) }} kg</strong>
                <br>
                Akan dikeluarkan: <strong>{{ number_format($jumlahDiminta, 2) }} kg</strong>
                <br>
                Sisa: <strong>{{ number_format($sisaStok, 2) }} kg</strong>
            </p>
        </div>
    </div>
</div>

<!-- Konfirmasi Form (jika disetujui) -->
@if($permintaan->status === 'disetujui')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Konfirmasi Pengeluaran</h3>
    </div>

    <form action="{{ route('pengelola.pengeluaran.konfirmasi', $permintaan->id_permintaan) }}" method="POST" class="p-6 space-y-5">
        @csrf

        <!-- Tanggal Pengeluaran -->
        <div>
            <label for="tanggal_pengeluaran" class="block text-sm font-medium text-gray-700 mb-1.5">
                Tanggal Pengeluaran <span class="text-red-500">*</span>
            </label>
            <input type="date" id="tanggal_pengeluaran" name="tanggal_pengeluaran" value="{{ old('tanggal_pengeluaran', date('Y-m-d')) }}" required class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">
            @error('tanggal_pengeluaran')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Catatan -->
        <div>
            <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (Opsional)</label>
            <textarea id="catatan" name="catatan" rows="3" placeholder="Catatan pengeluaran…" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">{{ old('catatan') }}</textarea>
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
                Konfirmasi Pengeluaran
            </button>
            <a href="{{ route('pengelola.pengeluaran.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@elseif($permintaan->status === 'selesai')
<!-- Pengeluaran Info (jika selesai) -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Informasi Pengeluaran</h3>
    </div>

    <div class="p-6 space-y-4">
        @php
            $detailPengambilan = $permintaan->detailPengambilan->first();
            $jumlahDikeluarkan = $permintaan->detailPengambilan->sum('jumlah');
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Tanggal Pengeluaran</p>
                <p class="text-sm text-gray-900">{{ $detailPengambilan && $detailPengambilan->created_at ? \Carbon\Carbon::parse($detailPengambilan->created_at)->format('d M Y H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Jumlah Dikeluarkan</p>
                <p class="text-sm text-gray-900 font-semibold">{{ number_format($jumlahDikeluarkan, 2) }} kg</p>
            </div>
        </div>

        @if($detailPengambilan && $detailPengambilan->catatan)
        <div class="pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Catatan</p>
            <p class="text-sm text-gray-900">{{ $detailPengambilan->catatan }}</p>
        </div>
        @endif
    </div>
</div>
@endif
@endsection
