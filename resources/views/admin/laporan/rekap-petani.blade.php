@extends('layouts.admin')

@section('title', 'Laporan Rekap Petani')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Laporan Rekap Petani</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Laporan Rekap Petani</h1>
        <p class="text-sm text-gray-500 mt-1">Kontribusi panen dan pengambilan gabah per petani</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.laporan.rekap-petani') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Kelompok Filter -->
            <div>
                <label for="id_kelompok" class="block text-sm font-medium text-gray-700 mb-1.5">Filter Kelompok Tani</label>
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
            <a href="{{ route('admin.laporan.rekap-petani') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Summary Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Petani</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ count($petaniList) }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Panen</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($petaniList->sum('total_panen_kg'), 2) }} kg</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Stok Aktif Sekarang</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($petaniList->sum('stok_aktif_kg'), 2) }} kg</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Diambil</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($petaniList->sum('total_diambil_kg'), 2) }} kg</p>
    </div>
</div>

<!-- Tabel Rekap Petani -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Rekap Detail per Petani</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">No</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Nama Petani</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kelompok</th>
                    <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Panen</th>
                    <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Untuk Lumbung</th>
                    <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Stok Aktif</th>
                    <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Sudah Diambil</th>
                    <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Sisa Bersih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($petaniList as $index => $row)
                    @php
                        $petani = $row['petani'];
                        $sisaBersih = $row['total_masuk_lumbung_kg'] - $row['total_diambil_kg'];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <a href="{{ route('admin.petani.show', $petani->id_petani) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                {{ $petani->nama_petani }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $petani->kelompokTani->nama_kelompok ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">
                            {{ number_format($row['total_panen_kg'], 2) }} kg
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">
                            {{ number_format($row['total_masuk_lumbung_kg'], 2) }} kg
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(34, 197, 94, 0.15); color: #22C55E;">
                                {{ number_format($row['stok_aktif_kg'], 2) }} kg
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">
                            {{ number_format($row['total_diambil_kg'], 2) }} kg
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-medium">
                            @if($sisaBersih >= 0)
                                <span style="color: #22C55E;">{{ number_format($sisaBersih, 2) }} kg</span>
                            @else
                                <span style="color: #EF4444;">{{ number_format($sisaBersih, 2) }} kg</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                            Tidak ada data petani
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-sm font-semibold text-gray-900">TOTAL</td>
                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ number_format($petaniList->sum('total_panen_kg'), 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ number_format($petaniList->sum('total_masuk_lumbung_kg'), 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ number_format($petaniList->sum('stok_aktif_kg'), 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ number_format($petaniList->sum('total_diambil_kg'), 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                        {{ number_format($petaniList->sum('total_masuk_lumbung_kg') - $petaniList->sum('total_diambil_kg'), 2) }} kg
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Legend -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <p class="text-sm text-blue-900">
        <strong>Catatan:</strong>
        <br>• <strong>Total Panen:</strong> Total seluruh gabah yang dipanen oleh petani
        <br>• <strong>Untuk Lumbung:</strong> Jumlah yang dialokasikan untuk lumbung (sesuai persentase kebijakan)
        <br>• <strong>Stok Aktif:</strong> Jumlah gabah yang saat ini masih tersimpan di lumbung
        <br>• <strong>Sudah Diambil:</strong> Jumlah gabah yang telah diambil kembali oleh petani
        <br>• <strong>Sisa Bersih:</strong> Gabah yang masih tersimpan atau belum diambil (Stok Aktif atau perhitungan sisa)
    </p>
</div>
@endsection
