@extends('layouts.admin')

@section('title', 'Edit Kelompok Tani')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.kelompok.index') }}" class="hover:text-gray-900">Data Master</a>
    <span>/</span>
    <a href="{{ route('admin.kelompok.index') }}" class="hover:text-gray-900">Kelompok Tani</a>
    <span>/</span>
    <span>Edit</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Edit Kelompok Tani</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui data kelompok tani</p>
    </div>
    <a href="{{ route('admin.kelompok.index') }}"
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
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden max-w-2xl">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Data Kelompok Tani</h3>
    </div>

    <form action="{{ route('admin.kelompok.update', $kelompok->id_kelompok) }}" method="POST" class="p-6 space-y-5">
        @csrf
        @method('PUT')

        <!-- Nama Kelompok -->
        <div>
            <label for="nama_kelompok" class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Kelompok <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="nama_kelompok"
                name="nama_kelompok"
                value="{{ old('nama_kelompok', $kelompok->nama_kelompok) }}"
                placeholder="Masukkan nama kelompok tani…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
            @error('nama_kelompok')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.kelompok.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection
