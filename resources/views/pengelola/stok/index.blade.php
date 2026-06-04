@extends('layouts.pengelola')

@section('title', 'Monitor Stok')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('pengelola.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Monitor Stok</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Monitor Stok Gabah</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau stok gabah di semua slot lumbung</p>
    </div>
</div>

<!-- Stat Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Stok Tersimpan</p>
    <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($stokList->sum('jumlah'), 2) }} kg</p>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('pengelola.stok.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Lumbung Filter -->
            <div>
                <label for="id_lumbung" class="block text-sm font-medium text-gray-700 mb-1.5">Lumbung</label>
                <select id="id_lumbung" name="id_lumbung" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">
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
                <select id="id_jenis_gabah" name="id_jenis_gabah" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">
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
                <select id="status" name="status" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">
                    <option value="">Semua status</option>
                    <option value="tersimpan" {{ request('status') === 'tersimpan' ? 'selected' : '' }}>Tersimpan</option>
                    <option value="kadaluarsa" {{ request('status') === 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                </select>
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.5 5.5a7.5 7.5 0 0010.5 10.5z" />
                </svg>
                Filter
            </button>
            <a href="{{ route('pengelola.stok.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Ringkasan Kapasitas Lumbung -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Ringkasan Kapasitas Lumbung</h3>
    </div>

    <div class="p-6 space-y-4">
        @forelse($ringkasanKapasitas as $lumbung)
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-gray-900">{{ $lumbung['nama_lumbung'] }}</p>
                    <span class="text-xs text-gray-500">{{ number_format($lumbung['total_terpakai'], 2) }} / {{ number_format($lumbung['total_kapasitas'], 2) }} kg</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    @php
                        $persenTerpakai = $lumbung['persen_terpakai'];
                        $barColor = $persenTerpakai >= 80 ? 'bg-red-500' : ($persenTerpakai >= 60 ? 'bg-amber-400' : 'bg-emerald-500');
                    @endphp
                    <div class="h-full {{ $barColor }} rounded-full" style="width: {{ $persenTerpakai }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ number_format($persenTerpakai, 1) }}% terpakai · {{ $lumbung['jumlah_slot'] }} slot</p>
            </div>
        @empty
            <p class="text-sm text-gray-500">Tidak ada data lumbung</p>
        @endforelse
    </div>
</div>

<!-- Detail Stok Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Lumbung / Slot</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Umur Simpan</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($stokList as $stok)
                @php
                    $batasHari = config('silpd.batas_hari_simpan', 90);
                    $batasDate = \Carbon\Carbon::now()->subDays($batasHari);
                    $isKadaluarsa = \Carbon\Carbon::parse($stok->tanggal_masuk)->lte($batasDate);
                    $umurHari = \Carbon\Carbon::parse($stok->tanggal_masuk)->diffInDays(now());
                @endphp
                <tr class="hover:bg-gray-50 transition-colors {{ $isKadaluarsa ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stok->slotLumbung->lumbung->nama_lumbung }} / {{ $stok->slotLumbung->kode_slot }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stok->detailPanen->panen->petani->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stok->detailPanen->jenisGabah->nama_jenis }}</td>
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
