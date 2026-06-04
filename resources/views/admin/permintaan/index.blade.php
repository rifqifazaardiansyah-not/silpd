@extends('layouts.admin')

@section('title', 'Permintaan Ambil')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Permintaan Ambil</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Permintaan Pengambilan</h1>
        <p class="text-sm text-gray-500 mt-1">Monitor permintaan pengambilan gabah dari petani</p>
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
        <a href="{{ route('admin.permintaan.index') }}" class="flex-1 px-6 py-3 text-sm font-medium text-center {{ !request('status') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
            Semua <span class="ml-2 text-xs text-gray-500">({{ $jumlahPerStatus['total'] ?? 0 }})</span>
        </a>
        <a href="{{ route('admin.permintaan.index', ['status' => 'pending']) }}" class="flex-1 px-6 py-3 text-sm font-medium text-center {{ request('status') === 'pending' ? 'text-amber-600 border-b-2 border-amber-600' : 'text-gray-600 hover:text-gray-900' }}">
            Pending <span class="ml-2 text-xs text-gray-500">({{ $jumlahPerStatus['pending'] ?? 0 }})</span>
        </a>
        <a href="{{ route('admin.permintaan.index', ['status' => 'disetujui']) }}" class="flex-1 px-6 py-3 text-sm font-medium text-center {{ request('status') === 'disetujui' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
            Disetujui <span class="ml-2 text-xs text-gray-500">({{ $jumlahPerStatus['disetujui'] ?? 0 }})</span>
        </a>
        <a href="{{ route('admin.permintaan.index', ['status' => 'selesai']) }}" class="flex-1 px-6 py-3 text-sm font-medium text-center {{ request('status') === 'selesai' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-600 hover:text-gray-900' }}">
            Selesai <span class="ml-2 text-xs text-gray-500">({{ $jumlahPerStatus['selesai'] ?? 0 }})</span>
        </a>
        <a href="{{ route('admin.permintaan.index', ['status' => 'ditolak']) }}" class="flex-1 px-6 py-3 text-sm font-medium text-center {{ request('status') === 'ditolak' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900' }}">
            Ditolak <span class="ml-2 text-xs text-gray-500">({{ $jumlahPerStatus['ditolak'] ?? 0 }})</span>
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.permintaan.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

            <!-- Date Range -->
            <div>
                <label for="dari" class="block text-sm font-medium text-gray-700 mb-1.5">Dari Tanggal</label>
                <input type="date" id="dari" name="dari" value="{{ request('dari') }}" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
            </div>

            <div>
                <label for="sampai" class="block text-sm font-medium text-gray-700 mb-1.5">Sampai Tanggal</label>
                <input type="date" id="sampai" name="sampai" value="{{ request('sampai') }}" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.5 5.5a7.5 7.5 0 0010.5 10.5z" />
                </svg>
                Filter
            </button>
            <a href="{{ route('admin.permintaan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>
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
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah Diminta</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Lumbung/Slot</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
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
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($permintaan->jumlah_diminta) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        {{ $permintaan->penyimpananGabah->slotLumbung->lumbung->nama_lumbung }} / {{ $permintaan->penyimpananGabah->slotLumbung->kode_slot }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($permintaan->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">Pending</span>
                        @elseif($permintaan->status === 'disetujui')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">Disetujui</span>
                        @elseif($permintaan->status === 'selesai')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Selesai</span>
                        @elseif($permintaan->status === 'ditolak')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700">Ditolak</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center justify-center">
                            <a href="{{ route('admin.permintaan.show', $permintaan->id_permintaan) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Lihat Detail">
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
                        Tidak ada permintaan pengambilan
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
