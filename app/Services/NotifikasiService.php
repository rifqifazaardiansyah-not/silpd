<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Service untuk sistem notifikasi dalam SILPD
 * Mengelola notifikasi otomatis untuk admin, pengelola, dan petani
 * berdasarkan kondisi sistem (FIFO, kapasitas, pengambilan, dll)
 */
class NotifikasiService
{
    /**
     * Tipe-tipe notifikasi dalam sistem
     */
    public const TIPE_NOTIFIKASI = [
        'FIFO_PRIORITY' => 'fifo_priority',           // Gabah terlama perlu diambil
        'LUMBUNG_PENUH' => 'lumbung_penuh',           // Lumbung hampir penuh
        'SLOT_PENUH' => 'slot_penuh',                 // Slot hampir penuh
        'GABAH_EXPIRED' => 'gabah_expired',           // Gabah melampaui durasi maksimal
        'GABAH_WARNING' => 'gabah_warning',           // Gabah mendekati durasi maksimal
        'PANEN_BARU' => 'panen_baru',                 // Ada panen baru yang sudah diinput
        'INSTRUKSI_SIMPAN' => 'instruksi_simpan',     // Instruksi penyimpanan baru
        'PERMINTAAN_PENGAMBILAN' => 'permintaan_pengambilan', // Ada permintaan pengambilan
        'PENYIMPANAN_SELESAI' => 'penyimpanan_selesai', // Penyimpanan sudah selesai
        'PENGAMBILAN_SELESAI' => 'pengambilan_selesai', // Pengambilan sudah selesai
        'KAPASITAS_RENDAH' => 'kapasitas_rendah',     // Kapasitas lumbung rendah
    ];

    /**
     * Priority level notifikasi
     */
    public const PRIORITY_LEVEL = [
        'LOW' => 'low',
        'MEDIUM' => 'medium',
        'HIGH' => 'high',
        'CRITICAL' => 'critical',
    ];

    /**
     * Generate notifikasi FIFO - Gabah terlama perlu diambil
     *
     * @param array|\Illuminate\Database\Eloquent\Model $penyimpanan - Data penyimpanan gabah
     * @param FifoService $fifoService - Instance FifoService
     * @return array - Data notifikasi
     *
     * @example
     * $notif = $service->buatNotifikasiFifo($penyimpanan, $fifoService);
     */
    public function buatNotifikasiFifo(array|\Illuminate\Database\Eloquent\Model $penyimpanan, FifoService $fifoService): array
    {
        $umurHari = $fifoService->hitungUmurSimpan($penyimpanan);
        $status = $fifoService->kategorikanStatusUmur($umurHari);

        $priority = match($status) {
            'kritis' => self::PRIORITY_LEVEL['CRITICAL'],
            'warning' => self::PRIORITY_LEVEL['HIGH'],
            default => self::PRIORITY_LEVEL['MEDIUM'],
        };

        return [
            'tipe' => self::TIPE_NOTIFIKASI['FIFO_PRIORITY'],
            'judul' => "Prioritas Pengambilan Gabah FIFO",
            'deskripsi' => "Gabah jenis {$this->getJenisGabah($penyimpanan)} di slot {$this->getSlot($penyimpanan)} " .
                          "telah disimpan selama {$umurHari} hari. Status: " . strtoupper($status) . ". " .
                          "Pertimbangkan untuk diambil agar kualitas tetap terjaga.",
            'priority' => $priority,
            'target_role' => ['pengelola', 'admin'],
            'related_id' => $penyimpanan['id_penyimpanan'] ?? ($penyimpanan->id_penyimpanan ?? null),
            'status_umur' => $status,
            'umur_hari' => $umurHari,
        ];
    }

