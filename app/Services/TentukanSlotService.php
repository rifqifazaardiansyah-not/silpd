<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

/**
 * Service untuk menentukan slot penyimpanan gabah yang sesuai
 * Menggunakan algoritma best-fit untuk optimasi kapasitas lumbung
 */
class TentukanSlotService
{
    /**
     * Ambang batas minimum kapasitas tersedia (dalam persentase)
     * Slot tidak akan digunakan jika kapasitas tersisa < threshold ini
     */
    private const MIN_KAPASITAS_THRESHOLD = 5; // 5% dari kapasitas total

    /**
     * Temukan slot yang paling sesuai untuk menyimpan gabah tertentu
     * Menggunakan strategi: fit dengan kapasitas tersedia paling minimum tetapi masih cukup
     *
     * @param EloquentCollection|Collection $slots - Collection slot lumbung dengan struktur:
     *        ['id_slot', 'kode_slot', 'kapasitas', 'kapasitas_tersedia', 'jenis_gabah_terakhir', ...]
     * @param float $jumlahGabah - Jumlah gabah yang akan disimpan
     * @param int $idJenisGabah - ID jenis gabah untuk mempertimbangkan kompatibilitas (optional)
     * @return array|null - Slot yang dipilih atau null jika tidak ada yang cocok
     *
     * @example
     * $slots = Slot::where('id_lumbung', $idLumbung)->get();
     * $slotTerpilih = $service->tentukanSlotTerbaik($slots, 24, 1);
     * // Hasil: ['id_slot' => 1, 'kode_slot' => 'A1', 'kapasitas' => 2000, ...]
     */
    public function tentukanSlotTerbaik(EloquentCollection|Collection $slots, float $jumlahGabah, int $idJenisGabah = 0): ?array
    {
        if ($slots->isEmpty() || $jumlahGabah <= 0) {
            return null;
        }

        // Filter slot yang memiliki kapasitas cukup
        $slotValid = $slots->filter(function ($slot) use ($jumlahGabah) {
            return $this->isSlotCukup($slot, $jumlahGabah);
        });

        if ($slotValid->isEmpty()) {
            return null;
        }

        // Urutkan berdasarkan best-fit: kapasitas tersedia paling minimum tetapi masih cukup
        $slotTerpilih = $slotValid
            ->sortBy(fn($slot) => $slot['kapasitas_tersedia'] ?? $slot->kapasitas_tersedia)
            ->first();

        return $slotTerpilih instanceof \Illuminate\Database\Eloquent\Model
            ? $slotTerpilih->toArray()
            : $slotTerpilih;
    }

    /**
     * Tentukan multiple slot untuk menyimpan gabah dalam jumlah besar
     * Jika satu slot tidak cukup, akan membagi ke beberapa slot
     *
     * @param EloquentCollection|Collection $slots - Collection slot lumbung
     * @param float $jumlahGabah - Jumlah gabah total untuk disimpan
     * @param int $idJenisGabah - ID jenis gabah (optional)
     * @return Collection - Collection slot yang dipilih dengan struktur [['id_slot', 'jumlah_disimpan'], ...]
     *
     * @example
     * $slots = Slot::where('id_lumbung', 1)->get();
     * $slotMultiple = $service->tentukanMultipleSlot($slots, 500, 1);
     * // Hasil: [
     * //     ['id_slot' => 1, 'jumlah_disimpan' => 200],
     * //     ['id_slot' => 2, 'jumlah_disimpan' => 300],
     * // ]
     */
    public function tentukanMultipleSlot(EloquentCollection|Collection $slots, float $jumlahGabah, int $idJenisGabah = 0): Collection
    {
        $hasilSlot = collect();
        $sisaGabah = $jumlahGabah;
        $slotsAvailable = $slots->filter(fn($slot) => $slot['kapasitas_tersedia'] > 0)->values();

        while ($sisaGabah > 0 && $slotsAvailable->isNotEmpty()) {
            $slotTerpilih = $this->tentukanSlotTerbaik($slotsAvailable, $sisaGabah, $idJenisGabah);

            if (!$slotTerpilih) {
                break;
            }

            $kapasitasSlot = $slotTerpilih['kapasitas_tersedia'] ?? 0;
            $jumlahDisimpan = min($sisaGabah, $kapasitasSlot);

            $hasilSlot->push([
                'id_slot' => $slotTerpilih['id_slot'],
                'kode_slot' => $slotTerpilih['kode_slot'],
                'jumlah_disimpan' => $jumlahDisimpan,
                'kapasitas_tersedia_sebelum' => $kapasitasSlot,
                'kapasitas_tersedia_sesudah' => $kapasitasSlot - $jumlahDisimpan,
            ]);

            $sisaGabah -= $jumlahDisimpan;

            // Hapus slot dari available jika sudah penuh
            $slotsAvailable = $slotsAvailable->filter(fn($s) => 
                ($s['id_slot'] ?? $s->id_slot) !== ($slotTerpilih['id_slot'] ?? 0)
            )->values();
        }

        return $hasilSlot;
    }

