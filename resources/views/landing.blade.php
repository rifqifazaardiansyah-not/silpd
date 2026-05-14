<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SILPD - Sistem Informasi Lumbung Padi</title>
    <link rel="stylesheet" href="{{ asset('css/silpd-design-system.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;330;340;480;540;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Geist', var(--font-sans);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="top-nav">
        <div class="top-nav-container">
            <a href="/" class="nav-logo">SILPD</a>
            <ul class="nav-links">
                <li><a href="#features" class="nav-link">Fitur</a></li>
                <li><a href="#about" class="nav-link">Tentang</a></li>
                <li><a href="#benefits" class="nav-link">Manfaat</a></li>
                <li><a href="#contact" class="nav-link">Kontak</a></li>
            </ul>
            <div class="nav-ctas">
                <a href="{{ route('login') }}" class="btn btn-secondary">Masuk</a>
                <a href="{{ route('login') }}" class="btn btn-primary">Mulai Gratis</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1 class="display-xl hero-title">
            Kelola Lumbung Padi Anda dengan Mudah
        </h1>
        <p class="hero-subtitle">
            SILPD adalah platform manajemen terpadu untuk petani, pengelola lumbung, dan administrator. 
            Pantau panen, kelola penyimpanan gabah, dan optimalkan hasil panen Anda dengan teknologi terkini.
        </p>
        <div class="hero-cta">
            <a href="{{ route('login') }}" class="btn btn-primary">Coba Sekarang</a>
            <a href="#features" class="btn btn-secondary">Pelajari Lebih Lanjut</a>
        </div>
    </section>

    <!-- Marquee Strip -->
    <div class="marquee-strip">
        <div class="marquee-content">
            <span>✓ Terintegrasi Penuh</span>
            <span>✓ Laporan Real-time</span>
            <span>✓ Manajemen Inventori</span>
            <span>✓ Tracking Panen</span>
            <span>✓ Keamanan Data Terjamin</span>
            <span>✓ Terintegrasi Penuh</span>
            <span>✓ Laporan Real-time</span>
        </div>
    </div>

    <!-- Color Block - Green (Lime) - Tentang Sistem -->
    <section class="color-block color-block-lime">
        <div class="color-block-content">
            <div class="eyebrow">Sistem Terintegrasi</div>
            <h2 class="color-block-title">Platform Manajemen Lumbung Padi Terpadu</h2>
            <p class="color-block-text">
                SILPD mengintegrasikan seluruh aspek manajemen lumbung padi dari pencatatan panen, 
                penyimpanan gabah, hingga distribusi. Dengan interface yang intuitif dan fitur-fitur powerful, 
                sistem ini dirancang khusus untuk kebutuhan petani Indonesia.
            </p>
            <div class="info-grid mt-lg">
                <div class="info-item">
                    <div class="info-number">3+</div>
                    <div class="info-label">Role Pengguna</div>
                </div>
                <div class="info-item">
                    <div class="info-number">15+</div>
                    <div class="info-label">Fitur Utama</div>
                </div>
                <div class="info-item">
                    <div class="info-number">100%</div>
                    <div class="info-label">Data Aman</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <h2 class="display-lg" style="text-align: center; margin-bottom: var(--space-xxl);">Fitur Unggulan</h2>
        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card">
                <div class="feature-card-icon">🌾</div>
                <h3 class="feature-card-title">Manajemen Panen</h3>
                <p class="feature-card-description">
                    Catat setiap panen dengan detail lengkap mulai dari tanggal, jumlah, hingga 
                    jenis gabah yang dipanen untuk tracking yang akurat.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card">
                <div class="feature-card-icon">📦</div>
                <h3 class="feature-card-title">Penyimpanan Gabah</h3>
                <p class="feature-card-description">
                    Kelola lokasi penyimpanan gabah di berbagai slot lumbung dengan sistem tracking 
                    real-time dan monitoring kondisi penyimpanan.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card">
                <div class="feature-card-icon">📊</div>
                <h3 class="feature-card-title">Laporan & Analitik</h3>
                <p class="feature-card-description">
                    Buat laporan komprehensif tentang produksi, penyimpanan, dan distribusi gabah 
                    dengan visualisasi data yang mudah dipahami.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card">
                <div class="feature-card-icon">👥</div>
                <h3 class="feature-card-title">Multi Role Management</h3>
                <p class="feature-card-description">
                    Dukungan penuh untuk petani, pengelola lumbung, dan administrator dengan 
                    hak akses dan fitur yang disesuaikan per role.
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="feature-card">
                <div class="feature-card-icon">🔒</div>
                <h3 class="feature-card-title">Keamanan Data</h3>
                <p class="feature-card-description">
                    Sistem keamanan berlapis dengan enkripsi password, rate limiting, dan 
                    session management yang ketat untuk melindungi data Anda.
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="feature-card">
                <div class="feature-card-icon">📱</div>
                <h3 class="feature-card-title">Responsive Design</h3>
                <p class="feature-card-description">
                    Akses dari mana saja dengan desain responsif yang sempurna di desktop, tablet, 
                    dan smartphone untuk kemudahan maksimal.
                </p>
            </div>
        </div>
    </section>

    <!-- Color Block - Lavender (Lilac) - Tentang Kelompok Tani -->
    <section class="color-block color-block-lilac">
        <div class="color-block-content">
            <div class="eyebrow">Kolaborasi & Komunitas</div>
            <h2 class="color-block-title">Dukung Kelompok Tani Anda</h2>
            <p class="color-block-text">
                SILPD memudahkan kolaborasi antar petani dalam satu kelompok tani. 
                Bagikan data panen, koordinasikan penyimpanan, dan tingkatkan efisiensi bersama.
            </p>
            <div style="margin-top: var(--space-xxl);">
                <a href="{{ route('login') }}" class="btn btn-primary">Bergabung Dengan Kelompok</a>
            </div>
        </div>
    </section>

    <!-- Color Block - Cream - Manfaat Sistem -->
    <section class="color-block color-block-cream" id="benefits">
        <div class="color-block-content">
            <div class="eyebrow">Keuntungan Bisnis</div>
            <h2 class="color-block-title">Maksimalkan Hasil Panen Anda</h2>
            <p class="color-block-text">
                Dengan data yang terorganisir dan insights yang akurat, tingkatkan produktivitas 
                lumbung padi Anda hingga 30% dan tingkat kepuasan pelanggan hingga 95%.
            </p>
            <div class="info-grid mt-lg">
                <div class="info-item">
                    <div class="info-number">30%</div>
                    <div class="info-label">Peningkatan Efisiensi</div>
                </div>
                <div class="info-item">
                    <div class="info-number">95%</div>
                    <div class="info-label">Tingkat Kepuasan</div>
                </div>
                <div class="info-item">
                    <div class="info-number">500+</div>
                    <div class="info-label">Pengguna Aktif</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Color Block - Mint - Tentang Pengguna -->
    <section class="color-block color-block-mint" id="about">
        <div class="color-block-content">
            <div class="eyebrow">Untuk Siapa</div>
            <h2 class="color-block-title">Solusi untuk Semua Pihak</h2>
            <p class="color-block-text">
                Baik Anda seorang petani individual, pengelola lumbung, atau administrator sistem, 
                SILPD menyediakan tools yang tepat untuk meningkatkan efisiensi pekerjaan Anda.
            </p>
        </div>
    </section>

    <!-- Color Block - Coral - Fitur Premium -->
    <section class="color-block color-block-coral">
        <div class="color-block-content">
            <div class="eyebrow">Fitur Unggulan</div>
            <h2 class="color-block-title">Teknologi Terdepan untuk Pertanian</h2>
            <p class="color-block-text">
                Manfaatkan teknologi terkini dengan sistem tracking real-time, notifikasi otomatis, 
                dan dashboard interaktif yang memberikan visibility penuh atas operasional lumbung padi Anda.
            </p>
        </div>
    </section>

    <!-- Color Block - Navy - Call to Action -->
    <section class="color-block color-block-navy">
        <div class="color-block-content">
            <div class="eyebrow">Mulai Sekarang</div>
            <h2 class="color-block-title">Transformasi Bisnis Lumbung Padi Anda</h2>
            <p class="color-block-text">
                Bergabunglah dengan ribuan petani dan pengelola lumbung yang telah merasakan manfaat SILPD. 
                Daftar sekarang dan dapatkan akses penuh ke semua fitur sistem kami.
            </p>
            <div style="margin-top: var(--space-xxl);">
                <a href="{{ route('login') }}" class="btn btn-primary">Mulai Gratis Sekarang</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div>
                    <h3 class="footer-column-title">Produk</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="#features">Fitur</a></li>
                        <li class="footer-link"><a href="#benefits">Manfaat</a></li>
                        <li class="footer-link"><a href="{{ route('login') }}">Dashboard</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="footer-column-title">Perusahaan</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="#about">Tentang Kami</a></li>
                        <li class="footer-link"><a href="#">Blog</a></li>
                        <li class="footer-link"><a href="#">Karir</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="footer-column-title">Dukungan</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="#">Dokumentasi</a></li>
                        <li class="footer-link"><a href="#">FAQ</a></li>
                        <li class="footer-link"><a href="#contact">Hubungi Kami</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="footer-column-title">Legal</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="#">Privasi</a></li>
                        <li class="footer-link"><a href="#">Syarat Layanan</a></li>
                        <li class="footer-link"><a href="#">Kebijakan</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 SILPD - Sistem Informasi Lumbung Padi. Semua hak dilindungi.</p>
                <p>Dibuat dengan 🌾 untuk petani Indonesia</p>
            </div>
        </div>
    </footer>
</body>
</html>
