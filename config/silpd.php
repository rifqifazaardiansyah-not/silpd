<?php

/**
 * Konfigurasi SILPD (Sistem Informasi Lumbung Padi Desa)
 *
 * Sistem manajemen pencatatan dan pengelolaan lumbung pangan desa
 * untuk ketahanan pangan dan mengurangi ketergantungan impor beras nasional.
 */

return [
    // ===== INFORMASI APLIKASI =====
    'nama_aplikasi' => 'SILPD',
    'nama_lengkap' => 'Sistem Informasi Lumbung Padi Desa',
    'versi' => '1.0.0',
    'deskripsi' => 'Platform digital untuk manajemen lumbung padi desa dengan sistem FIFO dan monitoring stok real-time',

    // ===== TUJUAN SISTEM =====
    'tujuan' => [
        'pencatatan_hasil_panen',
        'pengelolaan_stok_gabah_desa',
        'manajemen_penyimpanan_lumbung',
        'monitoring_kapasitas_penyimpanan',
        'pengambilan_gabah_saat_dibutuhkan',
        'mendukung_ketahanan_pangan_masyarakat_desa',
    ],

    // ===== PERSENTASE PENYIMPANAN =====
    'persen_lumbung' => 3, // 3% dari setiap panen disimpan di lumbung

    // ===== KONSTANTA GABAH & PENYIMPANAN =====
    'penyimpanan' => [
        // Batas durasi penyimpanan (dari FifoService)
        'max_durasi_simpan_hari' => 180,      // Gabah > 180 hari HARUS diambil (KRITIS)
        'warning_durasi_simpan_hari' => 120,  // Gabah 120-180 hari WARNING
        'batas_aman_hari' => 119,             // Gabah < 119 hari masih AMAN

        // Threshold kapasitas lumbung
        'kapasitas_warning_persen' => 80,     // Warning jika lumbung > 80%
        'kapasitas_penuh_persen' => 95,       // Lumbung dianggap PENUH di > 95%
        'min_kapasitas_threshold_persen' => 5, // Alert jika slot < 5% dari kapasitas (dari TentukanSlotService)
    ],

    // ===== ROLE & AKSES =====
    'roles' => [
        'admin' => 'Admin Desa',
        'pengelola' => 'Pengelola Lumbung',
        'petani' => 'Petani',
    ],

    'permissions' => [
        'admin' => [
            'menginput_data_panen',
            'mengelola_data_petani',
            'mengelola_akun_pengguna',
            'memvalidasi_permintaan_pengambilan',
            'monitoring_dashboard',
            'menerima_notifikasi_sistem',
            'generate_laporan',
            'manajemen_lumbung',
        ],
        'pengelola' => [
            'menerima_instruksi_penyimpanan',
            'menyimpan_gabah_ke_slot',
            'melakukan_konfirmasi_penyimpanan',
            'melakukan_pengeluaran_gabah',
            'menerima_notifikasi_fifo_dan_kapasitas',
            'melihat_stok_lumbung',
        ],
        'petani' => [
            'login_ke_sistem',
            'melihat_stok_gabah_miliknya',
            'melihat_status_penyimpanan',
            'mengajukan_permintaan_pengambilan',
        ],
    ],

    // ===== MEKANISME PENYIMPANAN =====
    'mekanisme_penyimpanan' => [
        'deskripsi' => 'Gabah disimpan berdasarkan jenis, batch panen, dan tanggal masuk untuk mencegah pencampuran dan mempermudah FIFO',
        'tujuan_slot' => [
            'mencegah_pencampuran_gabah',
            'mempermudah_fifo',
            'mempermudah_monitoring_umur_simpan',
        ],
    ],

    // ===== KONSEP FIFO (First In First Out) =====
    'fifo' => [
        'deskripsi' => 'First In First Out - Gabah yang paling lama disimpan dikeluarkan terlebih dahulu',
        'tujuan' => [
            'menjaga_kualitas_gabah',
            'mencegah_gabah_rusak_atau_busuk',
            'sistem_rotasi_stok_yang_teratur',
        ],
        'status_umur' => [
            'aman' => 'Gabah < 120 hari',
            'warning' => 'Gabah 120-180 hari',
            'kritis' => 'Gabah > 180 hari (HARUS DIAMBIL)',
        ],
    ],

    // ===== NOTIFIKASI SISTEM =====
    'notifikasi' => [
        'tipe' => [
            'FIFO_PRIORITY' => 'Prioritas FIFO - gabah lama harus diambil',
            'LUMBUNG_PENUH' => 'Lumbung sudah penuh (> 95%)',
            'SLOT_PENUH' => 'Slot lumbung sudah penuh',
            'GABAH_EXPIRED' => 'Gabah sudah kadaluarsa (> 180 hari)',
            'GABAH_WARNING' => 'Gabah mendekati batas simpan (120-180 hari)',
            'PANEN_BARU' => 'Panen baru dicatat',
            'INSTRUKSI_SIMPAN' => 'Instruksi penyimpanan baru',
            'PERMINTAAN_PENGAMBILAN' => 'Permintaan pengambilan gabah',
            'PENYIMPANAN_SELESAI' => 'Proses penyimpanan selesai',
            'PENGAMBILAN_SELESAI' => 'Proses pengambilan selesai',
            'KAPASITAS_RENDAH' => 'Kapasitas lumbung rendah',
        ],
        'prioritas' => [
            'CRITICAL' => 'Kritis - tindakan segera diperlukan',
            'HIGH' => 'Tinggi - perlu perhatian dalam 24 jam',
            'MEDIUM' => 'Sedang - perhatian dalam beberapa hari',
            'LOW' => 'Rendah - informasi saja',
        ],
    ],

    // ===== AUTHENTIKASI =====
    'auth' => [
        'driver' => 'session',
        'hash_algo' => 'argon2id',
        'hash_memory' => 65536,
        'hash_time' => 4,
        'hash_threads' => 1,
        'session_timeout_menit' => 120,
        'max_login_attempts' => 5,
        'lockout_durasi_detik' => 60,
    ],

    // ===== DATABASE =====
    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', 3306),
        'database' => env('DB_DATABASE', 'silpd_db'),
    ],

    // ===== LOGGING =====
    'logging' => [
        'channel' => 'stack',
        'level' => 'debug',
        'log_path' => storage_path('logs/silpd.log'),
    ],

    // ===== REKOMENDASI PENGAMBILAN GABAH =====
    'rekomendasi_pengambilan' => [
        'prioritas_fifo' => [
            1 => 'Gabah paling lama disimpan (First In)',
            2 => 'Jenis gabah yang diminta petani',
            3 => 'Slot dengan stok mencukupi',
        ],
    ],

    // ===== PENGAMBILAN GABAH =====
    'pengambilan_gabah' => [
        'kondisi_normal' => 'Petani mengalami gagal panen atau kondisi darurat',
        'kondisi_khusus' => 'Gabah dapat digunakan untuk kebutuhan desa atas persetujuan petani',
    ],

    // ===== STATUS PANEN =====
    'status_panen' => [
        'draft' => 'Panen baru dibuat, belum diproses',
        'terproses' => 'Panen sudah diproses dan dibuat instruksi penyimpanan',
        'selesai' => 'Semua instruksi penyimpanan sudah dikonfirmasi pengelola',
    ],

    // ===== STATUS INSTRUKSI PENYIMPANAN =====
    'status_instruksi_penyimpanan' => [
        'pending' => 'Instruksi baru, menunggu konfirmasi pengelola',
        'dikonfirmasi' => 'Pengelola sudah menerima instruksi',
        'dibatalkan' => 'Instruksi dibatalkan oleh admin',
    ],

    // ===== STATUS PERMINTAAN PENGAMBILAN =====
    'status_permintaan_pengambilan' => [
        'pending' => 'Permintaan baru, menunggu validasi admin',
        'divalidasi' => 'Admin sudah memvalidasi, siap dikeluarkan',
        'ditolak' => 'Admin menolak permintaan',
        'dibatalkan' => 'Petani membatalkan permintaan',
        'selesai' => 'Pengelola sudah mengeluarkan gabah',
    ],

    // ===== STATUS DETAIL PENGAMBILAN =====
    'status_detail_pengambilan' => [
        'pending' => 'Menunggu pengeluaran',
        'diambil' => 'Sudah dikeluarkan dari lumbung',
    ],
];
