@extends('layouts.admin')

@section('title', 'Input Panen')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Input Panen</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Input Panen</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola data panen petani dan alokasi ke lumbung</p>
    </div>
    <a href="{{ route('admin.panen.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Panen
    </a>
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

@if($errors->any())
<div class="p-4 mb-6 bg-red-50 border border-red-200 rounded-lg">
    <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Filter Bar -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.panen.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="cari" class="block text-sm font-medium text-gray-700 mb-1.5">Cari Petani</label>
                <input type="text" id="cari" name="cari" value="{{ request('cari') }}" placeholder="Nama petani…" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
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
            <a href="{{ route('admin.panen.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
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
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal Panen</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kelompok</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Panen</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status Instruksi</th>
                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($panenList as $index => $panen)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $panenList->firstItem() + $index }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($panen->tanggal_panen)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $panen->petani->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $panen->petani->kelompokTani->nama_kelompok ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        @php
                            $jenisGabah = $panen->detailPanen->pluck('jenisGabah.nama_jenis')->unique()->join(', ');
                        @endphp
                        {{ $jenisGabah }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right font-variant-numeric: tabular-nums">
                        {{ number_format($panen->detailPanen->sum('jumlah_panen')) }} kg
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @php
                            $statusList = $panen->detailPanen->map(function($detail) {
                                return $detail->instruksiPenyimpanan->first()?->status ?? 'belum_dibuat';
                            })->unique();
                        @endphp
                        @foreach($statusList as $status)
                            @if($status === 'belum_dibuat')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700">Belum Dibuat</span>
                            @elseif($status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">Pending</span>
                            @elseif($status === 'selesai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Selesai</span>
                            @endif
                        @endforeach
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.panen.show', $panen->id_panen) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Lihat Detail">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.panen.destroy', $panen->id_panen) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors" title="Hapus" onclick="return confirm('Yakin ingin menghapus panen ini?')">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L19.18 2.318a2.25 2.25 0 00-2.163-1.318H5.183a2.25 2.25 0 00-2.163 1.318L2.012 6.54m15.11 0v3.375c0 .621-.504 1.125-1.125 1.125H3.375c-.621 0-1.125-.504-1.125-1.125V6.54" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
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
