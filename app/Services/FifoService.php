<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

/**
 * Service untuk implementasi prinsip FIFO (First In First Out)
 * Memastikan gabah yang paling lama disimpan diambil terlebih dahulu
 */
class FifoService
{
    /**
     * Ambang batas waktu penyimpanan maksimal (dalam hari)
     * Gabah yang melampaui ini akan mendapat notifikasi prioritas
     */
    private const MAX_DURASI_SIMPAN_HARI = 180; // 6 bulan

    /**
     * Ambang batas notifikasi warning (dalam hari)
     * Gabah yang sudah mencapai ini akan di-warning
     */
    private const WARNING_DURASI_SIMPAN_HARI = 120; // 4 bulan

    /**
     * Dapatkan data gabah yang disimpan dengan urutan FIFO (terlama di depan)
     * Diurutkan berdasarkan tanggal masuk (ascending)
     *
     * @param EloquentCollection|Collection $penyimpananGabah - Collection penyimpanan gabah dengan struktur:
     *        ['id_penyimpanan', 'tanggal_masuk', 'jumlah', 'status', 'jenis_gabah', ...]
     * @return Collection - Collection yang diurutkan berdasarkan FIFO
     *
     * @example
     * $penyimpanan = PenyimpananGabah::where('status', 'tersimpan')->get();
     * $urutanFifo = $service->urutkanBerdasarkanFifo($penyimpanan);
     */
    public function urutkanBerdasarkanFifo(EloquentCollection|Collection $penyimpananGabah): Collection
    {
        return $penyimpananGabah
            ->sortBy(fn($item) => $item['tanggal_masuk'] ?? ($item->tanggal_masuk ?? now()))
            ->values();
    }

    /**
     * Ambil gabah berdasarkan FIFO untuk keperluan tertentu
     * Mengembalikan data gabah yang seharusnya diambil terlebih dahulu
     *
     * @param EloquentCollection|Collection $penyimpananGabah - Collection penyimpanan gabah
     * @param float $jumlahDibutuhkan - Jumlah gabah yang dibutuhkan untuk diambil (dalam kg)
     * @param int $idJenisGabah - Filter hanya jenis gabah tertentu (optional)
     * @return Collection - Collection gabah yang akan diambil dengan struktur:
     *        [['id_penyimpanan', 'jumlah_ambil', 'tanggal_masuk', ...], ...]
     *
     * @example
     * $penyimpanan = PenyimpananGabah::where('id_slot', $idSlot)
     *                                 ->where('status', 'tersimpan')
     *                                 ->get();
     * $gabahDiambil = $service->ambilBerdasarkanFifo($penyimpanan, 50, 1);
     * // Akan mengambil gabah terlama terlebih dahulu hingga mencapai 50 kg
     */
    public function ambilBerdasarkanFifo(EloquentCollection|Collection $penyimpananGabah, float $jumlahDibutuhkan, int $idJenisGabah = 0): Collection
    {
        $hasilAmbil = collect();
        $sisaDibutuhkan = $jumlahDibutuhkan;

        $penyimpananUrut = $this->urutkanBerdasarkanFifo(
            $penyimpananGabah->filter(fn($item) => 
                $idJenisGabah === 0 || ($item['id_jenis_gabah'] ?? $item->id_jenis_gabah ?? 0) === $idJenisGabah
            )
        );

        foreach ($penyimpananUrut as $penyimpanan) {
            if ($sisaDibutuhkan <= 0) {
                break;
            }

            $jumlahTersedia = $penyimpanan['jumlah'] ?? ($penyimpanan->jumlah ?? 0);
            $jumlahAmbil = min($sisaDibutuhkan, $jumlahTersedia);

            $hasilAmbil->push([
                'id_penyimpanan' => $penyimpanan['id_penyimpanan'] ?? $penyimpanan->id_penyimpanan,
                'jumlah_ambil' => $jumlahAmbil,
                'jumlah_tersedia' => $jumlahTersedia,
                'tanggal_masuk' => $penyimpanan['tanggal_masuk'] ?? ($penyimpanan->tanggal_masuk ?? null),
                'umur_simpan_hari' => $this->hitungUmurSimpan($penyimpanan),
                'id_slot' => $penyimpanan['id_slot'] ?? ($penyimpanan->id_slot ?? null),
                'kode_slot' => $penyimpanan['kode_slot'] ?? ($penyimpanan->kode_slot ?? null),
            ]);

            $sisaDibutuhkan -= $jumlahAmbil;
        }

        return $hasilAmbil;
    }

    /**
     * Hitung umur penyimpanan dalam hari
     *
     * @param array|\Illuminate\Database\Eloquent\Model $penyimpanan - Data penyimpanan
     * @return int - Umur dalam hari
     */
    public function hitungUmurSimpan(array|\Illuminate\Database\Eloquent\Model $penyimpanan): int
    {
        $tanggalMasuk = $penyimpanan['tanggal_masuk'] ?? ($penyimpanan->tanggal_masuk ?? now());
        
        if (is_string($tanggalMasuk)) {
            $tanggalMasuk = \Carbon\Carbon::parse($tanggalMasuk);
        }

        return now()->diffInDays($tanggalMasuk);
    }

    /**
     * Identifikasi gabah yang sudah terlalu lama disimpan
     * (melampaui MAX_DURASI_SIMPAN_HARI)
     *
     * @param EloquentCollection|Collection $penyimpananGabah - Collection penyimpanan gabah
     * @return Collection - Collection gabah yang tergolong terlalu lama disimpan
     */
    public function identifikasiGabahTerlamaLama(EloquentCollection|Collection $penyimpananGabah): Collection
    {
        return $penyimpananGabah->filter(function ($item) {
            $umurHari = $this->hitungUmurSimpan($item);
            return $umurHari >= self::MAX_DURASI_SIMPAN_HARI;
        })->values();
    }

    /**
     * Identifikasi gabah yang perlu warning (mendekati batas maksimal)
     * (antara WARNING_DURASI_SIMPAN_HARI hingga MAX_DURASI_SIMPAN_HARI)
     *
     * @param EloquentCollection|Collection $penyimpananGabah - Collection penyimpanan gabah
     * @return Collection - Collection gabah yang perlu warning
     */
    public function identifikasiGabahWarning(EloquentCollection|Collection $penyimpananGabah): Collection
    {
        return $penyimpananGabah->filter(function ($item) {
            $umurHari = $this->hitungUmurSimpan($item);
            return $umurHari >= self::WARNING_DURASI_SIMPAN_HARI && $umurHari < self::MAX_DURASI_SIMPAN_HARI;
        })->values();
    }

    /**
     * Dapatkan gabah yang masih dalam kondisi aman (umur simpan normal)
     *
     * @param EloquentCollection|Collection $penyimpananGabah - Collection penyimpanan gabah
     * @return Collection - Collection gabah dalam kondisi aman
     */
    public function identifikasiGabahAman(EloquentCollection|Collection $penyimpananGabah): Collection
    {
        return $penyimpananGabah->filter(function ($item) {
            $umurHari = $this->hitungUmurSimpan($item);
            return $umurHari < self::WARNING_DURASI_SIMPAN_HARI;
        })->values();
    }

    /**
     * Kategori status umur simpan
     *
     * @param int $umurHari - Umur penyimpanan dalam hari
     * @return string - Status: 'aman' | 'warning' | 'kritis'
     */
    public function kategorikanStatusUmur(int $umurHari): string
    {
        if ($umurHari >= self::MAX_DURASI_SIMPAN_HARI) {
            return 'kritis';
        }

        if ($umurHari >= self::WARNING_DURASI_SIMPAN_HARI) {
            return 'warning';
        }

        return 'aman';
    }

    /**
     * Rekomendasi prioritas pengambilan
     * Mengembalikan ranking gabah mana yang paling prioritas untuk diambil
     *
     * @param EloquentCollection|Collection $penyimpananGabah - Collection penyimpanan gabah
     * @return Collection - Collection dengan tambahan field 'prioritas' (1=tertinggi)
     */
    public function rekomendasiPrioritasPengambilan(EloquentCollection|Collection $penyimpananGabah): Collection
    {
        $denganUmur = $penyimpananGabah
            ->map(function ($item, $index) {
                $data = $item instanceof \Illuminate\Database\Eloquent\Model ? $item->toArray() : $item;
                $data['umur_simpan_hari'] = $this->hitungUmurSimpan($item);
                $data['status_umur'] = $this->kategorikanStatusUmur($data['umur_simpan_hari']);
                return $data;
            })
            ->sortByDesc('umur_simpan_hari')
            ->values();

        return $denganUmur->map(function ($item, $index) {
            $item['prioritas'] = $index + 1;
            return $item;
        });
    }

    /**
     * Rekomendasi slot untuk pengambilan berdasarkan FIFO
     * Slot mana yang harus di-prioritaskan untuk diambil
     *
     * @param EloquentCollection|Collection $slots - Collection slot dengan penyimpanan gabahnya
     * @return Collection - Collection slot dengan prioritas pengambilan
     */
    public function rekomendasiSlotPengambilan(EloquentCollection|Collection $slots): Collection
    {
        return $slots
            ->map(function ($slot) {
                $data = $slot instanceof \Illuminate\Database\Eloquent\Model ? $slot->toArray() : $slot;
                
                // Dapatkan gabah terlama di slot ini
                $gabahTerlama = $slot->penyimpananGabah()
                    ->where('status', 'tersimpan')
                    ->oldest('tanggal_masuk')
                    ->first();

                if ($gabahTerlama) {
                    $data['umur_simpan_hari'] = $this->hitungUmurSimpan($gabahTerlama);
                    $data['status_umur'] = $this->kategorikanStatusUmur($data['umur_simpan_hari']);
                    $data['tanggal_masuk_tertua'] = $gabahTerlama->tanggal_masuk;
                } else {
                    $data['umur_simpan_hari'] = 0;
                    $data['status_umur'] = 'aman';
                    $data['tanggal_masuk_tertua'] = null;
                }

                return $data;
            })
            ->sortByDesc('umur_simpan_hari')
            ->values();
    }

    /**
     * Dapatkan konfigurasi FIFO service
     *
     * @return array - Array konfigurasi: max_hari, warning_hari
     */
    public function getKonfigurasi(): array
    {
        return [
            'max_durasi_simpan_hari' => self::MAX_DURASI_SIMPAN_HARI,
            'warning_durasi_simpan_hari' => self::WARNING_DURASI_SIMPAN_HARI,
            'durasi_aman_hari' => self::WARNING_DURASI_SIMPAN_HARI,
        ];
    }

    /**
     * Update konfigurasi FIFO (jika diperlukan di masa depan)
     * Untuk sekarang hanya mengembalikan konfigurasi saat ini
     *
     * @return void
     */
    public function displayKonfigurasi(): void
    {
        echo "FIFO Configuration:\n";
        echo "- Max Durasi Simpan: " . self::MAX_DURASI_SIMPAN_HARI . " hari (6 bulan)\n";
        echo "- Warning Durasi Simpan: " . self::WARNING_DURASI_SIMPAN_HARI . " hari (4 bulan)\n";
    }
}
