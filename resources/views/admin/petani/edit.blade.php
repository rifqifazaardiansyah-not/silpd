@extends('layouts.admin')

@section('title', 'Edit Petani')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.petani.index') }}" class="hover:text-gray-900">Data Master</a>
    <span>/</span>
    <a href="{{ route('admin.petani.index') }}" class="hover:text-gray-900">Petani</a>
    <span>/</span>
    <span>Edit</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Edit Petani</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui data petani</p>
    </div>
    <a href="{{ route('admin.petani.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali
    </a>
</div>

<!-- Flash Messages -->
@if($errors->any())
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
    <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Form Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Data Petani</h3>
    </div>

    <form action="{{ route('admin.petani.update', $petani->id_petani) }}" method="POST" class="p-6 space-y-5">
        @csrf
        @method('PUT')

        <!-- Nama Petani -->
        <div>
            <label for="nama_petani" class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Petani <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="nama_petani"
                name="nama_petani"
                value="{{ old('nama_petani', $petani->nama_petani) }}"
                placeholder="Masukkan nama lengkap…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
            @error('nama_petani')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kelompok Tani -->
        <div>
            <label for="id_kelompok" class="block text-sm font-medium text-gray-700 mb-1.5">
                Kelompok Tani <span class="text-red-500">*</span>
            </label>
            <select
                id="id_kelompok"
                name="id_kelompok"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
                <option value="">Pilih kelompok tani…</option>
                @foreach($kelompokList as $kelompok)
                <option value="{{ $kelompok->id_kelompok }}" {{ old('id_kelompok', $petani->id_kelompok) == $kelompok->id_kelompok ? 'selected' : '' }}>
                    {{ $kelompok->nama_kelompok }}
                </option>
                @endforeach
            </select>
            @error('id_kelompok')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Luas Lahan -->
        <div>
            <label for="luas_lahan" class="block text-sm font-medium text-gray-700 mb-1.5">
                Luas Lahan <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input
                    type="number"
                    id="luas_lahan"
                    name="luas_lahan"
                    value="{{ old('luas_lahan', $petani->luas_lahan) }}"
                    placeholder="0.00"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                    required
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Hektar</span>
            </div>
            @error('luas_lahan')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Info Akun -->
        @if($petani->login)
        <div class="pt-4 border-t border-gray-200">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-900 mb-2">Akun Login</p>
                <p class="text-sm text-gray-600">Username: <span class="font-mono text-[13px]">{{ $petani->login->username }}</span></p>
                <p class="text-xs text-gray-500 mt-2">Untuk mengubah password, gunakan menu Manajemen Akun.</p>
            </div>
        </div>
        @else
        <div class="pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">Petani ini belum memiliki akun login. Buat akun di menu Manajemen Akun.</p>
        </div>
        @endif

        <!-- Buttons -->
        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.petani.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection
