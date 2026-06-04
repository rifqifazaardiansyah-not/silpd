@extends('layouts.admin')

@section('title', 'Ganti Password')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <span>Operasional</span>
    <span>/</span>
    <span>Ganti Password</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Ganti Password</h1>
        <p class="text-sm text-gray-500 mt-1">Ubah password akun Anda</p>
    </div>
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

<!-- Form Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden max-w-2xl">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Ubah Password</h3>
    </div>

    <form action="{{ route('admin.akun.ganti-password.post') }}" method="POST" class="p-6 space-y-5">
        @csrf

        <!-- Password Lama -->
        <div>
            <label for="password_lama" class="block text-sm font-medium text-gray-700 mb-1.5">
                Password Lama <span class="text-red-500">*</span>
            </label>
            <input
                type="password"
                id="password_lama"
                name="password_lama"
                placeholder="Masukkan password lama…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
            @error('password_lama')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Baru -->
        <div>
            <label for="password_baru" class="block text-sm font-medium text-gray-700 mb-1.5">
                Password Baru <span class="text-red-500">*</span>
            </label>
            <input
                type="password"
                id="password_baru"
                name="password_baru"
                placeholder="Masukkan password baru…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
            <p class="mt-1.5 text-xs text-gray-500">Minimal 6 karakter.</p>
            @error('password_baru')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Konfirmasi Password Baru -->
        <div>
            <label for="password_baru_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                Konfirmasi Password Baru <span class="text-red-500">*</span>
            </label>
            <input
                type="password"
                id="password_baru_confirmation"
                name="password_baru_confirmation"
                placeholder="Ulangi password baru…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Simpan Password Baru
            </button>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection
