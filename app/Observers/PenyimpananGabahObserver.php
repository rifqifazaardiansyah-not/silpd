<?php

namespace App\Observers;

use App\Models\PenyimpananGabah;
use App\Services\NotifikasiService;
use App\Services\TentukanSlotService;

class PenyimpananGabahObserver
{
    private $slotService;
    private $notifikasiService;

    public function __construct()
    {
        $this->slotService = new TentukanSlotService();
        $this->notifikasiService = new NotifikasiService();
    }

    /**
     * Trigger saat PenyimpananGabah baru dibuat.
     * Cek apakah slot sudah hampir penuh, jika ya trigger notifikasi.
     */
    public function created(PenyimpananGabah $penyimpanan): void
    {
        try {
            $slot = $penyimpanan->slotLumbung;

            if (!$slot) {
                return;
            }

            // Hitung persentase penggunaan slot
            $persentasePenggunaan = ($slot->kapasitas - $slot->kapasitas_tersedia) / $slot->kapasitas * 100;

            // Jika slot sudah > 80% penuh, trigger notifikasi
            if ($persentasePenggunaan > 80) {
                $this->notifikasiService->buatNotifikasiSlotPenuh(
                    $penyimpanan->detailPanen,
                    $slot,
                    $persentasePenggunaan
                );
            }

            // Cek apakah lumbung (warehouse) sudah hampir penuh
            $lumbung = $slot->lumbung;
            if ($lumbung) {
                $totalKapasitas = $lumbung->slotLumbung()->sum('kapasitas');
                $totalTersedia = $lumbung->slotLumbung()->sum('kapasitas_tersedia');
                $persentaseLumbung = ($totalKapasitas - $totalTersedia) / $totalKapasitas * 100;

                if ($persentaseLumbung > 95) {
                    $this->notifikasiService->buatNotifikasiLumbungPenuh(
                        $lumbung,
                        $persentaseLumbung
                    );
                } elseif ($persentaseLumbung > 80 && $persentaseLumbung <= 95) {
                    // Warning jika kapasitas rendah
                    $this->notifikasiService->buatNotifikasiKapasitasRendah(
                        $lumbung,
                        $persentaseLumbung
                    );
                }
            }
        } catch (\Exception $e) {
            // Log error tapi jangan crash sistem
            \Log::error('PenyimpananGabahObserver error: ' . $e->getMessage());
        }
    }

    /**
     * Trigger saat PenyimpananGabah diupdate.
     * Cek kapasitas slot ulang jika jumlah berubah.
     */
    public function updated(PenyimpananGabah $penyimpanan): void
    {
        // Jika jumlah gabah berubah, jalankan logic yang sama seperti created
        if ($penyimpanan->isDirty('jumlah')) {
            $this->created($penyimpanan);
        }

        // Jika status berubah menjadi 'diambil', kurangi notifikasi
        if ($penyimpanan->isDirty('status') && $penyimpanan->status === 'diambil') {
            // Bisa tambahkan logic khusus saat gabah diambil
        }
    }
}
