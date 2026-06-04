@extends('layouts.admin')

@section('title', 'Tambah Pengelola')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.pengelola.index') }}" class="hover:text-gray-900">Data Master</a>
    <span>/</span>
    <a href="{{ route('admin.pengelola.index') }}" class="hover:text-gray-900">Pengelola</a>
    <span>/</span>
    <span>Tambah</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Tambah Pengelola</h1>
        <p class="text-sm text-gray-500 mt-1">Buat pengelola lumbung baru</p>
    </div>
    <a href="{{ route('admin.pengelola.index') }}"
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
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Data Pengelola</h3>
    </div>

    <form action="{{ route('admin.pengelola.store') }}" method="POST" class="p-6 space-y-5">
        @csrf

        <!-- Nama Pengelola -->
        <div>
            <label for="nama_pengelola" class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Pengelola <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="nama_pengelola"
                name="nama_pengelola"
                value="{{ old('nama_pengelola') }}"
                placeholder="Masukkan nama lengkap…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
            @error('nama_pengelola')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- No HP -->
        <div>
            <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1.5">
                No HP <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="no_hp"
                name="no_hp"
                value="{{ old('no_hp') }}"
                placeholder="08xxx…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
            @error('no_hp')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Divider -->
        <div class="pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-900">Buat Akun Login (Opsional)</h4>
        </div>

        <!-- Checkbox Buat Akun -->
        <div class="flex items-center">
            <input
                type="checkbox"
                id="buat_akun"
                name="buat_akun"
                value="1"
                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                onchange="toggleAkunFields()"
            >
            <label for="buat_akun" class="ml-2 text-sm text-gray-700">
                Sekaligus buat akun login untuk pengelola ini
            </label>
        </div>

        <!-- Akun Fields (Hidden by default) -->
        <div id="akun-fields" class="hidden space-y-5 pt-4 border-t border-gray-200">
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
                >
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Simpan Pengelola
            </button>
            <a href="{{ route('admin.pengelola.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function toggleAkunFields() {
    const checkbox = document.getElementById('buat_akun');
    const fields = document.getElementById('akun-fields');
    const inputs = fields.querySelectorAll('input[name="username"], input[name="password"], input[name="password_confirmation"]');

    if (checkbox.checked) {
        fields.classList.remove('hidden');
        inputs.forEach(input => input.required = true);
    } else {
        fields.classList.add('hidden');
        inputs.forEach(input => {
            input.required = false;
            input.value = '';
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('buat_akun');
    if (checkbox.checked) {
        toggleAkunFields();
    }
});
</script>

@endsection
