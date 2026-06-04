@extends('layouts.pengelola')

@section('title', 'Pengeluaran Gabah')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('pengelola.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Pengeluaran Gabah</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Pengeluaran Gabah</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola pengeluaran gabah dari lumbung</p>
    </div>
</div>

<!-- Flash Messages -->
@if(session('success'))
<div class="flex items-start gap-3 p-4 mb-6 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Status Tabs -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
    <div class="flex border-b border-gray-200">
        <a href="{{ route('pengelola.pengeluaran.index', ['status' => 'disetujui']) }}" class="flex-1 px-6 py-3 text-sm font-medium text-center {{ request('status') === 'disetujui' || !request('status') ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-600 hover:text-gray-900' }}">
            Menunggu Pengeluaran <span class="ml-2 text-xs text-gray-500">({{ $jumlahMenunggu }})</span>
        </a>
        <a href="{{ route('pengelola.pengeluaran.index', ['status' => 'selesai']) }}" class="flex-1 px-6 py-3 text-sm font-medium text-center {{ request('status') === 'selesai' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-600 hover:text-gray-900' }}">
            Selesai <span class="ml-2 text-xs text-gray-500">({{ $jumlahSelesai }})</span>
        </a>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">No</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah Akan Dikeluarkan</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Slot Asal</th>
                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($permintaanList as $index => $permintaan)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $permintaanList->firstItem() + $index }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $permintaan->petani->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $permintaan->penyimpananGabah->detailPanen->jenisGabah->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($permintaan->detailPengambilan->sum('jumlah'), 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $permintaan->penyimpananGabah->slotLumbung->lumbung->nama_lumbung }} / {{ $permintaan->penyimpananGabah->slotLumbung->kode_slot }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center justify-center">
                            <a href="{{ route('pengelola.pengeluaran.show', $permintaan->id_permintaan) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Lihat Detail">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada pengeluaran gabah
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($permintaanList->count())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $permintaanList->firstItem() }}–{{ $permintaanList->lastItem() }} dari {{ $permintaanList->total() }} data
        </p>
        {{ $permintaanList->withQueryString()->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endsection
