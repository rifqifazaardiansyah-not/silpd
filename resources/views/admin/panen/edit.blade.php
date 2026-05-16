@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Edit Data Panen</h2>
            <p class="text-muted">ID Panen: {{ $panen->id_panen }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.panen.show', $panen->id_panen) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informasi Panen</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.panen.update', $panen->id_panen) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Petani -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="id_petani" class="form-label">Petani <span class="text-danger">*</span></label>
                        <select name="id_petani" id="id_petani" class="form-select @error('id_petani') is-invalid @enderror" required>
                            <option value="">-- Pilih Petani --</option>
                            @foreach ($petaniList as $petani)
                                <option value="{{ $petani->id_petani }}" 
                                    {{ old('id_petani', $panen->id_petani) == $petani->id_petani ? 'selected' : '' }}>
                                    {{ $petani->nama_petani }} ({{ $petani->kelompokTani->nama_kelompok ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_petani')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tanggal_panen" class="form-label">Tanggal Panen <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_panen" id="tanggal_panen" 
                               class="form-control @error('tanggal_panen') is-invalid @enderror"
                               value="{{ old('tanggal_panen', $panen->tanggal_panen->format('Y-m-d')) }}" required>
                        @error('tanggal_panen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <!-- Detail Panen -->
                <h5 class="mb-3">Detail Panen per Jenis Gabah</h5>
                <div id="detail-container">
                    @foreach (old('detail', $panen->detailPanen) as $index => $detail)
                    <div class="detail-row card mb-3 p-3" data-index="{{ $index }}">
                        <div class="row">
                            <div class="col-md-5">
                                <label class="form-label">Jenis Gabah <span class="text-danger">*</span></label>
                                <select name="detail[{{ $index }}][id_jenis_gabah]" 
                                        class="form-select jenis-gabah @error('detail.' . $index . '.id_jenis_gabah') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    @foreach ($jenisGabahList as $jenis)
                                        <option value="{{ $jenis->id_jenis_gabah }}"
                                            {{ (old('detail.' . $index . '.id_jenis_gabah') ?? $detail->id_jenis_gabah) == $jenis->id_jenis_gabah ? 'selected' : '' }}>
                                            {{ $jenis->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('detail.' . $index . '.id_jenis_gabah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Jumlah Panen (kg) <span class="text-danger">*</span></label>
                                <input type="number" name="detail[{{ $index }}][jumlah_panen]" 
                                       class="form-control jumlah-panen @error('detail.' . $index . '.jumlah_panen') is-invalid @enderror"
                                       step="0.01" min="0.01"
                                       value="{{ old('detail.' . $index . '.jumlah_panen', $detail->jumlah_panen ?? '') }}" required>
                                @error('detail.' . $index . '.jumlah_panen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Lumbung (3%)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control jumlah-lumbung" readonly>
                                    <span class="input-group-text">kg</span>
                                </div>
                            </div>

                            <div class="col-md-0">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm w-100 delete-detail">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="button" id="add-detail" class="btn btn-outline-primary mt-2">
                    <i class="bi bi-plus-circle"></i> Tambah Jenis Gabah
                </button>

                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i>
                    <strong>Catatan:</strong> Perubahan data panen akan menghasilkan instruksi penyimpanan baru. Instruksi lama yang masih pending akan dihapus.
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.panen.show', $panen->id_panen) }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const persenLumbung = {{ $persenLumbung }};

    // Fungsi hitung lumbung
    function hitungLumbung(row) {
        const jumlahInput = row.querySelector('.jumlah-panen');
        const lumbungInput = row.querySelector('.jumlah-lumbung');
        const jumlah = parseFloat(jumlahInput.value) || 0;
        const lumbung = (jumlah * persenLumbung / 100).toFixed(2);
        lumbungInput.value = lumbung;
    }

    // Tambah detail
    let detailIndex = document.querySelectorAll('.detail-row').length;
    document.getElementById('add-detail').addEventListener('click', function() {
        const container = document.getElementById('detail-container');
        const newRow = document.createElement('div');
        newRow.className = 'detail-row card mb-3 p-3';
        newRow.setAttribute('data-index', detailIndex);
        newRow.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <label class="form-label">Jenis Gabah <span class="text-danger">*</span></label>
                    <select name="detail[${detailIndex}][id_jenis_gabah]" class="form-select jenis-gabah" required>
                        <option value="">-- Pilih Jenis --</option>
                        @foreach ($jenisGabahList as $jenis)
                            <option value="{{ $jenis->id_jenis_gabah }}">{{ $jenis->nama_jenis }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Jumlah Panen (kg) <span class="text-danger">*</span></label>
                    <input type="number" name="detail[${detailIndex}][jumlah_panen]" 
                           class="form-control jumlah-panen" step="0.01" min="0.01" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Lumbung (3%)</label>
                    <div class="input-group">
                        <input type="text" class="form-control jumlah-lumbung" readonly>
                        <span class="input-group-text">kg</span>
                    </div>
                </div>

                <div class="col-md-0">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm w-100 delete-detail">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        detailIndex++;

        // Attach event listeners
        newRow.querySelector('.jumlah-panen').addEventListener('input', function() {
            hitungLumbung(newRow);
        });
        newRow.querySelector('.delete-detail').addEventListener('click', function() {
            newRow.remove();
        });

        hitungLumbung(newRow);
    });

    // Hitung lumbung untuk semua detail yang ada
    document.querySelectorAll('.detail-row').forEach(row => {
        const jumlahInput = row.querySelector('.jumlah-panen');
        if (jumlahInput) {
            jumlahInput.addEventListener('input', function() {
                hitungLumbung(row);
            });
        }

        const deleteBtn = row.querySelector('.delete-detail');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                row.remove();
            });
        }

        // Hitung initial untuk edit page
        hitungLumbung(row);
    });
});
</script>
@endsection
