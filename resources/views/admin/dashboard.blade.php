@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <span>Dashboard</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Dashboard</h1>
    <p class="text-base text-slate-600 mt-2">Selamat datang, {{ session('nama') }}</p>
</div>

<!-- Stat Cards (4 Kolom) -->
<div class="grid grid-cols-4 gap-6 mb-8">
    <!-- Total Petani -->
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Total Petani</p>
            <span class="p-2 rounded-lg" style="background-color: #0F172A15;">
                <svg class="w-5 h-5" style="color: #0F172A" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 001.591-.68m0 0A9.333 9.333 0 0015 19.5m0 0a9.36 9.36 0 01-5.496-1.629A9.334 9.334 0 0015 19.128zm0 0a9.348 9.348 0 00-6-8.972m0 0A9.382 9.382 0 002.25 9m0 0a9.368 9.368 0 015.746 1.629m0 0a9.355 9.355 0 015.004 1.343m0 0A9.325 9.325 0 0015 21M6.75 9a6 6 0 1111.573 1.066A6.002 6.002 0 016.75 9z" />
                </svg>
            </span>
        </div>
        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalPetani, 2, ',', '.') }}</p>
        <p class="mt-1 text-sm text-slate-600">Petani terdaftar</p>
    </div>

    <!-- Total Stok Aktif -->
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Total Stok Aktif</p>
            <span class="p-2 rounded-lg" style="background-color: #22C55E15;">
                <svg class="w-5 h-5" style="color: #22C55E" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.5v2.25m3-7.5v2.25m3-7.5v2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </span>
        </div>
        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalStokAktif, 2, ',', '.') }}</p>
        <p class="mt-1 text-sm text-slate-600">kg tersimpan</p>
    </div>

    <!-- Panen Bulan Ini -->
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Panen Bulan Ini</p>
            <span class="p-2 rounded-lg" style="background-color: #EAB30815;">
                <svg class="w-5 h-5" style="color: #EAB308" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </span>
        </div>
        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($panenBulanIni, 2, ',', '.') }}</p>
        <p class="mt-1 text-sm text-slate-600">transaksi panen</p>
    </div>

    <!-- Total Lumbung -->
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Total Lumbung</p>
            <span class="p-2 rounded-lg" style="background-color: #0EA5E915;">
                <svg class="w-5 h-5" style="color: #0EA5E9" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21V5.25A2.25 2.25 0 0011.25 3h-8.5A2.25 2.25 0 000 5.25v15.75m13.5 0h6A2.25 2.25 0 0021.75 18.75V9M3 12.75h18M3 6.75h18" />
                </svg>
            </span>
        </div>
        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalLumbung, 2, ',', '.') }}</p>
        <p class="mt-1 text-sm text-slate-600">lumbung aktif</p>
    </div>
</div>

<!-- Alert Notifikasi -->
@if($slotHampirPenuh->isNotEmpty())
<div class="mb-6 p-4 rounded-lg border-l-4 flex items-start gap-3" style="background-color: #EAB30815; border-left-color: #EAB308; border: 1px solid #EAB30830; border-left-width: 4px;">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #CA8A04" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c.866-1.5 2.845-2.501 5.303-2.501s4.437 1.001 5.303 2.501M3.75 21h16.5A2.25 2.25 0 0021 18.75V9a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9v9.75A2.25 2.25 0 005.25 21z" />
    </svg>
    <div>
        <p class="text-sm font-semibold" style="color: #CA8A04">{{ number_format($slotHampirPenuh->count(), 2, ',', '.') }} Slot Hampir Penuh</p>
        <p class="text-sm mt-0.5" style="color: #A16207">Beberapa slot lumbung sudah mencapai kapasitas 80% atau lebih.</p>
    </div>
</div>
@endif

