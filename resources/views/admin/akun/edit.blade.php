@extends('layouts.admin')

@section('title', 'Edit Akun')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.akun.index') }}" class="hover:text-gray-900">Operasional</a>
    <span>/</span>
    <a href="{{ route('admin.akun.index') }}" class="hover:text-gray-900">Manajemen Akun</a>
    <span>/</span>
    <span>Edit</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Edit Akun</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui username akun</p>
    </div>
    <a href="{{ route('admin.akun.index') }}"
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
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Data Akun</h3>
    </div>

    <form action="{{ route('admin.akun.update', $akun->id_login) }}" method="POST" class="p-6 space-y-5">
        @csrf
        @method('PUT')

        <!-- Role & Pemilik Info (read-only, side by side) -->
        <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Role</p>
                <p class="text-sm text-gray-900 mt-1 font-medium">{{ ucfirst($akun->role) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Pemilik Akun</p>
                <p class="text-sm text-gray-900 mt-1">{{ $akun->nama_pemilik ?? '-' }}</p>
            </div>
        </div>

        <!-- Username -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">
                Username <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username', $akun->username) }}"
                placeholder="Masukkan username…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
            <p class="mt-1.5 text-xs text-gray-500">Huruf, angka, titik, strip, underscore. Minimal 3 karakter.</p>
            @error('username')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Info -->
        <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
            <p class="text-sm text-blue-800">Untuk mengubah password, gunakan fitur <strong>Reset Password</strong> di halaman detail akun.</p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.akun.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection
