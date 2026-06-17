-- =====================================================================
-- SILPD Database Schema for PostgreSQL (Supabase)
-- Generated from Laravel Migrations
-- =====================================================================

-- Table: kelompok_tani
CREATE TABLE kelompok_tani (
    id_kelompok BIGSERIAL PRIMARY KEY,
    nama_kelompok VARCHAR(100) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Table: petani
CREATE TABLE petani (
    id_petani BIGSERIAL PRIMARY KEY,
    id_kelompok BIGINT NOT NULL,
    nama_petani VARCHAR(100) NOT NULL,
    luas_lahan DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_kelompok) REFERENCES kelompok_tani(id_kelompok) ON DELETE CASCADE
);

-- Table: jenis_gabah
CREATE TABLE jenis_gabah (
    id_jenis_gabah BIGSERIAL PRIMARY KEY,
    nama_jenis VARCHAR(100) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Table: panen
CREATE TABLE panen (
    id_panen BIGSERIAL PRIMARY KEY,
    id_petani BIGINT NOT NULL,
    tanggal_panen DATE NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_petani) REFERENCES petani(id_petani) ON DELETE CASCADE
);

-- Table: detail_panen
CREATE TABLE detail_panen (
    id_detail BIGSERIAL PRIMARY KEY,
    id_panen BIGINT NOT NULL,
    id_jenis_gabah BIGINT NOT NULL,
    jumlah_panen DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_panen) REFERENCES panen(id_panen) ON DELETE CASCADE,
    FOREIGN KEY (id_jenis_gabah) REFERENCES jenis_gabah(id_jenis_gabah) ON DELETE CASCADE
);

-- Table: lumbung
CREATE TABLE lumbung (
    id_lumbung BIGSERIAL PRIMARY KEY,
    nama_lumbung VARCHAR(100) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Table: slot_lumbung
CREATE TABLE slot_lumbung (
    id_slot BIGSERIAL PRIMARY KEY,
    id_lumbung BIGINT NOT NULL,
    kode_slot VARCHAR(20) NOT NULL,
    kapasitas DECIMAL(10, 2) NOT NULL,
    kapasitas_tersedia DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_lumbung) REFERENCES lumbung(id_lumbung) ON DELETE CASCADE
);

-- Table: penyimpanan_gabah
CREATE TABLE penyimpanan_gabah (
    id_penyimpanan BIGSERIAL PRIMARY KEY,
    id_detail BIGINT NOT NULL,
    id_instruksi BIGINT,
    jumlah_masuk DECIMAL(10, 2),
    id_slot BIGINT NOT NULL,
    jumlah DECIMAL(10, 2) NOT NULL,
    tanggal_masuk DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'tersimpan' CHECK (status IN ('tersimpan', 'diambil', 'habis')),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_detail) REFERENCES detail_panen(id_detail) ON DELETE CASCADE,
    FOREIGN KEY (id_slot) REFERENCES slot_lumbung(id_slot) ON DELETE CASCADE
);

-- Table: instruksi_penyimpanan
CREATE TABLE instruksi_penyimpanan (
    id_instruksi BIGSERIAL PRIMARY KEY,
    id_detail BIGINT NOT NULL,
    id_slot BIGINT NOT NULL,
    jumlah DECIMAL(10, 2) NOT NULL,
    tanggal_instruksi DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'selesai')),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_detail) REFERENCES detail_panen(id_detail) ON DELETE CASCADE,
    FOREIGN KEY (id_slot) REFERENCES slot_lumbung(id_slot) ON DELETE CASCADE
);

-- Add foreign key for id_instruksi in penyimpanan_gabah
ALTER TABLE penyimpanan_gabah 
ADD FOREIGN KEY (id_instruksi) REFERENCES instruksi_penyimpanan(id_instruksi) ON DELETE SET NULL;

-- Table: permintaan_pengambilan
CREATE TABLE permintaan_pengambilan (
    id_permintaan BIGSERIAL PRIMARY KEY,
    id_petani BIGINT NOT NULL,
    id_penyimpanan BIGINT NOT NULL,
    tanggal_permintaan DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'disetujui', 'ditolak', 'selesai')),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_petani) REFERENCES petani(id_petani) ON DELETE CASCADE,
    FOREIGN KEY (id_penyimpanan) REFERENCES penyimpanan_gabah(id_penyimpanan) ON DELETE CASCADE
);

-- Table: detail_pengambilan
CREATE TABLE detail_pengambilan (
    id_detail_ambil BIGSERIAL PRIMARY KEY,
    id_permintaan BIGINT NOT NULL,
    id_penyimpanan BIGINT NOT NULL,
    jumlah DECIMAL(10, 2) NOT NULL,
    alasan TEXT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_permintaan) REFERENCES permintaan_pengambilan(id_permintaan) ON DELETE CASCADE,
    FOREIGN KEY (id_penyimpanan) REFERENCES penyimpanan_gabah(id_penyimpanan) ON DELETE CASCADE
);

