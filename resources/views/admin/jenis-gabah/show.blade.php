@extends('layouts.admin')

@section('title', $jenisGabah->nama_jenis)

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.jenis-gabah.index') }}" class="hover:text-gray-900">Data Master</a>
    <span>/</span>
    <a href="{{ route('admin.jenis-gabah.index') }}" class="hover:text-gray-900">Jenis Gabah</a>
    <span>/</span>
    <span>{{ $jenisGabah->nama_jenis }}</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $jenisGabah->nama_jenis }}</h1>
        <p class="text-sm text-gray-500 mt-1">Detail stok dan distribusi</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.jenis-gabah.edit', $jenisGabah->id_jenis_gabah) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('admin.jenis-gabah.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>

<!-- Total Stok Stat -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Stok Tersimpan</p>
    <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalStok, 0, ',', '.') }} kg</p>
    <p class="mt-1 text-xs text-gray-500">Gabah jenis ini di semua lumbung</p>
</div>

<!-- Stok Per Lumbung Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Stok Per Lumbung</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Lumbung</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Slot</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stokPerLumbung as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->slot->lumbung->nama_lumbung ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->slot->nama_slot ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($item->jumlah_gabah, 0, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $item->tanggal_masuk->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada stok</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Petani dengan Stok Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Petani dengan Stok Jenis Ini</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">No</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Nama Petani</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kelompok Tani</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Stok</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($petaniDenganStok as $index => $petani)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <a href="{{ route('admin.petani.show', $petani->id_petani) }}" class="text-indigo-600 hover:text-indigo-700">
                            {{ $petani->nama_petani }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $petani->kelompokTani->nama_kelompok ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($petani->total_stok, 0, ',', '.') }} kg</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada petani dengan stok jenis ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
