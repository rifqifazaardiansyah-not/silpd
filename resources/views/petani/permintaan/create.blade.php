@extends('layouts.petani')

@section('title', 'Ajukan Permintaan Pengambilan')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('petani.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <a href="{{ route('petani.permintaan.index') }}" class="hover:text-gray-700">Permintaan Pengambilan</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Ajukan</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Ajukan Permintaan Pengambilan</h1>
        <p class="text-sm text-gray-500 mt-1">Pilih gabah yang ingin Anda ambil dari lumbung</p>
    </div>
    <a href="{{ route('petani.permintaan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali
    </a>
</div>

<!-- Info Alert -->
<div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
    <div class="flex gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25.75 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <div class="text-sm text-amber-800">
            <p class="font-medium">Sistem FIFO (First In First Out)</p>
            <p class="text-amber-700 mt-1">Pilih gabah yang ingin Anda ambil. Sistem merekomendasikan mengambil dari lot yang paling lama tersimpan terlebih dahulu.</p>
        </div>
    </div>
</div>

<!-- Flash Messages -->
@if($errors->any())
<div class="p-4 mb-6 bg-red-50 border border-red-200 rounded-lg">
    <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Form Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Pilih Gabah</h3>
    </div>

    <form action="{{ route('petani.permintaan.store') }}" method="POST" class="p-6">
        @csrf

        <!-- Stok Selection -->
        <div class="space-y-3 mb-6">
            @forelse($stokTersimpan as $stok)
                <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors {{ \Carbon\Carbon::parse($stok->tanggal_masuk)->diffInDays(now()) === 0 ? 'ring-2 ring-emerald-500' : '' }}">
                    <input type="radio" name="id_penyimpanan" value="{{ $stok->id_penyimpanan }}" required class="mt-1" {{ old('id_penyimpanan') == $stok->id_penyimpanan ? 'checked' : '' }}>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-gray-900">{{ $stok->detailPanen->jenisGabah->nama_jenis }}</p>
                            @if(\Carbon\Carbon::parse($stok->tanggal_masuk)->diffInDays(now()) === 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700">Rekomendasi FIFO</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-2 text-xs text-gray-600">
                            <div>
                                <p class="text-gray-500">Lumbung</p>
                                <p class="font-medium text-gray-900">{{ $stok->slotLumbung->lumbung->nama_lumbung }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Slot</p>
                                <p class="font-medium text-gray-900">{{ $stok->slotLumbung->kode_slot }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Jumlah Tersisa</p>
                                <p class="font-medium text-gray-900">{{ number_format($stok->jumlah, 2) }} kg</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Tanggal Masuk</p>
                                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($stok->tanggal_masuk)->format('d M Y') }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Umur simpan: {{ \Carbon\Carbon::parse($stok->tanggal_masuk)->diffInDays(now()) }} hari</p>
                    </div>
                </label>
            @empty
                <div class="p-6 text-center text-gray-500">
                    <p class="text-sm">Tidak ada stok gabah yang tersedia</p>
                </div>
            @endforelse
        </div>

        @error('id_penyimpanan')
            <p class="text-xs text-red-600 mb-6">{{ $message }}</p>
        @enderror

        <!-- Jumlah Diminta -->
        <div class="mb-6">
            <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1.5">
                Jumlah yang Ingin Diambil (kg) <span class="text-red-500">*</span>
            </label>
            <input type="number" id="jumlah" name="jumlah" min="0" step="0.01" required placeholder="0" value="{{ old('jumlah') }}" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-colors">
            @error('jumlah')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alasan Pengambilan -->
        <div class="mb-6">
            <label for="alasan" class="block text-sm font-medium text-gray-700 mb-1.5">
                Alasan Pengambilan <span class="text-red-500">*</span>
            </label>
            <textarea id="alasan" name="alasan" rows="4" required placeholder="Jelaskan alasan pengambilan (misal: gagal panen, kebutuhan konsumsi, dll)…" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-colors">{{ old('alasan') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Minimal 10 karakter</p>
            @error('alasan')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Ajukan Permintaan
            </button>
            <a href="{{ route('petani.permintaan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
