@extends('layouts.admin')

@section('title', 'Laporan Stok Gabah')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Laporan Stok Gabah</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Laporan Stok Gabah</h1>
        <p class="text-sm text-gray-500 mt-1">Rekapitulasi stok gabah di semua lumbung</p>
    </div>
    <a href="{{ route('admin.laporan.ekspor.stok') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        Ekspor CSV
    </a>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Stok</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalStokKeseluruhan, 2, ',', '.') }} kg</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Lot Kadaluarsa</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-red-600">{{ $jumlahKadaluarsa }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Lumbung Aktif</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ $lumbungList->count() }}</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.laporan.stok') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Petani Search (Paling Kiri) -->
            <div>
                <label for="search_petani" class="block text-sm font-medium text-gray-700 mb-1.5">Cari Petani</label>
                <input type="text" 
                       id="search_petani" 
                       name="search_petani" 
                       value="{{ request('search_petani') }}"
                       placeholder="Ketik nama petani..."
                       class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
            </div>

            <!-- Lumbung Filter -->
            <div>
                <label for="id_lumbung" class="block text-sm font-medium text-gray-700 mb-1.5">Lumbung</label>
                <select id="id_lumbung" name="id_lumbung" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    <option value="">Semua lumbung</option>
                    @foreach($lumbungList as $lumbung)
                        <option value="{{ $lumbung->id_lumbung }}" {{ request('id_lumbung') == $lumbung->id_lumbung ? 'selected' : '' }}>
                            {{ $lumbung->nama_lumbung }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jenis Gabah Filter -->
            <div>
                <label for="id_jenis_gabah" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Gabah</label>
                <select id="id_jenis_gabah" name="id_jenis_gabah" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
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
                <select id="status" name="status" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    <option value="">Semua status</option>
                    <option value="tersimpan" {{ request('status') === 'tersimpan' ? 'selected' : '' }}>Tersimpan</option>
                    <option value="kadaluarsa" {{ request('status') === 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                </select>
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.5 5.5a7.5 7.5 0 0010.5 10.5z" />
                </svg>
                Filter
            </button>
            <a href="{{ route('admin.laporan.stok') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Rekapitulasi per Lumbung -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Rekapitulasi per Lumbung</h3>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Lumbung</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Stok</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kapasitas</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rekapPerLumbung as $lumbung)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $lumbung->nama_lumbung }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($lumbung->total_stok, 2, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm">
                        @php
                            $persenTerpakai = ($lumbung->total_stok / $lumbung->kapasitas_total) * 100;
                            $barColor = $persenTerpakai >= 80 ? 'bg-red-500' : ($persenTerpakai >= 60 ? 'bg-amber-400' : 'bg-emerald-500');
                        @endphp
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $barColor }} rounded-full" style="width: {{ $persenTerpakai }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 w-12 text-right">{{ number_format($persenTerpakai, 2, ',', '.') }}%</span>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada data lumbung
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Rekapitulasi per Jenis Gabah -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Rekapitulasi per Jenis Gabah</h3>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Stok</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Lot Tersimpan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rekapPerJenis as $jenis)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $jenis->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($jenis->total_stok, 2, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $jenis->jumlah_lot }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada data jenis gabah
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Detail Stok -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Detail Stok</h3>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Lumbung / Slot</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($stokList as $stok)
                <tr class="hover:bg-gray-50 transition-colors {{ $stok->is_kadaluarsa ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stok->slotLumbung->lumbung->nama_lumbung }} / {{ $stok->slotLumbung->kode_slot }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stok->detailPanen->panen->petani->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stok->detailPanen->jenisGabah->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($stok->jumlah, 2, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($stok->tanggal_masuk)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($stok->is_kadaluarsa)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700">Kadaluarsa</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Tersimpan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada data stok
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
