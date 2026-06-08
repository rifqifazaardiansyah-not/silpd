@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <span>Dashboard</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Dashboard</h1>
    <p class="text-base text-slate-600 mt-2">Selamat datang, {{ session('nama') }}</p>
</div>

<!-- Stat Cards (4 Kolom) -->
<div class="grid grid-cols-4 gap-6 mb-8">
    <!-- Total Petani -->
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Total Petani</p>
            <span class="p-2 rounded-lg" style="background-color: #0F172A15;">
                <svg class="w-5 h-5" style="color: #0F172A" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </span>
        </div>
        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalPetani, 2, ',', '.') }}</p>
        <p class="mt-1 text-sm text-slate-600">Petani terdaftar</p>
    </div>

    <!-- Total Stok Aktif -->
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Total Stok Aktif</p>
            <span class="p-2 rounded-lg" style="background-color: #22C55E15;">
                <svg class="w-5 h-5" style="color: #22C55E" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
            </span>
        </div>
        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalStokAktif, 2, ',', '.') }}</p>
        <p class="mt-1 text-sm text-slate-600">kg tersimpan</p>
    </div>

    <!-- Panen Bulan Ini -->
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Panen Bulan Ini</p>
            <span class="p-2 rounded-lg" style="background-color: #EAB30815;">
                <svg class="w-5 h-5" style="color: #EAB308" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </span>
        </div>
        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($panenBulanIni, 2, ',', '.') }}</p>
        <p class="mt-1 text-sm text-slate-600">transaksi panen</p>
    </div>

    <!-- Total Lumbung -->
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Total Lumbung</p>
            <span class="p-2 rounded-lg" style="background-color: #0EA5E915;">
                <svg class="w-5 h-5" style="color: #0EA5E9" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                </svg>
            </span>
        </div>
        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalLumbung, 2, ',', '.') }}</p>
        <p class="mt-1 text-sm text-slate-600">lumbung aktif</p>
    </div>
</div>

<!-- Alert Notifikasi -->
@if($slotHampirPenuh->isNotEmpty())
<div class="mb-6 p-4 rounded-lg border-l-4 flex items-start gap-3" style="background-color: #EAB30815; border-left-color: #EAB308; border: 1px solid #EAB30830; border-left-width: 4px;">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #CA8A04" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303-3.376c.866 1.5.866 3.377 0 4.875l-7.553 13.075A2.25 2.25 0 0 1 11.817 21H4.5a2.25 2.25 0 0 1-1.933-3.376l7.553-13.075a2.25 2.25 0 0 1 3.866 0ZM12 15.75h.007v.008H12v-.008Z" />
    </svg>
    <div>
        <p class="text-sm font-semibold" style="color: #CA8A04">{{ number_format($slotHampirPenuh->count(), 2, ',', '.') }} Slot Hampir Penuh</p>
        <p class="text-sm mt-0.5" style="color: #A16207">Beberapa slot lumbung sudah mencapai kapasitas 80% atau lebih.</p>
    </div>
</div>
@endif

@if($gabahKadaluarsa->isNotEmpty())
<div class="mb-6 p-4 rounded-lg border-l-4 flex items-start gap-3" style="background-color: #EF444415; border-left-color: #EF4444; border: 1px solid #EF444430; border-left-width: 4px;">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #DC2626" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5-.866 3.377 0 4.875l7.553 13.075a2.25 2.25 0 0 0 3.866 0l7.553-13.075c.866-1.498.866-3.375 0-4.875l-7.553-4.5a2.25 2.25 0 0 0-3.866 0l-7.553 4.5ZM12 15.75h.007v.008H12v-.008Z" />
    </svg>
    <div>
        <p class="text-sm font-semibold" style="color: #DC2626">{{ number_format($gabahKadaluarsa->count(), 2, ',', '.') }} Lot Gabah Melewati Batas Simpan</p>
        <p class="text-sm mt-0.5" style="color: #991B1B">Ada gabah yang sudah melampaui waktu penyimpanan maksimal. Segera ambil atau proses.</p>
    </div>
</div>
@endif

