@extends('layouts.admin')

@section('title', $kelompok->nama_kelompok)

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.kelompok.index') }}" class="hover:text-gray-900">Data Master</a>
    <span>/</span>
    <a href="{{ route('admin.kelompok.index') }}" class="hover:text-gray-900">Kelompok Tani</a>
    <span>/</span>
    <span>{{ $kelompok->nama_kelompok }}</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $kelompok->nama_kelompok }}</h1>
        <p class="text-sm text-gray-500 mt-1">Detail kelompok dan anggota</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.kelompok.edit', $kelompok->id_kelompok) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('admin.kelompok.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 gap-6 mb-8">
    <!-- Jumlah Anggota -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Jumlah Anggota</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ $kelompok->petani_count ?? 0 }}</p>
        <p class="mt-1 text-xs text-gray-500">Petani terdaftar</p>
    </div>

    <!-- Total Stok Kelompok -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Stok Gabah</p>
        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalStokKelompok, 0, ',', '.') }}</p>
        <p class="mt-1 text-xs text-gray-500">kg tersimpan</p>
    </div>
</div>

<!-- Tabel Anggota -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Daftar Anggota</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">No</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Nama Petani</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Luas Lahan</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status Akun</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($anggota as $index => $petani)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $anggota->firstItem() + $index }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <a href="{{ route('admin.petani.show', $petani->id_petani) }}" class="text-indigo-600 hover:text-indigo-700">
                            {{ $petani->nama_petani }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($petani->luas_lahan, 2, ',', '.') }} ha</td>
                    <td class="px-4 py-3 text-sm">
                        @if($petani->login)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">
                            Punya Akun
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600">
                            Belum Ada Akun
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada anggota</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($anggota->count() > 0)
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $anggota->firstItem() }}–{{ $anggota->lastItem() }} dari {{ $anggota->total() }} data
        </p>
        {{ $anggota->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>

@endsection
