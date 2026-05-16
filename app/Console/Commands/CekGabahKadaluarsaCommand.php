<?php

namespace App\Console\Commands;

use App\Models\PenyimpananGabah;
use App\Services\FifoService;
use App\Services\NotifikasiService;
use Illuminate\Console\Command;

class CekGabahKadaluarsaCommand extends Command
{
    /**
     * Nama dan deskripsi command.
     */
    protected $signature = 'check:expired-grain';
    protected $description = 'Cek gabah yang sudah kadaluarsa atau mendekati batas simpan dan trigger notifikasi';

    /**
     * Execute command.
     */
    public function handle(): int
    {
        $this->info('🔍 Memulai cek gabah kadaluarsa...');

        try {
            $fifoService = new FifoService();
            $notifikasiService = new NotifikasiService();

            // Query gabah yang masih tersimpan
            $gabahTersimpan = PenyimpananGabah::where('status', 'tersimpan')
                ->with(['detailPanen', 'slotLumbung'])
                ->get();

            $countExpired = 0;
            $countWarning = 0;
            $countAman = 0;

            foreach ($gabahTersimpan as $penyimpanan) {
                // Hitung umur simpan dalam hari
                $umurHari = $fifoService->hitungUmurSimpan($penyimpanan->tanggal_masuk);

                // Kategorikan status umur
                $status = $fifoService->kategorikanStatusUmur($umurHari);

                // Jika sudah kadaluarsa (melebihi 180 hari)
                if ($status === 'kritis') {
                    $notifikasiService->buatNotifikasiGabahExpired(
                        $penyimpanan->detailPanen,
                        $penyimpanan->slotLumbung,
                        $penyimpanan,
                        $umurHari
                    );
                    $countExpired++;
                    $this->warn("⚠️  KRITIS: Gabah ID {$penyimpanan->id_penyimpanan} sudah {$umurHari} hari tersimpan");
                }
                // Jika warning (120-180 hari)
                elseif ($status === 'warning') {
                    $notifikasiService->buatNotifikasiGabahWarning(
                        $penyimpanan->detailPanen,
                        $penyimpanan->slotLumbung,
                        $penyimpanan,
                        $umurHari
                    );
                    $countWarning++;
                    $this->line("⚡ WARNING: Gabah ID {$penyimpanan->id_penyimpanan} sudah {$umurHari} hari tersimpan");
                }
                // Jika masih aman
                else {
                    $countAman++;
                }
            }

            // Summary
            $this->newLine();
            $this->info('✅ Cek gabah kadaluarsa selesai!');
            $this->line("Status Aman: {$countAman} unit");
            $this->line("Status Warning: {$countWarning} unit");
            $this->error("Status Kritis/Kadaluarsa: {$countExpired} unit");

            if ($countExpired > 0) {
                $this->error("❌ Ada {$countExpired} gabah yang HARUS SEGERA DIAMBIL!");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            \Log::error('CekGabahKadaluarsaCommand error: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