@if($gabahKadaluarsa->isNotEmpty())
<div class="mb-6 p-4 rounded-lg border-l-4 flex items-start gap-3" style="background-color: #EF444415; border-left-color: #EF4444; border: 1px solid #EF444430; border-left-width: 4px;">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #DC2626" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303-3.376c-.866-1.5-2.845-2.501-5.303-2.501S13.437 7.5 12.697 9m0 0V21m0-13.5a6 6 0 00-5.303 2.501M3.75 21H21A2.25 2.25 0 0023.25 18.75V9a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 9v9.75A2.25 2.25 0 003.75 21z" />
    </svg>
    <div>
        <p class="text-sm font-semibold" style="color: #DC2626">{{ number_format($gabahKadaluarsa->count(), 2, ',', '.') }} Lot Gabah Melewati Batas Simpan</p>
        <p class="text-sm mt-0.5" style="color: #991B1B">Ada gabah yang sudah melampaui waktu penyimpanan maksimal. Segera ambil atau proses.</p>
    </div>
</div>
@endif

<!-- Grid 2 Kolom: Permintaan & Instruksi Pending -->
<div class="grid grid-cols-2 gap-6 mb-8">
    <!-- Permintaan Pending -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Permintaan Pengambilan Pending</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Petani</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jenis Gabah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($permintaanPending->take(5) as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $item->petani->nama_petani ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $item->penyimpananGabah->detailPanen->jenisGabah->nama_jenis ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-900">{{ number_format($item->detailPengambilan->sum('jumlah'), 2, ',', '.') }} kg</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $item->tanggal_permintaan->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-600">Tidak ada permintaan pending</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jumlahPermintaanPending > 5)
        <div class="px-6 py-3 border-t border-slate-200 bg-slate-50">
            <a href="{{ route('admin.permintaan.index') }}" class="text-sm font-medium" style="color: #059669">
                Lihat Semua ({{ number_format($jumlahPermintaanPending, 2, ',', '.') }}) →
            </a>
        </div>
        @endif
    </div>

    <!-- Instruksi Pending -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Instruksi Penyimpanan Pending</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Petani</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Slot</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($instruksiPending->take(5) as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $item->detailPanen->panen->petani->nama_petani ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $item->slotLumbung->kode_slot ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-900">{{ number_format($item->jumlah, 2, ',', '.') }} kg</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $item->tanggal_instruksi->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-600">Tidak ada instruksi pending</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jumlahInstruksiPending > 5)
        <div class="px-6 py-3 border-t border-slate-200 bg-slate-50">
            <a href="{{ route('admin.instruksi.index') }}" class="text-sm font-medium" style="color: #059669">
                Lihat Semua ({{ number_format($jumlahInstruksiPending, 2, ',', '.') }}) →
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Ringkasan Kapasitas Lumbung -->
<div class="bg-white rounded-lg border border-slate-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-200">
        <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Ringkasan Kapasitas Lumbung</h3>
    </div>
    <div class="p-6 space-y-6">
        @forelse($ringkasanLumbung as $item)
        <div>
            <div class="flex justify-between items-center mb-2">
                <p class="text-sm font-medium text-slate-900">{{ $item->nama_lumbung }}</p>
                <span class="text-xs text-slate-600">{{ number_format($item->persenTerpakai, 2, ',', '.') }}%</span>
            </div>
            <div class="h-2 rounded-full overflow-hidden" style="background-color: #E2E8F0;">
                <div
                    class="h-full rounded-full transition-all"
                    style="background-color: {{ $item->persenTerpakai >= 80 ? '#EF4444' : ($item->persenTerpakai >= 60 ? '#EAB308' : '#22C55E') }}; width: {{ $item->persenTerpakai }}%"
                ></div>
            </div>
        </div>
        @empty
        <p class="text-sm text-slate-600">Tidak ada data lumbung</p>
        @endforelse
    </div>
</div>

<!-- Stok Per Jenis -->
<div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Stok Per Jenis Gabah</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jenis Gabah</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Total Stok</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jumlah Lot</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($stokPerJenis as $item)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-slate-900">{{ $item->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ number_format($item->total_stok, 2, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ number_format($item->jumlah_lot, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-600">Tidak ada stok</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
