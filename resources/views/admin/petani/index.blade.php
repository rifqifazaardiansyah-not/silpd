@extends('layouts.admin')

@section('title', 'Daftar Petani')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <span>Data Master</span>
    <span>/</span>
    <span>Petani</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Daftar Petani</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola data petani dan akun login mereka</p>
    </div>
    <a href="{{ route('admin.petani.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Petani
    </a>
</div>

<!-- Flash Messages -->
@if(session('success'))
<div class="mb-6 flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
    <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Filter Bar -->
<div class="mb-6 bg-white rounded-xl border border-gray-200 shadow-sm p-4">
    <form method="GET" action="{{ route('admin.petani.index') }}" class="flex gap-3 items-end">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1.5">Cari Petani</label>
            <input
                type="text"
                id="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Nama petani…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
            >
        </div>
        <div class="w-48">
            <label for="kelompok" class="block text-sm font-medium text-gray-700 mb-1.5">Kelompok Tani</label>
            <select
                id="kelompok"
                name="id_kelompok"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
            >
                <option value="">Semua Kelompok</option>
                @foreach($kelompokList as $kelompok)
                <option value="{{ $kelompok->id_kelompok }}" {{ request('id_kelompok') == $kelompok->id_kelompok ? 'selected' : '' }}>
                    {{ $kelompok->nama_kelompok }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            Filter
        </button>
        <a href="{{ route('admin.petani.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            Reset
        </a>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">No</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Nama Petani</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Kelompok Tani</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Luas Lahan</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status Akun</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($petaniList as $index => $petani)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $petaniList->firstItem() + $index }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $petani->nama_petani }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $petani->kelompokTani->nama_kelompok ?? '-' }}</td>
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
                    <td class="px-4 py-3 text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.petani.show', $petani->id_petani) }}"
                               class="p-1.5 text-gray-600 hover:text-indigo-600 transition-colors"
                               title="Lihat detail">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            <a href="{{ route('admin.petani.edit', $petani->id_petani) }}"
                               class="p-1.5 text-gray-600 hover:text-amber-600 transition-colors"
                               title="Edit">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.petani.destroy', $petani->id_petani) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus petani ini? Data terkait akan dihapus juga.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-600 hover:text-red-600 transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 2.991a2.25 2.25 0 00-2.163-1.491h-6.798c-.97 0-1.8.54-2.163 1.491L2.012 5.5m15.848 1.676c-.356.067-.71.142-1.056.217m-1.056-.217l-.5 6.5a2.25 2.25 0 01-2.25 2.25H5.25a2.25 2.25 0 01-2.25-2.25l-.5-6.5m15.848 0A2.25 2.25 0 0021 7.773v10.706a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18.48V7.771a2.25 2.25 0 012.25-2.25m15.848 0c-.356.067-.71.142-1.056.217M3.12 5.973c.346.075.7.13 1.056.217" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">Tidak ada data petani</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($petaniList->count() > 0)
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $petaniList->firstItem() }}–{{ $petaniList->lastItem() }} dari {{ $petaniList->total() }} data
        </p>
        {{ $petaniList->withQueryString()->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>

@endsection
