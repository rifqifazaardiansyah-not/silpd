@extends('layouts.admin')

@section('title', 'Buat Akun')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.akun.index') }}" class="hover:text-gray-900">Operasional</a>
    <span>/</span>
    <a href="{{ route('admin.akun.index') }}" class="hover:text-gray-900">Manajemen Akun</a>
    <span>/</span>
    <span>Buat Akun</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Buat Akun Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Buat akun login untuk pengguna</p>
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

    <form action="{{ route('admin.akun.store') }}" method="POST" class="p-6 space-y-5">
        @csrf

        <!-- Role Tabs -->
        <div class="border-b border-gray-200 pb-4">
            <p class="text-sm font-medium text-gray-900 mb-3">Pilih Role</p>
            <div class="flex gap-3">
                <label class="flex items-center gap-2">
                    <input type="radio" name="role" value="petani" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                           {{ request('role') === 'petani' || old('role') === 'petani' ? 'checked' : '' }} onchange="updateEntitas()">
                    <span class="text-sm text-gray-700">Petani</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="role" value="pengelola" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                           {{ request('role') === 'pengelola' || old('role') === 'pengelola' ? 'checked' : '' }} onchange="updateEntitas()">
                    <span class="text-sm text-gray-700">Pengelola</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="role" value="admin" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                           {{ request('role') === 'admin' || old('role') === 'admin' ? 'checked' : '' }} onchange="updateEntitas()">
                    <span class="text-sm text-gray-700">Admin</span>
                </label>
            </div>
        </div>

        <!-- Entitas Tersedia -->
        <div>
            <label for="ref_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                Pilih Entitas <span class="text-red-500">*</span>
            </label>
            <select
                id="ref_id"
                name="ref_id"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
                <option value="">Pilih entitas…</option>
                @foreach($entitasTersedia as $entitas)
                <option value="{{ $entitas->id }}" {{ old('ref_id') == $entitas->id ? 'selected' : '' }}>
                    {{ $entitas->nama }}
                </option>
                @endforeach
            </select>
            @error('ref_id')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
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
                value="{{ old('username') }}"
                placeholder="Masukkan username…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
            <p class="mt-1.5 text-xs text-gray-500">Huruf, angka, titik, strip, underscore. Minimal 3 karakter.</p>
            @error('username')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                Password <span class="text-red-500">*</span>
            </label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
            <p class="mt-1.5 text-xs text-gray-500">Minimal 6 karakter.</p>
            @error('password')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Konfirmasi Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                Konfirmasi Password <span class="text-red-500">*</span>
            </label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Ulangi password…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Buat Akun
            </button>
            <a href="{{ route('admin.akun.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function updateEntitas() {
    // This would typically fetch entitas based on selected role via AJAX
    // For now, it's a placeholder for future enhancement
}
</script>

@endsection
