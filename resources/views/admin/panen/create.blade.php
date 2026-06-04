@extends('layouts.admin')

@section('title', 'Tambah Panen')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <a href="{{ route('admin.panen.index') }}" class="hover:text-gray-700">Input Panen</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Tambah</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Tambah Panen Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Isi data panen dan alokasi ke lumbung</p>
    </div>
    <a href="{{ route('admin.panen.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali
    </a>
</div>

<!-- Form Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Data Panen</h3>
    </div>

    <form action="{{ route('admin.panen.store') }}" method="POST" class="p-6 space-y-5">
        @csrf

        <!-- Petani -->
        <div>
            <label for="id_petani" class="block text-sm font-medium text-gray-700 mb-1.5">
                Petani <span class="text-red-500">*</span>
            </label>
            <select id="id_petani" name="id_petani" required class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                <option value="">Pilih petani…</option>
                @foreach($petaniList as $petani)
                    <option value="{{ $petani->id_petani }}" {{ old('id_petani') == $petani->id_petani ? 'selected' : '' }}>
                        {{ $petani->nama_petani }} ({{ $petani->kelompokTani->nama_kelompok ?? '-' }})
                    </option>
                @endforeach
            </select>
            @error('id_petani')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tanggal Panen -->
        <div>
            <label for="tanggal_panen" class="block text-sm font-medium text-gray-700 mb-1.5">
                Tanggal Panen <span class="text-red-500">*</span>
            </label>
            <input type="date" id="tanggal_panen" name="tanggal_panen" value="{{ old('tanggal_panen', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
            @error('tanggal_panen')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Detail Panen Section -->
        <div class="pt-4 border-t border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900 tracking-tight mb-4">Detail Panen</h4>

            <div id="detail-container" class="space-y-3">
                <!-- Template baris detail -->
                <div class="detail-row grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Gabah <span class="text-red-500">*</span></label>
                        <select name="detail[0][id_jenis_gabah]" required class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                            <option value="">Pilih jenis…</option>
                            @foreach($jenisGabahList as $jenis)
                                <option value="{{ $jenis->id_jenis_gabah }}">{{ $jenis->nama_jenis }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Panen (kg) <span class="text-red-500">*</span></label>
                        <input type="number" name="detail[0][jumlah_panen]" min="0" step="0.01" required placeholder="0" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors jumlah-input">
                    </div>

                    <div class="text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg">
                        <span class="preview-lumbung">-</span> kg untuk lumbung
                    </div>

                    <button type="button" class="px-3 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors hapus-baris" style="display: none;">
                        Hapus
                    </button>
                </div>
            </div>

            <button type="button" id="tambah-baris" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 text-sm font-medium rounded-lg hover:bg-indigo-100 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Jenis Gabah Lain
            </button>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Simpan Data Panen
            </button>
            <a href="{{ route('admin.panen.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    const persenLumbung = {{ $persenLumbung ?? 3 }};
    let detailIndex = 1;

    function updatePreview(row) {
        const jumlahInput = row.querySelector('.jumlah-input');
        const preview = row.querySelector('.preview-lumbung');
        const jumlah = parseFloat(jumlahInput.value) || 0;
        const lumbung = (jumlah * persenLumbung / 100).toFixed(2);
        preview.textContent = lumbung;
    }

    function setupRow(row) {
        const jumlahInput = row.querySelector('.jumlah-input');
        const hapusBtn = row.querySelector('.hapus-baris');

        jumlahInput.addEventListener('input', () => updatePreview(row));

        hapusBtn.addEventListener('click', () => {
            row.remove();
            updateHapusButtons();
        });
    }

    function updateHapusButtons() {
        const rows = document.querySelectorAll('.detail-row');
        rows.forEach(row => {
            const hapusBtn = row.querySelector('.hapus-baris');
            hapusBtn.style.display = rows.length > 1 ? 'block' : 'none';
        });
    }

    document.getElementById('tambah-baris').addEventListener('click', () => {
        const container = document.getElementById('detail-container');
        const newRow = document.querySelector('.detail-row').cloneNode(true);

        // Update name attributes
        newRow.querySelectorAll('input, select').forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/\[\d+\]/, `[${detailIndex}]`));
            }
            if (el.tagName === 'INPUT') el.value = '';
            if (el.tagName === 'SELECT') el.value = '';
        });

        newRow.querySelector('.preview-lumbung').textContent = '-';
        container.appendChild(newRow);
        setupRow(newRow);
        updateHapusButtons();
        detailIndex++;
    });

    // Setup initial row
    document.querySelectorAll('.detail-row').forEach(row => {
        setupRow(row);
    });
    updateHapusButtons();
</script>
@endsection