    /**
     * Validasi apakah slot memiliki kapasitas cukup untuk gabah tertentu
     *
     * @param array|\Illuminate\Database\Eloquent\Model $slot - Data slot
     * @param float $jumlahGabah - Jumlah gabah yang akan disimpan
     * @return bool - True jika kapasitas cukup
     */
    public function isSlotCukup(array|\Illuminate\Database\Eloquent\Model $slot, float $jumlahGabah): bool
    {
        $kapasitasTersedia = $slot['kapasitas_tersedia'] ?? ($slot->kapasitas_tersedia ?? 0);
        return $kapasitasTersedia >= $jumlahGabah;
    }

    /**
     * Hitung sisa kapasitas slot setelah penyimpanan
     *
     * @param array|\Illuminate\Database\Eloquent\Model $slot - Data slot
     * @param float $jumlahGabah - Jumlah gabah yang disimpan
     * @return float - Sisa kapasitas
     */
    public function hitungSisaKapasitas(array|\Illuminate\Database\Eloquent\Model $slot, float $jumlahGabah): float
    {
        $kapasitasTersedia = $slot['kapasitas_tersedia'] ?? ($slot->kapasitas_tersedia ?? 0);
        return round(max(0, $kapasitasTersedia - $jumlahGabah), 2);
    }

    /**
     * Hitung persentase penggunaan slot
     *
     * @param array|\Illuminate\Database\Eloquent\Model $slot - Data slot
     * @return float - Persentase penggunaan (0-100)
     */
    public function hitungPersentasePenggunaan(array|\Illuminate\Database\Eloquent\Model $slot): float
    {
        $kapasitas = $slot['kapasitas'] ?? ($slot->kapasitas ?? 1);
        $kapasitasTersedia = $slot['kapasitas_tersedia'] ?? ($slot->kapasitas_tersedia ?? 0);
        $terpakai = $kapasitas - $kapasitasTersedia;

        return round(($terpakai / $kapasitas) * 100, 2);
    }

    /**
     * Dapatkan slot dengan persentase penggunaan tertinggi
     * (untuk distribusi beban yang merata)
     *
     * @param EloquentCollection|Collection $slots - Collection slot
     * @return array|null - Slot dengan penggunaan tertinggi
     */
    public function getSlotPalingPenuh(EloquentCollection|Collection $slots): ?array
    {
        if ($slots->isEmpty()) {
            return null;
        }

        $slotMax = $slots
            ->map(fn($slot) => array_merge(
                $slot instanceof \Illuminate\Database\Eloquent\Model ? $slot->toArray() : $slot,
                ['persentase_penggunaan' => $this->hitungPersentasePenggunaan($slot)]
            ))
            ->sortByDesc('persentase_penggunaan')
            ->first();

        return is_array($slotMax) ? $slotMax : $slotMax?->toArray();
    }

    /**
     * Dapatkan slot dengan persentase penggunaan terendah
     * (untuk distribusi beban optimal)
     *
     * @param EloquentCollection|Collection $slots - Collection slot
     * @return array|null - Slot dengan penggunaan terendah
     */
    public function getSlotPalingKosong(EloquentCollection|Collection $slots): ?array
    {
        if ($slots->isEmpty()) {
            return null;
        }

        $slotMin = $slots
            ->map(fn($slot) => array_merge(
                $slot instanceof \Illuminate\Database\Eloquent\Model ? $slot->toArray() : $slot,
                ['persentase_penggunaan' => $this->hitungPersentasePenggunaan($slot)]
            ))
            ->sortBy('persentase_penggunaan')
            ->first();

        return is_array($slotMin) ? $slotMin : $slotMin?->toArray();
    }

    /**
     * Estimasi berapa banyak gabah yang bisa disimpan di lumbung secara total
     *
     * @param EloquentCollection|Collection $slots - Collection slot dari satu lumbung
     * @return float - Total kapasitas tersedia di lumbung
     */
    public function totalKapasitasTersedia(EloquentCollection|Collection $slots): float
    {
        return round(
            $slots->sum(fn($slot) => $slot['kapasitas_tersedia'] ?? ($slot->kapasitas_tersedia ?? 0)),
            2
        );
    }

    /**
     * Validasi apakah lumbung masih memiliki ruang untuk menyimpan
     *
     * @param EloquentCollection|Collection $slots - Collection slot
     * @param float $jumlahGabah - Jumlah gabah yang akan disimpan
     * @return bool - True jika lumbung cukup
     */
    public function isLumbungCukup(EloquentCollection|Collection $slots, float $jumlahGabah): bool
    {
        return $this->totalKapasitasTersedia($slots) >= $jumlahGabah;
    }

    /**
     * Rekomendasi slot dengan batasan tertentu
     *
     * @param EloquentCollection|Collection $slots - Collection slot
     * @param float $jumlahGabah - Jumlah gabah
     * @param string $strategi - 'first-fit' | 'best-fit' | 'worst-fit' (default: 'best-fit')
     * @return array|null - Slot yang direkomendasikan
     */
    public function rekomendasiSlot(EloquentCollection|Collection $slots, float $jumlahGabah, string $strategi = 'best-fit'): ?array
    {
        return match($strategi) {
            'first-fit' => $slots->first(fn($slot) => $this->isSlotCukup($slot, $jumlahGabah)),
            'worst-fit' => $this->getSlotPalingPenuh($slots),
            'best-fit' => $this->tentukanSlotTerbaik($slots, $jumlahGabah),
            default => null,
        };
    }
}
