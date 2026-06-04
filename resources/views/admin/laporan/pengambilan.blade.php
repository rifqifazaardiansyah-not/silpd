@extends('layouts.admin')

@section('title', 'Laporan Pengambilan')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Laporan Pengambilan</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Laporan Pengambilan Gabah</h1>
        <p class="text-sm text-gray-500 mt-1">Rekapitulasi pengambilan gabah oleh petani</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.laporan.pengambilan') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Date Range -->
            <div>
                <label for="dari" class="block text-sm font-medium text-gray-700 mb-1.5">Dari Tanggal</label>
                <input type="date" id="dari" name="dari" value="{{ request('dari', $dari) }}" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
            </div>

            <div>
                <label for="sampai" class="block text-sm font-medium text-gray-700 mb-1.5">Sampai Tanggal</label>
                <input type="date" id="sampai" name="sampai" value="{{ request('sampai', $sampai) }}" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
            </div>

            <!-- Petani Filter -->
            <div>
                <label for="id_petani" class="block text-sm font-medium text-gray-700 mb-1.5">Petani</label>
                <select id="id_petani" name="id_petani" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    <option value="">Semua petani</option>
                    @foreach($petaniList as $petani)
                        <option value="{{ $petani->id_petani }}" {{ request('id_petani') == $petani->id_petani ? 'selected' : '' }}>
                            {{ $petani->nama_petani }}
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
        </div>

        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.5 5.5a7.5 7.5 0 0010.5 10.5z" />
                </svg>
                Filter
            </button>
            <a href="{{ route('admin.laporan.pengambilan') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Diambil</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalDiambilKg) }} kg</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Jumlah Transaksi</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ $jumlahTransaksi }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Ditolak</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-red-600">{{ $jumlahDitolak }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Periode</p>
        <p class="mt-3 text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($dari)->format('d M') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
    </div>
</div>

<!-- Rekapitulasi per Petani -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Rekapitulasi per Petani</h3>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Diambil</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah Pengambilan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rekapPerPetani as $rekap)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $rekap['petani']->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($rekap['total_diambil_kg']) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $rekap['jumlah_pengambilan'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada data pengambilan
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
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Diambil</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah Permintaan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rekapPerJenis as $namaJenis => $data)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $namaJenis }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($data['total_diambil_kg']) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $data['jumlah_transaksi'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada data pengambilan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Detail Pengambilan -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Detail Pengambilan</h3>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah Diminta</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Alasan</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pengambilanList as $pengambilan)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($pengambilan->tanggal_permintaan)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $pengambilan->petani->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $pengambilan->penyimpananGabah->detailPanen->jenisGabah->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($pengambilan->jumlah_diminta) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $pengambilan->detailPengambilan->first()?->alasan ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($pengambilan->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">Pending</span>
                        @elseif($pengambilan->status === 'disetujui')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">Disetujui</span>
                        @elseif($pengambilan->status === 'selesai')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Selesai</span>
                        @elseif($pengambilan->status === 'ditolak')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700">Ditolak</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada data pengambilan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($pengambilanList->count())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $pengambilanList->firstItem() }}–{{ $pengambilanList->lastItem() }} dari {{ $pengambilanList->total() }} data
        </p>
        {{ $pengambilanList->withQueryString()->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endsection
