@extends('layouts.admin')

@section('title', $lumbung->nama_lumbung)

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.lumbung.index') }}" class="hover:text-gray-900">Data Master</a>
    <span>/</span>
    <a href="{{ route('admin.lumbung.index') }}" class="hover:text-gray-900">Lumbung</a>
    <span>/</span>
    <span>{{ $lumbung->nama_lumbung }}</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $lumbung->nama_lumbung }}</h1>
        <p class="text-sm text-gray-500 mt-1">Detail lumbung dan slot penyimpanan</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.lumbung.edit', $lumbung->id_lumbung) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('admin.lumbung.index') }}"
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
        <p class="text-sm font-medium text-gray-900">Kapasitas Lumbung</p>
        <span class="text-sm font-semibold text-gray-900">{{ number_format($persenTerpakai, 2, ',', '.') }}%</span>
    </div>
    <div class="h-3 bg-gray-100 rounded-full overflow-hidden mb-3">
        <div
            class="h-full rounded-full transition-all {{ $persenTerpakai >= 80 ? 'bg-red-500' : ($persenTerpakai >= 60 ? 'bg-amber-400' : 'bg-emerald-500') }}"
            style="width: {{ $persenTerpakai }}%"
        ></div>
    </div>
    <div class="grid grid-cols-3 gap-4 text-sm">
        <div>
            <p class="text-gray-500">Total Kapasitas</p>
            <p class="text-lg font-semibold text-gray-900">{{ number_format($totalKapasitas, 2, ',', '.') }} kg</p>
        </div>
        <div>
            <p class="text-gray-500">Tersedia</p>
            <p class="text-lg font-semibold text-emerald-600">{{ number_format($totalTersedia, 2, ',', '.') }} kg</p>
        </div>
        <div>
            <p class="text-gray-500">Terpakai</p>
            <p class="text-lg font-semibold text-amber-600">{{ number_format($totalTerpakai, 2, ',', '.') }} kg</p>
        </div>
    </div>
</div>

<!-- Alerts -->
@if($slotHampirPenuh->isNotEmpty())
<div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3">
    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c.866-1.5 2.845-2.501 5.303-2.501s4.437 1.001 5.303 2.501M3.75 21h16.5A2.25 2.25 0 0021 18.75V9a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9v9.75A2.25 2.25 0 005.25 21z" />
    </svg>
    <div>
        <p class="text-sm font-medium text-amber-800">{{ $slotHampirPenuh->count() }} Slot Hampir Penuh</p>
        <p class="text-sm text-amber-700 mt-0.5">Beberapa slot sudah mencapai kapasitas 80% atau lebih.</p>
    </div>
</div>
@endif

@if($gabahKadaluarsa->isNotEmpty())
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303-3.376c-.866-1.5-2.845-2.501-5.303-2.501S13.437 7.5 12.697 9m0 0V21m0-13.5a6 6 0 00-5.303 2.501M3.75 21H21A2.25 2.25 0 0023.25 18.75V9a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 9v9.75A2.25 2.25 0 003.75 21z" />
    </svg>
    <div>
        <p class="text-sm font-medium text-red-800">{{ $gabahKadaluarsa->count() }} Lot Gabah Kadaluarsa</p>
        <p class="text-sm text-red-700 mt-0.5">Ada gabah yang melewati batas waktu penyimpanan maksimal.</p>
    </div>
</div>
@endif

<!-- Pengelola Section -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Pengelola Lumbung</h3>
    </div>
    <div class="p-6 space-y-3">
        @forelse($lumbung->pengelola as $pengelola)
        <div class="flex items-center justify-between py-2">
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $pengelola->nama_pengelola }}</p>
                <p class="text-xs text-gray-500">{{ $pengelola->no_hp }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium
                {{ $pengelola->pivot->peran === 'pemilik_akun' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $pengelola->pivot->peran === 'pemilik_akun' ? 'Pemilik Akun' : 'Anggota' }}
            </span>
        </div>
        @empty
        <p class="text-sm text-gray-500">Tidak ada pengelola</p>
        @endforelse
    </div>
</div>

<!-- Slot Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Daftar Slot</h3>
        <a href="{{ route('admin.lumbung.slot.create', $lumbung->id_lumbung) }}"
           class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Slot
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kode Slot</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kapasitas</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tersedia</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Terpakai</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($lumbung->slotLumbung as $slot)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900 font-mono">{{ $slot->kode_slot }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($slot->kapasitas, 2, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($slot->kapasitas_tersedia, 2, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($slot->kapasitas - $slot->kapasitas_tersedia, 2, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm">
                        @php
                            $terpakai = $slot->kapasitas - $slot->kapasitas_tersedia;
                            $persenSlot = $slot->kapasitas > 0 ? round(($terpakai / $slot->kapasitas) * 100, 2) : 0;
                        @endphp
                        <div class="w-24">
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all {{ $persenSlot >= 80 ? 'bg-red-500' : ($persenSlot >= 60 ? 'bg-amber-400' : 'bg-emerald-500') }}"
                                    style="width: {{ $persenSlot }}%"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ number_format($persenSlot, 2, ',', '.') }}%</p>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.lumbung.slot.show', [$lumbung->id_lumbung, $slot->id_slot]) }}"
                               class="p-1.5 text-gray-600 hover:text-indigo-600 transition-colors"
                               title="Lihat detail">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            <a href="{{ route('admin.lumbung.slot.edit', [$lumbung->id_lumbung, $slot->id_slot]) }}"
                               class="p-1.5 text-gray-600 hover:text-amber-600 transition-colors"
                               title="Edit">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.lumbung.slot.destroy', [$lumbung->id_lumbung, $slot->id_slot]) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus slot ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-600 hover:text-red-600 transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada slot</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Stok Gabah Table (FIFO) -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Stok Gabah dalam Lumbung (FIFO)</h3>
        <p class="text-xs text-gray-500 mt-1">Urutan berdasarkan tanggal masuk (tertua dahulu)</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Slot</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Umur</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stokList as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <a href="{{ route('admin.petani.show', $item->detailPanen->panen->petani->id_petani) }}" class="text-indigo-600 hover:text-indigo-700">
                            {{ $item->detailPanen->panen->petani->nama_petani ?? '-' }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->detailPanen->jenisGabah->nama_jenis ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 font-mono">{{ $item->slotLumbung->kode_slot ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ number_format($item->jumlah, 2, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal_masuk)->diffInDays(now()) }} hari</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada stok</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
