@extends('layouts.admin')

@section('title', 'Tambah Lumbung')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.lumbung.index') }}" class="hover:text-gray-900">Data Master</a>
    <span>/</span>
    <a href="{{ route('admin.lumbung.index') }}" class="hover:text-gray-900">Lumbung</a>
    <span>/</span>
    <span>Tambah</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Tambah Lumbung</h1>
        <p class="text-sm text-gray-500 mt-1">Buat lumbung baru dan tentukan pengelolanya</p>
    </div>
    <a href="{{ route('admin.lumbung.index') }}"
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
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Data Lumbung</h3>
    </div>

    <form action="{{ route('admin.lumbung.store') }}" method="POST" class="p-6 space-y-6">
        @csrf

        <!-- Nama Lumbung -->
        <div>
            <label for="nama_lumbung" class="block text-sm font-medium text-gray-700 mb-1.5">
                Nama Lumbung <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="nama_lumbung"
                name="nama_lumbung"
                value="{{ old('nama_lumbung') }}"
                placeholder="Masukkan nama lumbung…"
                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                required
            >
            @error('nama_lumbung')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pengelola Section -->
        <div class="pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Pengelola Lumbung</h4>
            <p class="text-xs text-gray-500 mb-4">Minimal satu pengelola dengan peran Pemilik Akun disarankan</p>

            <div class="space-y-3">
                @forelse($pengelolaList as $pengelola)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            id="pengelola_{{ $pengelola->id_pengelola }}"
                            name="pengelola[{{ $pengelola->id_pengelola }}][checked]"
                            value="1"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1"
                            onchange="togglePeranOptions({{ $pengelola->id_pengelola }})"
                        >
                        <div class="flex-1">
                            <label for="pengelola_{{ $pengelola->id_pengelola }}" class="text-sm font-medium text-gray-900">
                                {{ $pengelola->nama_pengelola }}
                            </label>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $pengelola->no_hp }}</p>
                        </div>
                    </div>

                    <!-- Peran Options (Hidden by default) -->
                    <div id="peran-options-{{ $pengelola->id_pengelola }}" class="hidden mt-3 ml-7 space-y-2">
                        <label class="flex items-center gap-2">
                            <input
                                type="radio"
                                name="pengelola[{{ $pengelola->id_pengelola }}][peran]"
                                value="pemilik_akun"
                                class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                            >
                            <span class="text-sm text-gray-700">Pemilik Akun</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input
                                type="radio"
                                name="pengelola[{{ $pengelola->id_pengelola }}][peran]"
                                value="anggota"
                                class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                            >
                            <span class="text-sm text-gray-700">Anggota</span>
                        </label>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">Tidak ada pengelola tersedia</p>
                @endforelse
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Simpan Lumbung
            </button>
            <a href="{{ route('admin.lumbung.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function togglePeranOptions(pengelolaId) {
    const checkbox = document.getElementById(`pengelola_${pengelolaId}`);
    const options = document.getElementById(`peran-options-${pengelolaId}`);
    const radios = options.querySelectorAll('input[type="radio"]');

    if (checkbox.checked) {
        options.classList.remove('hidden');
        radios[0].checked = true; // Default to pemilik_akun
    } else {
        options.classList.add('hidden');
        radios.forEach(r => r.checked = false);
    }
}
</script>

@endsection
