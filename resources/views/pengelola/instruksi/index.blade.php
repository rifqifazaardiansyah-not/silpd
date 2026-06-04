@extends('layouts.pengelola')

@section('title', 'Instruksi Penyimpanan')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('pengelola.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Instruksi Penyimpanan</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Instruksi Penyimpanan</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola instruksi penyimpanan gabah dari admin</p>
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
        <a href="{{ route('pengelola.instruksi.index', ['status' => 'semua']) }}" class="flex-1 px-6 py-3 text-sm font-medium text-center {{ $statusFilter === 'semua' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-600 hover:text-gray-900' }}">
            Semua <span class="ml-2 text-xs text-gray-500">({{ $jumlahTotal }})</span>
        </a>
        <a href="{{ route('pengelola.instruksi.index', ['status' => 'pending']) }}" class="flex-1 px-6 py-3 text-sm font-medium text-center {{ $statusFilter === 'pending' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-600 hover:text-gray-900' }}">
            Pending <span class="ml-2 text-xs text-gray-500">({{ $jumlahPending }})</span>
        </a>
        <a href="{{ route('pengelola.instruksi.index', ['status' => 'selesai']) }}" class="flex-1 px-6 py-3 text-sm font-medium text-center {{ $statusFilter === 'selesai' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-600 hover:text-gray-900' }}">
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
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Slot Tujuan</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($instruksiList as $index => $instruksi)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $instruksiList->firstItem() + $index }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($instruksi->created_at)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $instruksi->detailPanen->panen->petani->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $instruksi->detailPanen->jenisGabah->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($instruksi->jumlah, 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $instruksi->slotLumbung->lumbung->nama_lumbung }} / {{ $instruksi->slotLumbung->kode_slot }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($instruksi->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">Pending</span>
                        @elseif($instruksi->status === 'selesai')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Selesai</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center justify-center">
                            <a href="{{ route('pengelola.instruksi.show', $instruksi->id_instruksi) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Lihat Detail">
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
                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada instruksi penyimpanan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($instruksiList->count())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $instruksiList->firstItem() }}–{{ $instruksiList->lastItem() }} dari {{ $instruksiList->total() }} data
        </p>
        {{ $instruksiList->withQueryString()->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endsection