    /**
     * Generate notifikasi Lumbung Penuh
     *
     * @param array|\Illuminate\Database\Eloquent\Model $lumbung - Data lumbung dengan kapasitas
     * @param float $persentasePenggunaan - Persentase penggunaan (0-100)
     * @return array - Data notifikasi
     */
    public function buatNotifikasiLumbungPenuh(array|\Illuminate\Database\Eloquent\Model $lumbung, float $persentasePenggunaan): array
    {
        $namaLumbung = $lumbung['nama_lumbung'] ?? ($lumbung->nama_lumbung ?? 'Lumbung');

        $priority = match(true) {
            $persentasePenggunaan >= 95 => self::PRIORITY_LEVEL['CRITICAL'],
            $persentasePenggunaan >= 85 => self::PRIORITY_LEVEL['HIGH'],
            $persentasePenggunaan >= 75 => self::PRIORITY_LEVEL['MEDIUM'],
            default => self::PRIORITY_LEVEL['LOW'],
        };

        return [
            'tipe' => self::TIPE_NOTIFIKASI['LUMBUNG_PENUH'],
            'judul' => "{$namaLumbung} Hampir Penuh",
            'deskripsi' => "{$namaLumbung} sudah terisi {$persentasePenggunaan}% dari kapasitas total. " .
                          "Pertimbangkan untuk mengambil gabah atau memperluas kapasitas.",
            'priority' => $priority,
            'target_role' => ['pengelola', 'admin'],
            'related_id' => $lumbung['id_lumbung'] ?? ($lumbung->id_lumbung ?? null),
            'persentase_penggunaan' => $persentasePenggunaan,
        ];
    }

    /**
     * Generate notifikasi Slot Penuh
     *
     * @param array|\Illuminate\Database\Eloquent\Model $slot - Data slot
     * @param float $persentasePenggunaan - Persentase penggunaan (0-100)
     * @return array - Data notifikasi
     */
    public function buatNotifikasiSlotPenuh(array|\Illuminate\Database\Eloquent\Model $slot, float $persentasePenggunaan): array
    {
        $kodeSlot = $slot['kode_slot'] ?? ($slot->kode_slot ?? 'Unknown');

        $priority = match(true) {
            $persentasePenggunaan >= 95 => self::PRIORITY_LEVEL['CRITICAL'],
            $persentasePenggunaan >= 85 => self::PRIORITY_LEVEL['HIGH'],
            default => self::PRIORITY_LEVEL['MEDIUM'],
        };

        return [
            'tipe' => self::TIPE_NOTIFIKASI['SLOT_PENUH'],
            'judul' => "Slot {$kodeSlot} Hampir Penuh",
            'deskripsi' => "Slot {$kodeSlot} sudah terisi {$persentasePenggunaan}% dari kapasitas. " .
                          "Pertimbangkan untuk mengambil gabah dari slot ini.",
            'priority' => $priority,
            'target_role' => ['pengelola', 'admin'],
            'related_id' => $slot['id_slot'] ?? ($slot->id_slot ?? null),
            'persentase_penggunaan' => $persentasePenggunaan,
        ];
    }

    /**
     * Generate notifikasi Gabah Expired (melampaui durasi maksimal)
     *
     * @param array|\Illuminate\Database\Eloquent\Model $penyimpanan - Data penyimpanan gabah
     * @param int $hariMelampaui - Berapa hari melampaui durasi maksimal
     * @return array - Data notifikasi
     */
    public function buatNotifikasiGabahExpired(array|\Illuminate\Database\Eloquent\Model $penyimpanan, int $hariMelampaui = 0): array
    {
        return [
            'tipe' => self::TIPE_NOTIFIKASI['GABAH_EXPIRED'],
            'judul' => "⚠️ ALERT: Gabah Expired - Segera Ambil",
            'deskripsi' => "Gabah jenis {$this->getJenisGabah($penyimpanan)} di slot {$this->getSlot($penyimpanan)} " .
                          "telah melampaui durasi penyimpanan maksimal {$hariMelampaui} hari. " .
                          "SEGERA AMBIL untuk menghindari kerusakan kualitas gabah.",
            'priority' => self::PRIORITY_LEVEL['CRITICAL'],
            'target_role' => ['pengelola', 'admin'],
            'related_id' => $penyimpanan['id_penyimpanan'] ?? ($penyimpanan->id_penyimpanan ?? null),
            'action_required' => true,
            'hari_melampaui' => $hariMelampaui,
        ];
    }

