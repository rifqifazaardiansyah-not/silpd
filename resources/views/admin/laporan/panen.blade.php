@extends('layouts.admin')

@section('title', 'Laporan Panen')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Laporan Panen</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Laporan Panen</h1>
        <p class="text-sm text-gray-500 mt-1">Rekapitulasi panen dan alokasi ke lumbung</p>
    </div>
    <a href="{{ route('admin.laporan.ekspor.panen') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        Ekspor CSV
    </a>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.laporan.panen') }}" class="space-y-4">
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

            <!-- Kelompok Filter -->
            <div>
                <label for="id_kelompok" class="block text-sm font-medium text-gray-700 mb-1.5">Kelompok Tani</label>
                <select id="id_kelompok" name="id_kelompok" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    <option value="">Semua kelompok</option>
                    @foreach($kelompokList as $kelompok)
                        <option value="{{ $kelompok->id_kelompok }}" {{ request('id_kelompok') == $kelompok->id_kelompok ? 'selected' : '' }}>
                            {{ $kelompok->nama_kelompok }}
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
            <a href="{{ route('admin.laporan.panen') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Panen</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalPanenKg, 2) }} kg</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Untuk Lumbung</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalLumbungKg, 2) }} kg</p>
        <p class="mt-1 text-xs text-gray-500">{{ $persenLumbung }}% dari total</p>
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
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kelompok</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Panen</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Untuk Lumbung</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rekapPerPetani as $rekap)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $rekap['petani']->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $rekap['petani']->kelompokTani->nama_kelompok ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($rekap['total_panen_kg'], 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($rekap['total_lumbung_kg'], 2) }} kg</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada data panen
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
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Panen</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Untuk Lumbung</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rekapPerJenis as $namaJenis => $data)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $namaJenis }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($data['total_panen'], 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($data['total_lumbung'], 2) }} kg</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada data panen
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Rekapitulasi per Kelompok Tani -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Rekapitulasi per Kelompok Tani</h3>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kelompok Tani</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Panen</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Untuk Lumbung</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah Petani</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rekapPerKelompok as $namaKelompok => $data)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $namaKelompok }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($data['total_panen'], 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($data['total_lumbung'], 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $data['jumlah_petani'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada data panen
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Detail Panen -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Detail Panen</h3>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah Panen</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Untuk Lumbung</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($panenList as $panen)
                @foreach($panen->detailPanen as $detail)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($panen->tanggal_panen)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $panen->petani->nama_petani }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $detail->jenisGabah->nama_jenis }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($detail->jumlah_panen, 2) }} kg</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($detail->jumlah_panen * ($persenLumbung / 100), 2) }} kg</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada data panen
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($panenList->count())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $panenList->firstItem() }}–{{ $panenList->lastItem() }} dari {{ $panenList->total() }} data
        </p>
        {{ $panenList->withQueryString()->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endsection
