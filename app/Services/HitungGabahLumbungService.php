<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Service untuk menghitung jumlah gabah yang akan disimpan di lumbung
 * Mengimplementasikan aturan 3% dari setiap jenis gabah yang dipanen
 */
class HitungGabahLumbungService
{
    /**
     * Persentase gabah yang disimpan di lumbung desa
     */
    private const PERSENTASE_PENYIMPANAN = 3;

    /**
     * Hitung jumlah gabah untuk disimpan berdasarkan detail panen
     *
     * @param float $jumlahPanen - Jumlah gabah yang dipanen (dalam kg)
     * @return float - Jumlah gabah yang akan disimpan di lumbung
     *
     * @example
     * $service = new HitungGabahLumbungService();
     * $gabahDisimpan = $service->hitungGabahDisimpan(800); // 24 kg (3% dari 800)
     */
    public function hitungGabahDisimpan(float $jumlahPanen): float
    {
        if ($jumlahPanen <= 0) {
            return 0;
        }

        return round(($jumlahPanen * self::PERSENTASE_PENYIMPANAN) / 100, 2);
    }

    /**
     * Hitung sisa gabah untuk petani (100% - 3%)
     *
     * @param float $jumlahPanen - Jumlah gabah yang dipanen (dalam kg)
     * @return float - Jumlah gabah yang menjadi milik petani
     *
     * @example
     * $service = new HitungGabahLumbungService();
     * $gabahPetani = $service->hitungGabahPetani(800); // 776 kg (97% dari 800)
     */
    public function hitungGabahPetani(float $jumlahPanen): float
    {
        if ($jumlahPanen <= 0) {
            return 0;
        }

        return round($jumlahPanen - $this->hitungGabahDisimpan($jumlahPanen), 2);
    }

    /**
     * Hitung total gabah untuk disimpan dari multiple detail panen
     * (biasanya dari satu event panen yang memiliki beberapa jenis gabah)
     *
     * @param Collection $detailPanenCollection - Collection dari detail panen dengan key 'jumlah_panen'
     * @return float - Total gabah yang akan disimpan
     *
     * @example
     * $detailPanen = collect([
     *     ['jumlah_panen' => 800],
     *     ['jumlah_panen' => 450],
     * ]);
     * $total = $service->hitungTotalGabahDisimpan($detailPanen); // 37.5 kg
     */
    public function hitungTotalGabahDisimpan(Collection $detailPanenCollection): float
    {
        return round(
            $detailPanenCollection->sum(fn($detail) =>
                $this->hitungGabahDisimpan($detail['jumlah_panen'] ?? 0)
            ),
            2
        );
    }

    /**
     * Hitung total gabah untuk petani dari multiple detail panen
     *
     * @param Collection $detailPanenCollection - Collection dari detail panen
     * @return float - Total gabah milik petani
     */
    public function hitungTotalGabahPetani(Collection $detailPanenCollection): float
    {
        return round(
            $detailPanenCollection->sum(fn($detail) =>
                $this->hitungGabahPetani($detail['jumlah_panen'] ?? 0)
            ),
            2
        );
    }

    /**
     * Breakdown gabah dari satu event panen
     * Mengembalikan detail perpisahan antara gabah lumbung dan gabah petani per jenis
     *
     * @param float $jumlahPanen - Total panen
     * @param string $jenisGabah - Nama jenis gabah
     * @return array - Array berisi: jumlah_panen, jumlah_disimpan, jumlah_petani
     *
     * @example
     * $breakdown = $service->breakdownGabah(800, 'IR64');
     * // Hasil:
     * // [
     * //     'jumlah_panen' => 800,
     * //     'jumlah_disimpan' => 24,
     * //     'jumlah_petani' => 776,
     * //     'jenis_gabah' => 'IR64',
     * //     'persentase_lumbung' => 3,
     * // ]
     */
    public function breakdownGabah(float $jumlahPanen, string $jenisGabah = ''): array
    {
        $jumlahDisimpan = $this->hitungGabahDisimpan($jumlahPanen);
        $jumlahPetani = $this->hitungGabahPetani($jumlahPanen);

        return [
            'jumlah_panen' => $jumlahPanen,
            'jumlah_disimpan' => $jumlahDisimpan,
            'jumlah_petani' => $jumlahPetani,
            'jenis_gabah' => $jenisGabah,
            'persentase_lumbung' => self::PERSENTASE_PENYIMPANAN,
            'persentase_petani' => 100 - self::PERSENTASE_PENYIMPANAN,
        ];
    }

    /**
     * Validasi apakah jumlah panen valid untuk disimpan
     * (minimal harus ada sisa setelah 3% disimpan)
     *
     * @param float $jumlahPanen - Jumlah panen untuk divalidasi
     * @return bool - True jika valid
     */
    public function isValidJumlahPanen(float $jumlahPanen): bool
    {
        return $jumlahPanen > 0;
    }

    /**
     * Dapatkan persentase penyimpanan lumbung
     *
     * @return int - Persentase tetap (3)
     */
    public function getPersentasePenyimpanan(): int
    {
        return self::PERSENTASE_PENYIMPANAN;
    }

    /**
     * Hitung berapa banyak panen yang dibutuhkan untuk mendapatkan gabah disimpan tertentu
     * (reverse calculation)
     *
     * @param float $gabahDisimpanTarget - Target jumlah gabah yang ingin disimpan
     * @return float - Jumlah panen yang dibutuhkan
     *
     * @example
     * $panen = $service->hitungPanenDariTarget(24); // 800 kg
     */
    public function hitungPanenDariTarget(float $gabahDisimpanTarget): float
    {
        if ($gabahDisimpanTarget <= 0) {
            return 0;
        }

        return round(($gabahDisimpanTarget * 100) / self::PERSENTASE_PENYIMPANAN, 2);
    }
}