    /**
     * Generate notifikasi Gabah Warning (mendekati durasi maksimal)
     *
     * @param array|\Illuminate\Database\Eloquent\Model $penyimpanan - Data penyimpanan gabah
     * @param int $hariSisanya - Berapa hari lagi hingga melampaui batas
     * @return array - Data notifikasi
     */
    public function buatNotifikasiGabahWarning(array|\Illuminate\Database\Eloquent\Model $penyimpanan, int $hariSisanya): array
    {
        return [
            'tipe' => self::TIPE_NOTIFIKASI['GABAH_WARNING'],
            'judul' => "Perhatian: Gabah Mendekati Durasi Maksimal",
            'deskripsi' => "Gabah jenis {$this->getJenisGabah($penyimpanan)} di slot {$this->getSlot($penyimpanan)} " .
                          "akan melampaui durasi penyimpanan dalam {$hariSisanya} hari. " .
                          "Rencanakan pengambilan untuk menjaga kualitas.",
            'priority' => self::PRIORITY_LEVEL['HIGH'],
            'target_role' => ['pengelola', 'admin'],
            'related_id' => $penyimpanan['id_penyimpanan'] ?? ($penyimpanan->id_penyimpanan ?? null),
            'action_required' => true,
            'hari_tersisa' => $hariSisanya,
        ];
    }

    /**
     * Generate notifikasi Panen Baru
     *
     * @param array|\Illuminate\Database\Eloquent\Model $panen - Data panen
     * @param string $namaPetani - Nama petani yang panen
     * @param float $totalGabah - Total gabah yang dipanen
     * @return array - Data notifikasi
     */
    public function buatNotifikasiPanenBaru(array|\Illuminate\Database\Eloquent\Model $panen, string $namaPetani, float $totalGabah): array
    {
        return [
            'tipe' => self::TIPE_NOTIFIKASI['PANEN_BARU'],
            'judul' => "Panen Baru Dicatat",
            'deskripsi' => "Petani {$namaPetani} telah melakukan panen sebanyak {$totalGabah} kg pada " .
                          date('d-m-Y', strtotime($panen['tanggal_panen'] ?? $panen->tanggal_panen ?? now())) . ". " .
                          "Siap untuk diproses penyimpanan.",
            'priority' => self::PRIORITY_LEVEL['MEDIUM'],
            'target_role' => ['admin', 'pengelola'],
            'related_id' => $panen['id_panen'] ?? ($panen->id_panen ?? null),
            'action_required' => true,
        ];
    }

    /**
     * Generate notifikasi Instruksi Penyimpanan
     *
     * @param array|\Illuminate\Database\Eloquent\Model $instruksi - Data instruksi penyimpanan
     * @param string $namaSlot - Nama/kode slot
     * @param float $jumlahGabah - Jumlah gabah yang akan disimpan
     * @return array - Data notifikasi
     */
    public function buatNotifikasiInstruksiSimpan(array|\Illuminate\Database\Eloquent\Model $instruksi, string $namaSlot, float $jumlahGabah): array
    {
        return [
            'tipe' => self::TIPE_NOTIFIKASI['INSTRUKSI_SIMPAN'],
            'judul' => "Instruksi Penyimpanan Baru",
            'deskripsi' => "Ada instruksi penyimpanan baru: {$jumlahGabah} kg gabah harus disimpan " .
                          "di slot {$namaSlot}. Mohon segera lakukan penyimpanan.",
            'priority' => self::PRIORITY_LEVEL['MEDIUM'],
            'target_role' => ['pengelola'],
            'related_id' => $instruksi['id_instruksi'] ?? ($instruksi->id_instruksi ?? null),
            'action_required' => true,
        ];
    }

    /**
     * Generate notifikasi Permintaan Pengambilan
     *
     * @param array|\Illuminate\Database\Eloquent\Model $permintaan - Data permintaan pengambilan
     * @param string $namaPetani - Nama petani yang mengajukan
     * @param float $jumlahDiminta - Jumlah yang diminta
     * @return array - Data notifikasi
     */
    public function buatNotifikasiPermintaanPengambilan(array|\Illuminate\Database\Eloquent\Model $permintaan, string $namaPetani, float $jumlahDiminta): array
    {
        return [
            'tipe' => self::TIPE_NOTIFIKASI['PERMINTAAN_PENGAMBILAN'],
            'judul' => "Ada Permintaan Pengambilan Gabah",
            'deskripsi' => "Petani {$namaPetani} mengajukan permintaan pengambilan gabah sebanyak {$jumlahDiminta} kg. " .
                          "Mohon review dan setujui/tolak permintaan tersebut.",
            'priority' => self::PRIORITY_LEVEL['HIGH'],
            'target_role' => ['admin'],
            'related_id' => $permintaan['id_permintaan'] ?? ($permintaan->id_permintaan ?? null),
            'action_required' => true,
        ];
    }

