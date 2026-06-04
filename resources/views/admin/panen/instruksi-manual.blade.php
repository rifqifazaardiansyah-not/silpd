@extends('layouts.admin')

@section('title', 'Buat Instruksi Manual')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <a href="{{ route('admin.panen.index') }}" class="hover:text-gray-700">Input Panen</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Buat Instruksi</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Buat Instruksi Penyimpanan</h1>
        <p class="text-sm text-gray-500 mt-1">Pilih slot lumbung untuk menyimpan gabah</p>
    </div>
    <a href="{{ route('admin.panen.show', $detailPanen->panen->id_panen) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali
    </a>
</div>

<!-- Info Card -->
<div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
    <div class="flex gap-3">
        <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25.75 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <div class="text-sm text-indigo-800">
            <p class="font-medium">{{ $detailPanen->jenisGabah->nama_jenis }} — {{ number_format($jumlahLumbung) }} kg perlu ditempatkan</p>
            <p class="text-indigo-700 mt-1">Petani: {{ $detailPanen->panen->petani->nama_petani }}</p>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Pilih Slot Lumbung</h3>
    </div>

    <form action="{{ route('admin.panen.instruksi-manual.post', $detailPanen->id_detail) }}" method="POST" class="p-6">
        @csrf

        <div class="space-y-3">
            @forelse($slotTersedia as $slot)
                <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                    <input type="radio" name="id_slot" value="{{ $slot->id_slot }}" required class="mt-1" {{ old('id_slot') == $slot->id_slot ? 'checked' : '' }}>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $slot->lumbung->nama_lumbung }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Kode: {{ $slot->kode_slot }}</p>
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Kapasitas Tersedia</span>
                                <span>{{ number_format($slot->kapasitas_tersedia, 2) }} kg</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                @php
                                    $persenTerpakai = $slot->kapasitas_total > 0 
                                        ? (($slot->kapasitas_total - $slot->kapasitas_tersedia) / $slot->kapasitas_total * 100)
                                        : 0;
                                    $barColor = $persenTerpakai >= 80 ? 'bg-red-500' : ($persenTerpakai >= 60 ? 'bg-amber-400' : 'bg-emerald-500');
                                @endphp
                                <div class="h-full {{ $barColor }} rounded-full" style="width: {{ $persenTerpakai }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ number_format($persenTerpakai, 1) }}% terpakai</p>
                        </div>
                    </div>
                </label>
            @empty
                <div class="p-6 text-center text-gray-500">
                    <p class="text-sm">Tidak ada slot lumbung yang tersedia</p>
                </div>
            @endforelse
        </div>

        @error('id_slot_lumbung')
            <p class="mt-3 text-xs text-red-600">{{ $message }}</p>
        @enderror

        <!-- Buttons -->
        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Buat Instruksi
            </button>
            <a href="{{ route('admin.panen.show', $detailPanen->panen->id_panen) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
