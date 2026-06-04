@extends('layouts.petani')

@section('title', 'Dashboard Petani')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <span class="text-gray-900 font-medium">Dashboard</span>
</nav>
@endsection

@section('content')
<!-- Welcome Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Selamat datang, {{ session('nama') }}</h1>
    <p class="text-base text-slate-600 mt-2">Kelola stok gabah dan permintaan pengambilan Anda</p>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Total Gabah Tersimpan</p>
        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalGabahTersimpan, 2) }} kg</p>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Permintaan Aktif</p>
        <p class="mt-3 text-3xl font-bold tracking-tight" style="color: #059669">{{ $permintaanAktif }}</p>
    </div>
</div>

<!-- Profil Card -->
<div class="bg-white rounded-lg border border-slate-200 p-6 mb-8">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Profil Petani</p>
            <p class="text-lg font-bold text-slate-900">{{ $petani->nama_petani }}</p>
            <p class="text-sm text-slate-600 mt-1">{{ $petani->kelompokTani->nama_kelompok ?? '-' }}</p>
        </div>
        <a href="{{ route('petani.stok.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background-color: #05966915; color: #059669">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Lihat Stok
        </a>
    </div>
</div>

<!-- Stok per Jenis Gabah -->
<div class="bg-white rounded-lg border border-slate-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-200">
        <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Stok per Jenis Gabah</h3>
    </div>

    <div class="p-6">
        @forelse($stokPerJenis as $jenis)
            <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-b-0">
                <div>
                    <p class="text-sm font-medium text-slate-900">{{ $jenis->nama_jenis }}</p>
                    <p class="text-xs text-slate-600 mt-0.5">{{ $jenis->jumlah_lot }} lot tersimpan</p>
                </div>
                <p class="text-sm font-semibold text-slate-900">{{ number_format($jenis->total_stok, 2) }} kg</p>
            </div>
        @empty
            <p class="text-sm text-slate-600">Tidak ada stok gabah</p>
        @endforelse
    </div>
</div>

<!-- Permintaan Terbaru -->
<div class="bg-white rounded-lg border border-slate-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Permintaan Terbaru</h3>
        <a href="{{ route('petani.permintaan.index') }}" class="text-xs font-medium" style="color: #059669">Lihat Semua →</a>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-slate-200 bg-slate-50">
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Jumlah</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($permintaanTerbaru as $permintaan)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-slate-900">{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ $permintaan->penyimpananGabah->detailPanen->jenisGabah->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900 text-right">{{ number_format($permintaan->detailPengambilan->sum('jumlah'), 2) }} kg</td>
                    <td class="px-4 py-3 text-sm">
                        @if($permintaan->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: #EAB30815; color: #CA8A04">Pending</span>
                        @elseif($permintaan->status === 'disetujui')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: #0EA5E915; color: #0284C7">Disetujui</span>
                        @elseif($permintaan->status === 'selesai')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: #22C55E15; color: #16A34A">Selesai</span>
                        @elseif($permintaan->status === 'ditolak')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: #EF444415; color: #DC2626">Ditolak</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-600">
                        Tidak ada permintaan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Panen Terbaru -->
<div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Panen Terbaru</h3>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-slate-200 bg-slate-50">
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($panenTerbaru as $panen)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-slate-900">{{ \Carbon\Carbon::parse($panen->tanggal_panen)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900">
                        @php
                            $jenisGabah = $panen->detailPanen->pluck('jenisGabah.nama_jenis')->unique()->join(', ');
                        @endphp
                        {{ $jenisGabah }}
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-900 text-right">{{ number_format($panen->detailPanen->sum('jumlah_panen'), 2) }} kg</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-600">
                        Tidak ada panen
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