    /**
     * Generate notifikasi Penyimpanan Selesai
     *
     * @param array|\Illuminate\Database\Eloquent\Model $penyimpanan - Data penyimpanan
     * @param float $jumlahDisimpan - Jumlah yang berhasil disimpan
     * @return array - Data notifikasi
     */
    public function buatNotifikasiPenyimpananSelesai(array|\Illuminate\Database\Eloquent\Model $penyimpanan, float $jumlahDisimpan): array
    {
        return [
            'tipe' => self::TIPE_NOTIFIKASI['PENYIMPANAN_SELESAI'],
            'judul' => "Penyimpanan Gabah Selesai",
            'deskripsi' => "Penyimpanan {$jumlahDisimpan} kg gabah jenis {$this->getJenisGabah($penyimpanan)} " .
                          "di slot {$this->getSlot($penyimpanan)} telah selesai dan terkonfirmasi.",
            'priority' => self::PRIORITY_LEVEL['LOW'],
            'target_role' => ['admin', 'pengelola'],
            'related_id' => $penyimpanan['id_penyimpanan'] ?? ($penyimpanan->id_penyimpanan ?? null),
        ];
    }

    /**
     * Generate notifikasi Pengambilan Selesai
     *
     * @param array|\Illuminate\Database\Eloquent\Model $pengambilan - Data pengambilan
     * @param float $jumlahDiambil - Jumlah yang berhasil diambil
     * @param string $namaPetani - Nama petani yang mengambil
     * @return array - Data notifikasi
     */
    public function buatNotifikasiPengambilanSelesai(array|\Illuminate\Database\Eloquent\Model $pengambilan, float $jumlahDiambil, string $namaPetani): array
    {
        return [
            'tipe' => self::TIPE_NOTIFIKASI['PENGAMBILAN_SELESAI'],
            'judul' => "Pengambilan Gabah Selesai",
            'deskripsi' => "Pengambilan {$jumlahDiambil} kg gabah oleh petani {$namaPetani} telah selesai dan terkonfirmasi. " .
                          "Data stok telah diperbarui.",
            'priority' => self::PRIORITY_LEVEL['LOW'],
            'target_role' => ['admin', 'pengelola'],
            'related_id' => $pengambilan['id_permintaan'] ?? ($pengambilan->id_permintaan ?? null),
        ];
    }

    /**
     * Generate notifikasi Kapasitas Rendah
     * Ketika gabah yang tersimpan turun di bawah threshold minimum
     *
     * @param array|\Illuminate\Database\Eloquent\Model $lumbung - Data lumbung
     * @param float $kapasitasTersedia - Kapasitas yang masih tersedia (kg)
     * @param float $totalKapasitas - Total kapasitas lumbung (kg)
     * @return array - Data notifikasi
     */
    public function buatNotifikasiKapasitasRendah(array|\Illuminate\Database\Eloquent\Model $lumbung, float $kapasitasTersedia, float $totalKapasitas): array
    {
        $namaLumbung = $lumbung['nama_lumbung'] ?? ($lumbung->nama_lumbung ?? 'Lumbung');
        $persentaseKosong = round(($kapasitasTersedia / $totalKapasitas) * 100, 2);

        return [
            'tipe' => self::TIPE_NOTIFIKASI['KAPASITAS_RENDAH'],
            'judul' => "{$namaLumbung} Kapasitas Rendah",
            'deskripsi' => "{$namaLumbung} hanya memiliki {$persentaseKosong}% kapasitas yang tersedia ({$kapasitasTersedia} kg dari {$totalKapasitas} kg). " .
                          "Pertimbangkan untuk menambah kapasitas penyimpanan.",
            'priority' => self::PRIORITY_LEVEL['MEDIUM'],
            'target_role' => ['admin'],
            'related_id' => $lumbung['id_lumbung'] ?? ($lumbung->id_lumbung ?? null),
            'persentase_kosong' => $persentaseKosong,
        ];
    }