-- Table: pengelola
CREATE TABLE pengelola (
    id_pengelola BIGSERIAL PRIMARY KEY,
    nama_pengelola VARCHAR(100) NOT NULL,
    no_hp VARCHAR(15) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Table: admin
CREATE TABLE admin (
    id_admin BIGSERIAL PRIMARY KEY,
    nama_admin VARCHAR(100) NOT NULL,
    jabatan VARCHAR(50) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Table: login
CREATE TABLE login (
    id_login BIGSERIAL PRIMARY KEY,
    id_petani BIGINT,
    id_pengelola BIGINT,
    id_admin BIGINT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL CHECK (role IN ('petani', 'pengelola', 'admin')),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_petani) REFERENCES petani(id_petani) ON DELETE CASCADE,
    FOREIGN KEY (id_pengelola) REFERENCES pengelola(id_pengelola) ON DELETE CASCADE,
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin) ON DELETE CASCADE
);

-- Table: lumbung_pengelola (many-to-many pivot table)
CREATE TABLE lumbung_pengelola (
    id_lumbung_pengelola BIGSERIAL PRIMARY KEY,
    id_lumbung BIGINT NOT NULL,
    id_pengelola BIGINT NOT NULL,
    peran VARCHAR(20) DEFAULT 'anggota' CHECK (peran IN ('pemilik_akun', 'anggota')),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_lumbung) REFERENCES lumbung(id_lumbung) ON DELETE CASCADE,
    FOREIGN KEY (id_pengelola) REFERENCES pengelola(id_pengelola) ON DELETE CASCADE,
    UNIQUE (id_lumbung, id_pengelola)
);

-- Create indexes for better query performance
CREATE INDEX idx_petani_kelompok ON petani(id_kelompok);
CREATE INDEX idx_panen_petani ON panen(id_petani);
CREATE INDEX idx_detail_panen_panen ON detail_panen(id_panen);
CREATE INDEX idx_detail_panen_jenis ON detail_panen(id_jenis_gabah);
CREATE INDEX idx_slot_lumbung ON slot_lumbung(id_lumbung);
CREATE INDEX idx_penyimpanan_detail ON penyimpanan_gabah(id_detail);
CREATE INDEX idx_penyimpanan_slot ON penyimpanan_gabah(id_slot);
CREATE INDEX idx_penyimpanan_instruksi ON penyimpanan_gabah(id_instruksi);
CREATE INDEX idx_instruksi_detail ON instruksi_penyimpanan(id_detail);
CREATE INDEX idx_instruksi_slot ON instruksi_penyimpanan(id_slot);
CREATE INDEX idx_permintaan_petani ON permintaan_pengambilan(id_petani);
CREATE INDEX idx_permintaan_penyimpanan ON permintaan_pengambilan(id_penyimpanan);
CREATE INDEX idx_detail_ambil_permintaan ON detail_pengambilan(id_permintaan);
CREATE INDEX idx_detail_ambil_penyimpanan ON detail_pengambilan(id_penyimpanan);
CREATE INDEX idx_login_username ON login(username);
CREATE INDEX idx_lumbung_pengelola_lumbung ON lumbung_pengelola(id_lumbung);
CREATE INDEX idx_lumbung_pengelola_pengelola ON lumbung_pengelola(id_pengelola);

-- Comments for documentation
COMMENT ON TABLE kelompok_tani IS 'Kelompok tani yang mengelola petani';
COMMENT ON TABLE petani IS 'Data petani yang tergabung dalam kelompok tani';
COMMENT ON TABLE jenis_gabah IS 'Jenis-jenis gabah yang bisa disimpan';
COMMENT ON TABLE panen IS 'Data hasil panen petani';
COMMENT ON TABLE detail_panen IS 'Detail jenis dan jumlah gabah per panen';
COMMENT ON TABLE lumbung IS 'Data lumbung penyimpanan';
COMMENT ON TABLE slot_lumbung IS 'Slot penyimpanan dalam lumbung';
COMMENT ON TABLE penyimpanan_gabah IS 'Data penyimpanan gabah di slot tertentu';
COMMENT ON TABLE instruksi_penyimpanan IS 'Instruksi penyimpanan gabah ke slot';
COMMENT ON TABLE permintaan_pengambilan IS 'Permintaan pengambilan gabah oleh petani';
COMMENT ON TABLE detail_pengambilan IS 'Detail pengambilan gabah';
COMMENT ON TABLE pengelola IS 'Data pengelola lumbung';
COMMENT ON TABLE admin IS 'Data admin sistem';
COMMENT ON TABLE login IS 'Data login untuk semua role (petani, pengelola, admin)';
COMMENT ON TABLE lumbung_pengelola IS 'Relasi many-to-many antara lumbung dan pengelola';
