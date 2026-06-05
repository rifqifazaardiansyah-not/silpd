@extends('layouts.admin')

@section('title', $pengelola->nama_pengelola)

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.pengelola.index') }}" class="hover:text-gray-900">Data Master</a>
    <span>/</span>
    <a href="{{ route('admin.pengelola.index') }}" class="hover:text-gray-900">Pengelola</a>
    <span>/</span>
    <span>{{ $pengelola->nama_pengelola }}</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $pengelola->nama_pengelola }}</h1>
        <p class="text-sm text-gray-500 mt-1">Detail pengelola dan lumbung yang dikelola</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.pengelola.edit', $pengelola->id_pengelola) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('admin.pengelola.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>

<!-- Info Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Informasi Pengelola</h3>
    </div>
    <div class="p-6 space-y-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</p>
            <p class="text-sm text-gray-900 mt-1">{{ $pengelola->nama_pengelola }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">No HP</p>
            <p class="text-sm text-gray-900 mt-1">{{ $pengelola->no_hp }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Akun Login</p>
            @if($pengelola->login)
            <p class="text-sm text-gray-900 mt-1 font-mono text-[13px]">{{ $pengelola->login->username }}</p>
            <div class="flex gap-2 mt-3">
                <form action="{{ route('admin.pengelola.reset-password', $pengelola->id_pengelola) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-600 text-white text-xs font-medium rounded-lg hover:bg-amber-700 transition-colors"
                            onclick="return confirm('Reset password pengelola ini?')">
                        Reset Password
                    </button>
                </form>
                <form action="{{ route('admin.pengelola.hapus-akun', $pengelola->id_pengelola) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors"
                            onclick="return confirm('Hapus akun pengelola ini?')">
                        Hapus Akun
                    </button>
                </form>
            </div>
            @else
            <p class="text-sm text-gray-500 mt-1">Belum memiliki akun login</p>
            <form action="{{ route('admin.pengelola.buatAkun', $pengelola->id_pengelola) }}" method="POST" class="inline mt-3">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    Buat Akun Login
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<!-- Lumbung yang Dikelola Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Lumbung yang Dikelola</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Nama Lumbung</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Peran</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Jumlah Slot</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kapasitas</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Terpakai %</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pengelola->lumbung as $lumbung)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <a href="{{ route('admin.lumbung.show', $lumbung->id_lumbung) }}" class="text-indigo-600 hover:text-indigo-700">
                            {{ $lumbung->nama_lumbung }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium
                            {{ $lumbung->pivot->peran === 'pemilik_akun' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $lumbung->pivot->peran === 'pemilik_akun' ? 'Pemilik Akun' : 'Anggota' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $lumbung->slotLumbung->count() }} slot</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($lumbung->total_kapasitas, 2, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="w-24">
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all {{ $lumbung->persen_terpakai >= 80 ? 'bg-red-500' : ($lumbung->persen_terpakai >= 60 ? 'bg-amber-400' : 'bg-emerald-500') }}"
                                    style="width: {{ $lumbung->persen_terpakai }}%"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ number_format($lumbung->persen_terpakai, 2, ',', '.') }}%</p>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Tidak mengelola lumbung apapun</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
