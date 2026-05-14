<?php

/**
 * Konfigurasi SILPD (Sistem Informasi Lumbung Padi Desa)
 *
 * Sistem manajemen pencatatan dan pengelolaan lumbung pangan desa
 * untuk ketahanan pangan dan mengurangi ketergantungan impor beras nasional.
 */

return [
    // ===== APLIKASI =====
    'nama_aplikasi' => 'SILPD',
    'nama_lengkap' => 'Sistem Informasi Lumbung Padi Desa',
    'versi' => '1.0.0',

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
    'persentase_penyimpanan' => 3, // 3% dari setiap panen disimpan di lumbung

    // ===== MONITORING GABAH =====
    'monitoring' => [
        'umur_simpan_maksimal_hari' => 90, // Gabah > 90 hari akan mendapat notifikasi
        'kapasitas_lumbung_warning' => 80, // Warning jika lumbung sudah mencapai 80%
        'kapasitas_lumbung_penuh' => 95,   // Lumbung dianggap penuh di 95%
    ],

    // ===== ROLE & AKSES =====
    'roles' => [
        'admin' => 'Admin Desa',
        'pengelola' => 'Pengelola Lumbung',
        'petani' => 'Petani',
    ],

    'admin_tasks' => [
        'menginput_data_panen',
        'mengelola_data_petani',
        'mengelola_akun_pengguna',
        'memvalidasi_permintaan_pengambilan',
        'monitoring_dashboard',
        'menerima_notifikasi_sistem',
    ],

    'pengelola_tasks' => [
        'menerima_instruksi_penyimpanan',
        'menyimpan_gabah_ke_slot',
        'melakukan_konfirmasi_penyimpanan',
        'melakukan_pengeluaran_gabah',
        'menerima_notifikasi_fifo_dan_kapasitas',
    ],

    'petani_fitur' => [
        'login_ke_sistem',
        'melihat_stok_gabah_miliknya',
        'melihat_status_penyimpanan',
        'mengajukan_permintaan_pengambilan',
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

    // ===== KONSEP FIFO =====
    'fifo' => [
        'deskripsi' => 'First In First Out - Gabah yang paling lama disimpan dikeluarkan terlebih dahulu',
        'prioritas_pengambilan' => [
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
