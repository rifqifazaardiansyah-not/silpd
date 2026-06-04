@extends('layouts.pengelola')

@section('title', 'Dashboard Pengelola')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <span class="text-gray-900 font-medium">Dashboard</span>
</nav>
@endsection

@section('content')
<!-- Welcome Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Selamat datang, {{ session('nama') }}</h1>
    <p class="text-base text-slate-600 mt-2">Kelola instruksi penyimpanan dan pengeluaran gabah</p>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Instruksi Pending</p>
        <p class="mt-3 text-3xl font-bold tracking-tight" style="color: #EAB308">{{ $jumlahInstruksiPending }}</p>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Pengeluaran Pending</p>
        <p class="mt-3 text-3xl font-bold tracking-tight" style="color: #EAB308">{{ $jumlahPermintaanDisetujui }}</p>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Slot Hampir Penuh</p>
        <p class="mt-3 text-3xl font-bold tracking-tight" style="color: #EAB308">{{ $slotHampirPenuh->count() }}</p>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Lot Kadaluarsa</p>
        <p class="mt-3 text-3xl font-bold tracking-tight" style="color: #EF4444">{{ $gabahKadaluarsa->count() }}</p>
    </div>
</div>

<!-- Alerts -->
@if($slotHampirPenuh->count() > 0)
<div class="p-4 mb-8 rounded-lg border-l-4 flex items-start gap-3" style="background-color: #EAB30815; border-left-color: #EAB308; border: 1px solid #EAB30830; border-left-width: 4px;">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #CA8A04" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c.866 1.5 2.926 2.871 5.303 2.871s4.437-1.372 5.303-2.871m0 0a3.75 3.75 0 11-7.5 0m7.5 0a3.75 3.75 0 1-7.5 0" />
    </svg>
    <div>
        <p class="text-sm font-semibold" style="color: #CA8A04">Perhatian: Slot Hampir Penuh</p>
        <p class="text-sm mt-1" style="color: #A16207">{{ $slotHampirPenuh->count() }} slot lumbung sudah mencapai 80% kapasitas. Segera lakukan pengeluaran gabah.</p>
    </div>
</div>
@endif

@if($gabahKadaluarsa->count() > 0)
<div class="p-4 mb-8 rounded-lg border-l-4 flex items-start gap-3" style="background-color: #EF444415; border-left-color: #EF4444; border: 1px solid #EF444430; border-left-width: 4px;">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #DC2626" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c.866 1.5 2.926 2.871 5.303 2.871s4.437-1.372 5.303-2.871m0 0a3.75 3.75 0 11-7.5 0m7.5 0a3.75 3.75 0 1-7.5 0" />
    </svg>
    <div>
        <p class="text-sm font-semibold" style="color: #DC2626">Peringatan: Gabah Kadaluarsa</p>
        <p class="text-sm mt-1" style="color: #991B1B">{{ $gabahKadaluarsa->count() }} lot gabah sudah melampaui batas waktu penyimpanan. Segera lakukan pengeluaran.</p>
    </div>
</div>
@endif

<!-- Instruksi Pending -->
<div class="bg-white rounded-lg border border-slate-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Instruksi Penyimpanan Pending</h3>
        <a href="{{ route('pengelola.instruksi.index') }}" class="text-xs font-medium" style="color: #059669">Lihat Semua →</a>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-slate-200 bg-slate-50">
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Petani</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Jumlah</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Slot Tujuan</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal</th>
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-700">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($instruksiPending as $instruksi)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-slate-900">{{ $instruksi->detailPanen->panen->petani->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ $instruksi->detailPanen->jenisGabah->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900 text-right">{{ number_format($instruksi->jumlah, 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ $instruksi->slotLumbung->lumbung->nama_lumbung }} / {{ $instruksi->slotLumbung->kode_slot }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ \Carbon\Carbon::parse($instruksi->created_at)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center justify-center">
                            <a href="{{ route('pengelola.instruksi.show', $instruksi->id_instruksi) }}" class="p-1.5 rounded-lg transition-colors" style="color: #059669; background-color: #05966915">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-600">
                        Tidak ada instruksi pending
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pengeluaran Pending -->
<div class="bg-white rounded-lg border border-slate-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Pengeluaran Gabah Pending</h3>
        <a href="{{ route('pengelola.pengeluaran.index') }}" class="text-xs font-medium" style="color: #059669">Lihat Semua →</a>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-slate-200 bg-slate-50">
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Petani</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Jumlah</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Slot Asal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal</th>
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-700">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($permintaanDisetujui as $permintaan)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-slate-900">{{ $permintaan->petani->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ $permintaan->penyimpananGabah->detailPanen->jenisGabah->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900 text-right">{{ number_format($permintaan->detailPengambilan->sum('jumlah'), 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ $permintaan->penyimpananGabah->slotLumbung->lumbung->nama_lumbung }} / {{ $permintaan->penyimpananGabah->slotLumbung->kode_slot }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center justify-center">
                            <a href="{{ route('pengelola.pengeluaran.show', $permintaan->id_permintaan) }}" class="p-1.5 rounded-lg transition-colors" style="color: #059669; background-color: #05966915">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-600">
                        Tidak ada pengeluaran pending
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Ringkasan Kapasitas Lumbung -->
<div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Ringkasan Kapasitas Lumbung</h3>
    </div>

    <div class="p-6 space-y-4">
        @forelse($ringkasanLumbung as $lumbung)
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-slate-900">{{ $lumbung->nama_lumbung }}</p>
                    <span class="text-xs text-slate-600">{{ number_format($lumbung->total_stok) }} / {{ number_format($lumbung->kapasitas_total) }} kg</span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background-color: #E2E8F0;">
                    @php
                        $persenTerpakai = ($lumbung->total_stok / $lumbung->kapasitas_total) * 100;
                        $barColor = $persenTerpakai >= 80 ? '#EF4444' : ($persenTerpakai >= 60 ? '#EAB308' : '#22C55E');
                    @endphp
                    <div class="h-full rounded-full" style="background-color: {{ $barColor }}; width: {{ $persenTerpakai }}%"></div>
                </div>
                <p class="text-xs text-slate-600 mt-1">{{ number_format($persenTerpakai, 1) }}% terpakai</p>
            </div>
        @empty
            <p class="text-sm text-slate-600">Tidak ada data lumbung</p>
        @endforelse
    </div>
</div>
@endsection
