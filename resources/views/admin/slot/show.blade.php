@extends('layouts.admin')

@section('title', 'Detail Slot - ' . $slot->nama_slot)

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.lumbung.index') }}" class="hover:text-gray-900">Data Master</a>
    <span>/</span>
    <a href="{{ route('admin.lumbung.index') }}" class="hover:text-gray-900">Lumbung</a>
    <span>/</span>
    <a href="{{ route('admin.lumbung.show', $lumbung->id_lumbung) }}" class="hover:text-gray-900">{{ $lumbung->nama_lumbung }}</a>
    <span>/</span>
    <span>{{ $slot->nama_slot }}</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Slot {{ $slot->nama_slot }}</h1>
        <p class="text-sm text-gray-500 mt-1">Detail kapasitas dan stok gabah</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.lumbung.slot.edit', [$lumbung->id_lumbung, $slot->id_slot]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('admin.lumbung.show', $lumbung->id_lumbung) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>

<!-- Capacity Bar -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
    <div class="flex justify-between items-center mb-3">
        <p class="text-sm font-medium text-gray-900">Kapasitas Slot</p>
        <span class="text-sm font-semibold text-gray-900">{{ $persenTerpakai }}%</span>
    </div>
    <div class="h-3 bg-gray-100 rounded-full overflow-hidden mb-3">
        <div
            class="h-full rounded-full transition-all {{ $persenTerpakai >= 80 ? 'bg-red-500' : ($persenTerpakai >= 60 ? 'bg-amber-400' : 'bg-emerald-500') }}"
            style="width: {{ $persenTerpakai }}%"
        ></div>
    </div>
    <div class="grid grid-cols-3 gap-4 text-sm">
        <div>
            <p class="text-gray-500">Kapasitas Total</p>
            <p class="text-lg font-semibold text-gray-900">{{ number_format($slot->kapasitas, 0, ',', '.') }} kg</p>
        </div>
        <div>
            <p class="text-gray-500">Tersedia</p>
            <p class="text-lg font-semibold text-emerald-600">{{ number_format($slot->kapasitas - $terpakai, 0, ',', '.') }} kg</p>
        </div>
        <div>
            <p class="text-gray-500">Terpakai</p>
            <p class="text-lg font-semibold text-amber-600">{{ number_format($terpakai, 0, ',', '.') }} kg</p>
        </div>
    </div>
</div>

<!-- Gabah Tersimpan Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Gabah Tersimpan (FIFO)</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Umur Simpan</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($gabahTersimpan as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->petani->nama_petani ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->jenisGabah->nama_jenis ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($item->jumlah, 0, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $item->tanggal_masuk->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $item->umur_hari }} hari</td>
                    <td class="px-4 py-3 text-sm">
                        @if($item->is_kadaluarsa)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700">
                            Kadaluarsa
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">
                            Tersimpan
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Slot kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Riwayat Penyimpanan Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Riwayat Penyimpanan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($riwayatPenyimpanan as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->petani->nama_petani ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->jenisGabah->nama_jenis ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($item->jumlah, 0, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $item->tanggal_masuk->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600">
                            Selesai
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada riwayat</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($riwayatPenyimpanan->count() > 0)
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $riwayatPenyimpanan->firstItem() }}–{{ $riwayatPenyimpanan->lastItem() }} dari {{ $riwayatPenyimpanan->total() }} data
        </p>
        {{ $riwayatPenyimpanan->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>

@endsection
