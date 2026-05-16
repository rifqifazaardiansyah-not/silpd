<?php

namespace App\Observers;

use App\Models\InstruksiPenyimpanan;

class InstruksiPenyimpananObserver
{
    /**
     * Trigger saat InstruksiPenyimpanan diupdate.
     * Jika status berubah menjadi 'selesai', otomatis kurangi kapasitas_tersedia di slot.
     */
    public function updated(InstruksiPenyimpanan $instruksi): void
    {
        try {
            // Cek apakah status field berubah
            if (!$instruksi->isDirty('status')) {
                return;
            }

            // Jika status baru menjadi 'selesai'
            if ($instruksi->status === 'selesai') {
                $slot = $instruksi->slotLumbung;

                if (!$slot) {
                    return;
                }

                // Kurangi kapasitas_tersedia dengan jumlah gabah yang disimpan
                $kapasitasBaru = $slot->kapasitas_tersedia - $instruksi->jumlah;

                // Pastikan tidak negatif
                if ($kapasitasBaru < 0) {
                    \Log::warning("Kapasitas slot {$slot->id_slot_lumbung} akan negatif. Instruksi: {$instruksi->id_instruksi}");
                    $kapasitasBaru = 0;
                }

                $slot->update(['kapasitas_tersedia' => $kapasitasBaru]);

                \Log::info("Kapasitas slot {$slot->id_slot_lumbung} berkurang {$instruksi->jumlah} kg");
            }

            // Jika status berubah kembali dari 'selesai' ke 'pending'
            if ($instruksi->getOriginal('status') === 'selesai' && $instruksi->status === 'pending') {
                $slot = $instruksi->slotLumbung;

                if (!$slot) {
                    return;
                }

                // Tambah kembali kapasitas_tersedia (rollback)
                $kapasitasBaru = $slot->kapasitas_tersedia + $instruksi->jumlah;

                // Pastikan tidak melebihi kapasitas
                if ($kapasitasBaru > $slot->kapasitas) {
                    $kapasitasBaru = $slot->kapasitas;
                }

                $slot->update(['kapasitas_tersedia' => $kapasitasBaru]);

                \Log::info("Kapasitas slot {$slot->id_slot_lumbung} kembali ditambah {$instruksi->jumlah} kg");
            }
        } catch (\Exception $e) {
            // Log error tapi jangan crash sistem
            \Log::error('InstruksiPenyimpananObserver error: ' . $e->getMessage());
        }
    }

    /**
     * Trigger saat InstruksiPenyimpanan dibuat.
     * Validasi bahwa slot punya kapasitas cukup.
     */
    public function created(InstruksiPenyimpanan $instruksi): void
    {
        try {
            $slot = $instruksi->slotLumbung;

            if (!$slot) {
                return;
            }

            // Jika kapasitas tidak cukup, warn log
            if ($instruksi->jumlah > $slot->kapasitas_tersedia) {
                \Log::warning("Instruksi {$instruksi->id_instruksi} melebihi kapasitas slot {$slot->id_slot_lumbung}");
            }
        } catch (\Exception $e) {
            \Log::error('InstruksiPenyimpananObserver.created error: ' . $e->getMessage());
        }
    }
}
