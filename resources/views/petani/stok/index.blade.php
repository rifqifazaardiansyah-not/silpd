@extends('layouts.petani')

@section('title', 'Stok Gabah Saya')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('petani.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Stok Gabah Saya</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Stok Gabah Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau stok gabah yang tersimpan di lumbung</p>
    </div>
    <a href="{{ route('petani.permintaan.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Ajukan Permintaan
    </a>
</div>

<!-- Flash Messages -->
@if(session('warning'))
<div class="flex items-start gap-3 p-4 mb-6 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
    </svg>
    <span>{{ session('warning') }}</span>
</div>
@endif

@if(session('info'))
<div class="flex items-start gap-3 p-4 mb-6 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 text-sm">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
    </svg>
    <span>{{ session('info') }}</span>
</div>
@endif

<!-- Stat Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Tersimpan</p>
    <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalTersimpan, 2) }} kg</p>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('petani.stok.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Jenis Gabah Filter -->
            <div>
                <label for="id_jenis_gabah" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Gabah</label>
                <select id="id_jenis_gabah" name="id_jenis_gabah" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-colors">
                    <option value="">Semua jenis</option>
                    @foreach($jenisGabahList as $jenis)
                        <option value="{{ $jenis->id_jenis_gabah }}" {{ request('id_jenis_gabah') == $jenis->id_jenis_gabah ? 'selected' : '' }}>
                            {{ $jenis->nama_jenis }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-colors">
                    <option value="">Semua status</option>
                    <option value="tersimpan" {{ request('status') === 'tersimpan' ? 'selected' : '' }}>Tersimpan</option>
                    <option value="habis" {{ request('status') === 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.5 5.5a7.5 7.5 0 0010.5 10.5z" />
                </svg>
                Filter
            </button>
            <a href="{{ route('petani.stok.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Rekap per Jenis Gabah -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Rekap per Jenis Gabah</h3>
    </div>

    <div class="p-6 space-y-3">
        @forelse($rekapPerJenis as $jenis)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $jenis['nama_jenis'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $jenis['jumlah_lot'] }} lot tersimpan</p>
                </div>
                <p class="text-sm font-semibold text-gray-900">{{ number_format($jenis['total'], 2) }} kg</p>
            </div>
        @empty
            <p class="text-sm text-gray-500">Tidak ada stok gabah</p>
        @endforelse
    </div>
</div>

<!-- Info FIFO -->
<div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
    <div class="flex gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25.75 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <div class="text-sm text-amber-800">
            <p class="font-medium">Sistem FIFO (First In First Out)</p>
            <p class="text-amber-700 mt-1">Gabah yang paling lama akan diambil terlebih dahulu saat pengambilan. Tabel di bawah sudah diurutkan berdasarkan tanggal masuk.</p>
        </div>
    </div>
</div>

<!-- Detail Stok Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Lumbung</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Slot</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Umur Simpan</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($stokList as $stok)
                @php
                    $umurHari = \Carbon\Carbon::parse($stok->tanggal_masuk)->diffInDays(now());
                    $batasHari = config('silpd.batas_hari_simpan', 90);
                    $isKadaluarsa = $umurHari > $batasHari;
                @endphp
                <tr class="hover:bg-gray-50 transition-colors {{ $isKadaluarsa ? 'bg-amber-50' : '' }}">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stok->detailPanen->jenisGabah->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stok->slotLumbung->lumbung->nama_lumbung }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stok->slotLumbung->kode_slot }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($stok->jumlah, 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($stok->tanggal_masuk)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $umurHari }} hari</td>
                    <td class="px-4 py-3 text-sm">
                        @if($isKadaluarsa)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700">Kadaluarsa</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Tersimpan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada stok gabah
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($stokList->count())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $stokList->firstItem() }}–{{ $stokList->lastItem() }} dari {{ $stokList->total() }} data
        </p>
        {{ $stokList->withQueryString()->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endsection
