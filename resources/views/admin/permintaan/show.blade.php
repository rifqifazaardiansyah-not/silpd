@extends('layouts.admin')

@section('title', 'Detail Permintaan')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <a href="{{ route('admin.permintaan.index') }}" class="hover:text-gray-700">Permintaan Ambil</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Detail</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Permintaan #{{ $permintaan->id_permintaan }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->format('d M Y') }} • {{ $permintaan->petani->nama_petani }}</p>
    </div>
    <a href="{{ route('admin.permintaan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali
    </a>
</div>

<!-- Status Badge -->
<div class="mb-6">
    @if($permintaan->status === 'pending')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-50 text-amber-700">Pending</span>
    @elseif($permintaan->status === 'disetujui')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-50 text-indigo-700">Disetujui</span>
    @elseif($permintaan->status === 'selesai')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700">Selesai</span>
    @elseif($permintaan->status === 'ditolak')
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-50 text-red-700">Ditolak</span>
    @endif
</div>

<!-- FIFO Warning -->
@if($adaPelanggaranFifo)
<div class="p-4 mb-6 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3">
    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c.866 1.5 2.926 2.871 5.303 2.871s4.437-1.372 5.303-2.871m0 0a3.75 3.75 0 11-7.5 0m7.5 0a3.75 3.75 0 1-7.5 0" />
    </svg>
    <div>
        <p class="text-sm font-medium text-amber-800">Rekomendasi FIFO</p>
        <p class="text-sm text-amber-700 mt-1">Ada gabah jenis yang sama yang lebih lama tersimpan. Disarankan ambil dari lot yang lebih lama terlebih dahulu.</p>
        @if($rekomendasiFifo)
        <ul class="mt-2 space-y-1 text-sm text-amber-700">
            @foreach($rekomendasiFifo as $rekomendasi)
                <li>• {{ $rekomendasi->slotLumbung->lumbung->nama_lumbung }} / {{ $rekomendasi->slotLumbung->kode_slot }} ({{ \Carbon\Carbon::parse($rekomendasi->tanggal_masuk)->format('d M Y') }})</li>
            @endforeach
        </ul>
        @endif
    </div>
</div>
@endif

<!-- Detail Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Informasi Permintaan</h3>
    </div>

    <div class="p-6 space-y-6">
        <!-- Petani Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Petani</p>
                <p class="text-sm text-gray-900">{{ $permintaan->petani->nama_petani }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Kelompok Tani</p>
                <p class="text-sm text-gray-900">{{ $permintaan->petani->kelompokTani->nama_kelompok ?? '-' }}</p>
            </div>
        </div>

        <!-- Gabah Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Jenis Gabah</p>
                <p class="text-sm text-gray-900">{{ $permintaan->penyimpananGabah->detailPanen->jenisGabah->nama_jenis }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Tanggal Masuk Gabah</p>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($permintaan->penyimpananGabah->tanggal_masuk)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Slot Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Lumbung</p>
                <p class="text-sm text-gray-900">{{ $permintaan->penyimpananGabah->slotLumbung->lumbung->nama_lumbung }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Slot</p>
                <p class="text-sm text-gray-900">{{ $permintaan->penyimpananGabah->slotLumbung->kode_slot }}</p>
            </div>
        </div>

        <!-- Jumlah dan Alasan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Jumlah Diminta</p>
                <p class="text-sm text-gray-900 font-semibold">{{ number_format($permintaan->jumlah_diminta) }} kg</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Alasan Pengambilan</p>
                <p class="text-sm text-gray-900">{{ $permintaan->detailPengambilan->first()?->alasan ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Action Forms -->
@if($permintaan->status === 'pending')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Setujui Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Setujui Permintaan</h3>
        </div>
        <form action="{{ route('admin.permintaan.setujui', $permintaan->id_permintaan) }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label for="catatan_setujui" class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (Opsional)</label>
                <textarea id="catatan_setujui" name="catatan_admin" rows="3" placeholder="Catatan persetujuan…" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"></textarea>
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Setujui
            </button>
        </form>
    </div>

    <!-- Tolak Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Tolak Permintaan</h3>
        </div>
        <form action="{{ route('admin.permintaan.tolak', $permintaan->id_permintaan) }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label for="alasan_tolak" class="block text-sm font-medium text-gray-700 mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea id="alasan_tolak" name="alasan_tolak" rows="3" required placeholder="Jelaskan alasan penolakan…" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"></textarea>
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors" onclick="return confirm('Yakin ingin menolak permintaan ini?')">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Tolak
            </button>
        </form>
    </div>
</div>
@elseif($permintaan->status === 'disetujui')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Batal Setujui Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Batal Persetujuan</h3>
        </div>
        <form action="{{ route('admin.permintaan.batal-setujui', $permintaan->id_permintaan) }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label for="alasan_batal" class="block text-sm font-medium text-gray-700 mb-1.5">Alasan <span class="text-red-500">*</span></label>
                <textarea id="alasan_batal" name="alasan_batal" rows="3" required placeholder="Jelaskan alasan pembatalan…" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-colors"></textarea>
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors" onclick="return confirm('Yakin ingin membatalkan persetujuan?')">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Batal Setujui
            </button>
        </form>
    </div>

    <!-- Tolak Paksa Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Tolak Setelah Disetujui</h3>
        </div>
        <form action="{{ route('admin.permintaan.tolak-setelah-disetujui', $permintaan->id_permintaan) }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label for="alasan_tolak_paksa" class="block text-sm font-medium text-gray-700 mb-1.5">Alasan <span class="text-red-500">*</span></label>
                <textarea id="alasan_tolak_paksa" name="alasan_tolak" rows="3" required placeholder="Jelaskan alasan penolakan…" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"></textarea>
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors" onclick="return confirm('Yakin ingin menolak permintaan yang sudah disetujui?')">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Tolak
            </button>
        </form>
    </div>
</div>
@endif

<!-- Riwayat Permintaan -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Riwayat Permintaan Petani (5 Terakhir)</h3>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($riwayatPermintaan as $riwayat)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($riwayat->tanggal_permintaan)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $riwayat->penyimpananGabah->detailPanen->jenisGabah->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($riwayat->jumlah_diminta) }} kg</td>
                    <td class="px-4 py-3 text-sm">
                        @if($riwayat->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">Pending</span>
                        @elseif($riwayat->status === 'disetujui')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700">Disetujui</span>
                        @elseif($riwayat->status === 'selesai')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Selesai</span>
                        @elseif($riwayat->status === 'ditolak')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700">Ditolak</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada riwayat permintaan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