<!-- Grid 2 Kolom: Permintaan & Instruksi Pending -->
<div class="grid grid-cols-2 gap-6 mb-8">
    <!-- Permintaan Pending -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Permintaan Pengambilan Pending</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Petani</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jenis Gabah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($permintaanPending->take(5) as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $item->petani->nama_petani ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $item->penyimpananGabah->detailPanen->jenisGabah->nama_jenis ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-900">{{ number_format($item->detailPengambilan->sum('jumlah'), 2, ',', '.') }} kg</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $item->tanggal_permintaan->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-600">Tidak ada permintaan pending</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jumlahPermintaanPending > 5)
        <div class="px-6 py-3 border-t border-slate-200 bg-slate-50">
            <a href="{{ route('admin.permintaan.index') }}" class="text-sm font-medium" style="color: #059669">
                Lihat Semua ({{ number_format($jumlahPermintaanPending, 2, ',', '.') }}) →
            </a>
        </div>
        @endif
    </div>

    <!-- Instruksi Pending -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Instruksi Penyimpanan Pending</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Petani</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Slot</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($instruksiPending->take(5) as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $item->detailPanen->panen->petani->nama_petani ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $item->slotLumbung->kode_slot ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-900">{{ number_format($item->jumlah, 2, ',', '.') }} kg</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $item->tanggal_instruksi->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-600">Tidak ada instruksi pending</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jumlahInstruksiPending > 5)
        <div class="px-6 py-3 border-t border-slate-200 bg-slate-50">
            <a href="{{ route('admin.instruksi.index') }}" class="text-sm font-medium" style="color: #059669">
                Lihat Semua ({{ number_format($jumlahInstruksiPending, 2, ',', '.') }}) →
            </a>
        </div>
        @endif
    </div>
</div>


<!-- Stok Per Jenis -->
<div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Stok Per Jenis Gabah</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jenis Gabah</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Total Stok</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Jumlah Lot</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($stokPerJenis as $item)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-slate-900">{{ $item->nama_jenis }}</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ number_format($item->total_stok, 2, ',', '.') }} kg</td>
                    <td class="px-4 py-3 text-sm text-slate-900">{{ number_format($item->jumlah_lot, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-600">Tidak ada stok</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Insight charts moved to the bottom -->
<div class="bg-white rounded-lg border border-slate-200 overflow-hidden mt-8 mb-8">
    <div class="px-6 py-4 border-b border-slate-200">
        <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Ringkasan Kapasitas Lumbung</h3>
    </div>
    <div class="p-6 space-y-6">
        @forelse($ringkasanLumbung as $item)
        <div>
            <div class="flex justify-between items-center mb-2">
                <p class="text-sm font-medium text-slate-900">{{ $item->nama_lumbung }}</p>
                <span class="text-xs text-slate-600">{{ number_format($item->persenTerpakai, 2, ',', '.') }}%</span>
            </div>
            <div class="h-2 rounded-full overflow-hidden" style="background-color: #E2E8F0;">
                <div
                    class="h-full rounded-full transition-all"
                    style="background-color: {{ $item->persenTerpakai >= 80 ? '#EF4444' : ($item->persenTerpakai >= 60 ? '#EAB308' : '#22C55E') }}; width: {{ $item->persenTerpakai }}%"
                ></div>
            </div>
        </div>
        @empty
        <p class="text-sm text-slate-600">Tidak ada data lumbung</p>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-8">
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Tren Panen 6 Bulan Terakhir</h3>
                    <p class="text-xs text-slate-500 mt-1">Perkembangan jumlah panen selama 6 bulan terakhir</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                    Rata-rata {{ number_format($grafikPanen->avg('total_panen'), 1, ',', '.') }} panen/bulan
                </span>
            </div>
        </div>
        <div class="p-6">
            <canvas id="chartPanenTrend" height="180"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Kapasitas Lumbung vs Stok Aktif</h3>
                    <p class="text-xs text-slate-500 mt-1">Komparasi kapasitas total dengan stok yang sedang tersimpan</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                    Terpakai {{ number_format($persenTerpakai, 2, ',', '.') }}%
                </span>
            </div>
        </div>
        <div class="p-6">
            <canvas id="chartKapasitas" height="180"></canvas>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const panenLabels = @json($grafikPanen->pluck('bulan'));
        const panenData = @json($grafikPanen->pluck('total_panen'));

        new Chart(document.getElementById('chartPanenTrend'), {
            type: 'line',
            data: {
                labels: panenLabels,
                datasets: [{
                    label: 'Jumlah Panen',
                    data: panenData,
                    borderColor: '#0F766E',
                    backgroundColor: 'rgba(15, 118, 110, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 5,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#475569' },
                        grid: { color: 'rgba(148, 163, 184, 0.18)' }
                    },
                    x: {
                        ticks: { color: '#475569' },
                        grid: { display: false }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartKapasitas'), {
            type: 'doughnut',
            data: {
                labels: ['Kapasitas Total', 'Stok Aktif'],
                datasets: [{
                    data: [{{ (float) $totalKapasitas }}, {{ (float) $totalStokAktif }}],
                    backgroundColor: ['#0EA5E9', '#10B981'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#475569', usePointStyle: true }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
