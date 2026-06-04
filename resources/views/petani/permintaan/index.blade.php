@extends('layouts.petani')

@section('title', 'Permintaan Pengambilan')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('petani.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Permintaan Pengambilan</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Permintaan Pengambilan</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola permintaan pengambilan gabah Anda</p>
    </div>
    <a href="{{ route('petani.permintaan.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Ajukan Permintaan Pengambilan
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

@if(session('warning'))
<div class="flex items-start gap-3 p-4 mb-6 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
    </svg>
    <span>{{ session('warning') }}</span>
</div>
@endif

@if(session('info'))
<div class="flex items-start gap-3 p-4 mb-6 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 text-sm">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
    </svg>
    <span>{{ session('info') }}</span>
</div>
@endif

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">No</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jenis Gabah</th>
                <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah Diminta</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Alasan</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($permintaanList as $index => $permintaan)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($permintaan->tanggal_permintaan)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $permintaan->penyimpananGabah->detailPanen->jenisGabah->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($permintaan->detailPengambilan->first()?->jumlah ?? 0, 2) }} kg</td>
                    <td class="px-4 py-3 text-sm text-gray-900 truncate max-w-xs">{{ $permintaan->detailPengambilan->first()?->alasan ?? '-' }}</td>
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
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('petani.permintaan.show', $permintaan->id_permintaan) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Lihat Detail">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            @if($permintaan->status === 'pending')
                                <form action="{{ route('petani.permintaan.batal', $permintaan->id_permintaan) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors" title="Batalkan" onclick="return confirm('Yakin ingin membatalkan permintaan ini?')">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                        Tidak ada permintaan pengambilan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
