@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Detail Panen</h2>
            <p class="text-muted">ID: {{ $panen->id_panen }} | Tanggal: {{ $panen->tanggal_panen->format('d M Y') }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.panen.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            @if (!$ringkasanDetail->some(fn($r) => $r['penyimpanan']) && !$ringkasanDetail->some(fn($r) => $r['instruksi']?->status === 'selesai'))
            <a href="{{ route('admin.panen.edit', $panen->id_panen) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form action="{{ route('admin.panen.destroy', $panen->id_panen) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus data panen ini?')">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </form>
            @endif
        </div>
    </div>

    @if ($errors->has('edit') || $errors->has('hapus'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        @if ($errors->has('edit'))
            {{ $errors->first('edit') }}
        @elseif ($errors->has('hapus'))
            {{ $errors->first('hapus') }}
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('warning_list'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Peringatan:</strong>
        <ul class="mb-0">
            @foreach (session('warning_list') as $warning)
                <li>{{ $warning }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Header Panen -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informasi Panen</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Petani:</strong><br>
                    {{ $panen->petani->nama_petani }}<br>
                    <small class="text-muted">{{ $panen->petani->kelompokTani->nama_kelompok ?? '-' }}</small>
                </div>
                <div class="col-md-4">
                    <strong>Tanggal Panen:</strong><br>
                    {{ $panen->tanggal_panen->format('d M Y') }}
                </div>
                <div class="col-md-4">
                    <strong>Total Panen:</strong><br>
                    {{ number_format($totalPanen, 2) }} kg
                    <br>
                    <strong style="color: #28a745;">Untuk Lumbung ({{ $persenLumbung }}%):</strong>
                    {{ number_format($totalLumbung, 2) }} kg
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Per Jenis Gabah -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Detail Panen per Jenis Gabah</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Jenis Gabah</th>
                        <th>Jumlah Panen (kg)</th>
                        <th>Untuk Lumbung (kg)</th>
                        <th>Status Instruksi</th>
                        <th>Slot Penyimpanan</th>
                        <th>Status Penyimpanan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ringkasanDetail as $ring)
                    <tr>
                        <td>
                            <strong>{{ $ring['detail']->jenisGabah->nama_jenis }}</strong>
                        </td>
                        <td>{{ number_format($ring['detail']->jumlah_panen, 2) }}</td>
                        <td>{{ number_format($ring['jumlah_lumbung'], 2) }}</td>
                        <td>
                            @if ($ring['instruksi'])
                                @if ($ring['instruksi']->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif ($ring['instruksi']->status === 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Belum ada</span>
                            @endif
                        </td>
                        <td>
                            @if ($ring['instruksi'])
                                {{ $ring['instruksi']->slotLumbung->kode_slot ?? '-' }}<br>
                                <small class="text-muted">({{ $ring['instruksi']->slotLumbung->lumbung->nama_lumbung ?? '-' }})</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($ring['penyimpanan'])
                                <span class="badge bg-info">{{ ucfirst($ring['penyimpanan']->status) }}</span>
                            @else
                                <span class="badge bg-light text-dark">Belum</span>
                            @endif
                        </td>
                        <td>
                            @if ($ring['ada_instruksi'] && !$ring['sudah_disimpan'])
                                <form action="{{ route('admin.panen.batal-instruksi', $ring['instruksi']->id_instruksi) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Batalkan instruksi ini?')">
                                        Batalkan
                                    </button>
                                </form>
                            @elseif (!$ring['ada_instruksi'])
                                <a href="{{ route('admin.panen.form-instruksi', ['id' => $panen->id_panen, 'idDetail' => $ring['detail']->id_detail]) }}" class="btn btn-sm btn-outline-primary">
                                    Buat Instruksi
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Tidak ada detail panen
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