    /**
     * Helper: Ambil nama jenis gabah dari penyimpanan
     *
     * @param array|\Illuminate\Database\Eloquent\Model $penyimpanan - Data penyimpanan
     * @return string - Nama jenis gabah
     */
    private function getJenisGabah(array|\Illuminate\Database\Eloquent\Model $penyimpanan): string
    {
        if (is_array($penyimpanan)) {
            return $penyimpanan['nama_jenis'] ?? $penyimpanan['jenis_gabah'] ?? 'Unknown';
        }

        return $penyimpanan->jenisGabah->nama_jenis ?? 'Unknown';
    }

    /**
     * Helper: Ambil kode slot dari penyimpanan
     *
     * @param array|\Illuminate\Database\Eloquent\Model $penyimpanan - Data penyimpanan
     * @return string - Kode slot
     */
    private function getSlot(array|\Illuminate\Database\Eloquent\Model $penyimpanan): string
    {
        if (is_array($penyimpanan)) {
            return $penyimpanan['kode_slot'] ?? $penyimpanan['slot'] ?? 'Unknown';
        }

        return $penyimpanan->slot->kode_slot ?? 'Unknown';
    }

    /**
     * Batch create multiple notifikasi sekaligus
     * Useful untuk setup notifikasi awal sistem
     *
     * @param Collection $notifikasiData - Collection berisi array notifikasi
     * @return Collection - Collection notifikasi yang sudah dibuat
     */
    public function buatBatchNotifikasi(Collection $notifikasiData): Collection
    {
        return $notifikasiData->map(fn($data) => array_merge($data, [
            'created_at' => now(),
            'is_read' => false,
        ]));
    }

    /**
     * Filter notifikasi berdasarkan role pengguna
     *
     * @param Collection $notifikasi - Collection notifikasi
     * @param string $role - Role pengguna: 'admin' | 'pengelola' | 'petani'
     * @return Collection - Notifikasi yang relevan untuk role tersebut
     */
    public function filterNotifikasiByRole(Collection $notifikasi, string $role): Collection
    {
        return $notifikasi->filter(fn($notif) =>
            in_array($role, $notif['target_role'] ?? [])
        );
    }

    /**
     * Urutkan notifikasi berdasarkan priority dan waktu
     *
     * @param Collection $notifikasi - Collection notifikasi
     * @return Collection - Notifikasi yang sudah diurutkan
     */
    public function urutkanNotifikasi(Collection $notifikasi): Collection
    {
        $priorityOrder = [
            self::PRIORITY_LEVEL['CRITICAL'] => 1,
            self::PRIORITY_LEVEL['HIGH'] => 2,
            self::PRIORITY_LEVEL['MEDIUM'] => 3,
            self::PRIORITY_LEVEL['LOW'] => 4,
        ];

        return $notifikasi->sort(function ($a, $b) use ($priorityOrder) {
            $priorityA = $priorityOrder[$a['priority']] ?? 999;
            $priorityB = $priorityOrder[$b['priority']] ?? 999;

            if ($priorityA !== $priorityB) {
                return $priorityA - $priorityB;
            }

            // If priority sama, urutkan by time (newest first)
            return strtotime($b['created_at'] ?? now()) - strtotime($a['created_at'] ?? now());
        })->values();
    }

    /**
     * Dapatkan ringkasan notifikasi (count per tipe & priority)
     *
     * @param Collection $notifikasi - Collection notifikasi
     * @return array - Summary data
     */
    public function getRingkasanNotifikasi(Collection $notifikasi): array
    {
        return [
            'total' => $notifikasi->count(),
            'belum_dibaca' => $notifikasi->where('is_read', false)->count(),
            'per_priority' => [
                'critical' => $notifikasi->where('priority', 'critical')->count(),
                'high' => $notifikasi->where('priority', 'high')->count(),
                'medium' => $notifikasi->where('priority', 'medium')->count(),
                'low' => $notifikasi->where('priority', 'low')->count(),
            ],
            'per_tipe' => $notifikasi->groupBy('tipe')->map->count(),
        ];
    }
}
