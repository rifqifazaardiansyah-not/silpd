@extends('layouts.admin')

@section('title', $petani->nama_petani)

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.petani.index') }}" class="hover:text-gray-900">Data Master</a>
    <span>/</span>
    <a href="{{ route('admin.petani.index') }}" class="hover:text-gray-900">Petani</a>
    <span>/</span>
    <span>{{ $petani->nama_petani }}</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $petani->nama_petani }}</h1>
        <p class="text-sm text-gray-500 mt-1">Detail profil dan stok gabah</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.petani.edit', $petani->id_petani) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('admin.petani.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>

<!-- Grid 2 Kolom: Profil & Akun -->
<div class="grid grid-cols-2 gap-6 mb-8">
    <!-- Profil Petani -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Profil Petani</h3>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</p>
                <p class="text-sm text-gray-900 mt-1">{{ $petani->nama_petani }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Kelompok Tani</p>
                <p class="text-sm text-gray-900 mt-1">{{ $petani->kelompokTani->nama_kelompok ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Luas Lahan</p>
                <p class="text-sm text-gray-900 mt-1">{{ number_format($petani->luas_lahan, 2, ',', '.') }} Hektar</p>
            </div>
        </div>
    </div>

    <!-- Info Akun -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Akun Login</h3>
        </div>
        <div class="p-6 space-y-4">
            @if($petani->login)
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Username</p>
                <p class="text-sm text-gray-900 mt-1 font-mono text-[13px]">{{ $petani->login->username }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 mt-1">
                    Akun Aktif
                </span>
            </div>
            @else
            <div class="text-center py-4">
                <p class="text-sm text-gray-600">Belum memiliki akun login</p>
                <p class="text-xs text-gray-500 mt-1">Buat akun di menu Manajemen Akun</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Total Stok Aktif Stat -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Stok Aktif</p>
    <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($totalStok, 0, ',', '.') }} kg</p>
    <p class="mt-1 text-xs text-gray-500">Gabah tersimpan di lumbung</p>
</div>

<!-- Stok Aktif Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Stok Gabah Aktif (FIFO)</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Slot</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Lumbung</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Umur</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stokAktif as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->jenisGabah->nama_jenis ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->slot->nama_slot ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->slot->lumbung->nama_lumbung ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($item->jumlah_gabah, 0, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $item->tanggal_masuk->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $item->tanggal_masuk->diffInDays(now()) }} hari</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada stok aktif</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Riwayat Panen Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Riwayat Panen</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Panen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($riwayatPanen as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $item->tanggal_panen->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        {{ $item->detailPanen->pluck('jenisGabah.nama_jenis')->unique()->join(', ') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($item->detailPanen->sum('jumlah_panen'), 0, ',', '.') }} kg</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada riwayat panen</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($riwayatPanen->count() > 0)
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $riwayatPanen->firstItem() }}–{{ $riwayatPanen->lastItem() }} dari {{ $riwayatPanen->total() }} data
        </p>
        {{ $riwayatPanen->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>

@endsection
