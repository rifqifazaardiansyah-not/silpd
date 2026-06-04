# SILPD Design System
## Sistem Informasi Lumbung Padi Desa — UI/UX Guidelines for Laravel Blade + Tailwind CSS

---

## 1. Visual Philosophy

SILPD adalah sistem operasional desa, bukan aplikasi konsumer. Desainnya harus terasa **tepercaya, bersih, dan fungsional** — seperti dokumen administrasi yang didigitalkan dengan baik, bukan dashboard startup.

Pendekatan: **Minimalism as clarity**. Setiap elemen visual harus menjawab pertanyaan: *"Apakah ini membantu pengelola desa membuat keputusan lebih cepat?"* Jika tidak, hapus.

**Karakteristik utama:**
- Canvas putih bersih (`#ffffff`) dengan teks near-black (`#171717`)
- Warna aksen hanya untuk status dan role — bukan dekorasi
- Shadow-as-border: border digantikan `box-shadow` berlapis untuk kedalaman halus
- Tipografi rapi dengan negative letter-spacing di heading
- Tabel dan form adalah warga kelas satu — harus sempurna dibaca

---

## 2. Color Palette

### Base
| Token | Value | Penggunaan |
|---|---|---|
| `--color-base` | `#ffffff` | Background halaman |
| `--color-surface` | `#fafafa` | Background card, tabel baris genap |
| `--color-border` | `rgba(0,0,0,0.08)` | Border via shadow |
| `--color-text-primary` | `#171717` | Teks utama |
| `--color-text-secondary` | `#6b7280` | Label, caption, placeholder |
| `--color-text-tertiary` | `#9ca3af` | Teks disabled, hint |

### Role Colors (digunakan di sidebar, badge, accent)
| Role | Primary | Light BG | Usage |
|---|---|---|---|
| **Admin** | `#4f46e5` (indigo-600) | `#eef2ff` (indigo-50) | Sidebar admin, badge admin |
| **Pengelola** | `#059669` (emerald-600) | `#ecfdf5` (emerald-50) | Sidebar pengelola, badge pengelola |
| **Petani** | `#d97706` (amber-600) | `#fffbeb` (amber-50) | Sidebar petani, badge petani |

### Status Colors (gabah & permintaan)
| Status | Color | Tailwind Class | Background |
|---|---|---|---|
| `tersimpan` | `#059669` | `text-emerald-600` | `bg-emerald-50` |
| `pending` | `#d97706` | `text-amber-600` | `bg-amber-50` |
| `disetujui` | `#4f46e5` | `text-indigo-600` | `bg-indigo-50` |
| `ditolak` | `#dc2626` | `text-red-600` | `bg-red-50` |
| `selesai` | `#6b7280` | `text-gray-500` | `bg-gray-100` |
| `habis` | `#9ca3af` | `text-gray-400` | `bg-gray-50` |
| `kadaluarsa` | `#dc2626` | `text-red-600` | `bg-red-50` |
| `hampir_penuh` | `#ea580c` | `text-orange-600` | `bg-orange-50` |

### Notification / Alert Colors
| Type | Border | BG | Text |
|---|---|---|---|
| `success` | `#059669` | `#ecfdf5` | `#065f46` |
| `warning` | `#d97706` | `#fffbeb` | `#92400e` |
| `error` | `#dc2626` | `#fef2f2` | `#991b1b` |
| `info` | `#4f46e5` | `#eef2ff` | `#3730a3` |

---

## 3. Typography

### Font Stack
```css
font-family: 'Inter', system-ui, -apple-system, sans-serif;
font-feature-settings: "liga" 1, "cv02" 1, "cv03" 1, "cv04" 1;
```
Gunakan Inter dari Google Fonts. Jika tidak tersedia, system-ui sebagai fallback.

### Scale
| Role | Size | Weight | Letter-spacing | Tailwind |
|---|---|---|---|---|
| Page title | 24px | 600 | -0.48px | `text-2xl font-semibold tracking-tight` |
| Section heading | 18px | 600 | -0.27px | `text-lg font-semibold tracking-tight` |
| Card title | 14px | 500 | -0.14px | `text-sm font-medium` |
| Body | 14px | 400 | 0 | `text-sm` |
| Caption/Label | 12px | 400 | 0 | `text-xs` |
| Badge/Tag | 11px | 500 | 0.11px | `text-[11px] font-medium tracking-wide` |
| Table header | 11px | 600 | 0.55px | `text-[11px] font-semibold uppercase tracking-wider` |
| Monospace (kode/ID) | 13px | 400 | 0 | `text-[13px] font-mono` |

### Rules
- **Heading harus `tracking-tight`** — jangan beri letter-spacing positif
- **Angka dalam tabel** gunakan `tabular-nums` (`font-variant-numeric: tabular-nums`)
- **Jumlah kg** format ribuan dengan titik: `1.200 kg` bukan `1200 kg`
- **Tanggal** format: `12 Mar 2025` — bukan ISO, bukan slash

---

## 4. Spacing & Layout

### Grid System
- Layout utama: **sidebar + main content** — sidebar `w-64` (256px) fixed
- Max content width: `max-w-7xl mx-auto px-6`
- Card padding: `p-6`
- Table cell padding: `px-4 py-3`
- Form group gap: `space-y-5`
- Section gap: `space-y-6`

### Sidebar
```
Sidebar width: 256px (w-64)
Sidebar BG: Role primary color (indigo/emerald/amber)
Nav item padding: px-3 py-2
Nav item radius: rounded-lg
Active item: bg-white/20 text-white font-medium
Inactive item: text-white/70 hover:bg-white/10
```

### Page Header Pattern
Setiap halaman punya header dengan breadcrumb + judul + action button:
```
[Breadcrumb] Dashboard / Petani / Tambah
[H1] Tambah Petani Baru                    [Button: Kembali]
[Subtitle] Isi data petani dan akun login opsional
```

---

## 5. Components

### Cards
```html
<!-- Standard card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
  <!-- Header -->
  <div class="px-6 py-4 border-b border-gray-100">
    <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Judul Card</h3>
  </div>
  <!-- Body -->
  <div class="p-6">...</div>
</div>
```

Gunakan `shadow-sm` + `border border-gray-200` sebagai pengganti shadow-as-border bawaan Vercel,
karena Blade tidak perlu kedalaman layer yang sama dengan SPA.

### Stat Cards (untuk Dashboard)
```html
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
  <div class="flex items-center justify-between">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Petani</p>
    <span class="p-2 bg-indigo-50 rounded-lg">
      <!-- Icon SVG -->
    </span>
  </div>
  <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">24</p>
  <p class="mt-1 text-xs text-gray-500">3 kelompok tani aktif</p>
</div>
```

### Tabel
```html
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
  <table class="w-full">
    <thead>
      <tr class="border-b border-gray-200 bg-gray-50">
        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">
          Nama Petani
        </th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      <tr class="hover:bg-gray-50 transition-colors">
        <td class="px-4 py-3 text-sm text-gray-900">Slamet Riyadi</td>
      </tr>
    </tbody>
  </table>
</div>
```

### Status Badge
```html
<!-- tersimpan -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">
  Tersimpan
</span>

<!-- pending -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">
  Pending
</span>

<!-- ditolak -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700">
  Ditolak
</span>
```

Buat Blade component `<x-status-badge :status="$item->status" />` untuk reusability.

### Capacity Bar (Progress Bar Kapasitas Slot)
```html
<div class="w-full">
  <div class="flex justify-between text-xs text-gray-500 mb-1">
    <span>Terpakai</span>
    <span>{{ $persenTerpakai }}%</span>
  </div>
  <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
    <div
      class="h-full rounded-full transition-all {{ $persenTerpakai >= 80 ? 'bg-red-500' : ($persenTerpakai >= 60 ? 'bg-amber-400' : 'bg-emerald-500') }}"
      style="width: {{ $persenTerpakai }}%"
    ></div>
  </div>
</div>
```

### Form Controls
```html
<!-- Text Input -->
<div>
  <label for="nama" class="block text-sm font-medium text-gray-700 mb-1.5">
    Nama Petani <span class="text-red-500">*</span>
  </label>
  <input
    type="text"
    id="nama"
    name="nama_petani"
    value="{{ old('nama_petani') }}"
    placeholder="Masukkan nama lengkap…"
    class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg
           text-gray-900 placeholder-gray-400
           focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
           transition-colors"
  >
  @error('nama_petani')
    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
  @enderror
</div>

<!-- Select -->
<select class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg
               text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500
               focus:border-transparent transition-colors">
  <option value="">Pilih kelompok tani…</option>
</select>
```

**Rules:**
- Font size input MINIMAL `text-sm` (14px) — jangan lebih kecil
- Jangan blokir paste (`onpaste`, `oncopy`)
- Label selalu ada — jangan rely hanya pada placeholder
- Error message warna `text-red-600` ukuran `text-xs` di bawah input

### Buttons
```html
<!-- Primary -->
<button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600
  text-white text-sm font-medium rounded-lg hover:bg-indigo-700
  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
  disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
  Simpan Data
</button>

<!-- Secondary -->
<a href="{{ route('admin.petani.index') }}" class="inline-flex items-center gap-2 px-4 py-2
  bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg
  hover:bg-gray-50 transition-colors">
  Kembali
</a>

<!-- Danger -->
<button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600
  text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
  Hapus
</button>
```

**Rules:**
- Gunakan `<a>` untuk navigasi, `<button>` untuk aksi form
- Tombol hapus HARUS pakai konfirmasi `onclick="return confirm('...')"` atau modal
- Tombol primary warna mengikuti role: admin=indigo, pengelola=emerald, petani=amber

### Alert / Flash Messages
```html
@if(session('success'))
<div class="flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm">
  <!-- CheckCircle icon -->
  <span>{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="p-4 bg-red-50 border border-red-200 rounded-lg">
  <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
    @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif
```

### Pagination
```html
<div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
  <p class="text-xs text-gray-500">
    Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} data
  </p>
  {{ $items->withQueryString()->links('vendor.pagination.tailwind') }}
</div>
```

---

## 6. Layout Structure per Role

### Admin Layout (`layouts/admin.blade.php`)
```
┌─────────────────────────────────────────────┐
│  SIDEBAR (indigo-600, w-64, fixed)          │
│  ┌─────────────────────────────────────┐    │
│  │ 🌾 SILPD                            │    │
│  │ Admin Desa                          │    │
│  ├─────────────────────────────────────┤    │
│  │ Dashboard                           │    │
│  │ ── Data Master ──                   │    │
│  │   Petani                            │    │
│  │   Kelompok Tani                     │    │
│  │   Jenis Gabah                       │    │
│  │   Lumbung & Slot                    │    │
│  │   Pengelola & Peran                 │    │
│  │ ── Operasional ──                   │    │
│  │   Input Panen                       │    │
│  │   Instruksi Simpan                  │    │
│  │   Permintaan Ambil                  │    │
│  │   Manajemen Akun                    │    │
│  │ ── Laporan ──                       │    │
│  │   Stok Gabah                        │    │
│  │   Laporan Panen                     │    │
│  │   Laporan Pengambilan               │    │
│  │   Rekap Petani                      │    │
│  └─────────────────────────────────────┘    │
│                                              │
│  MAIN CONTENT (ml-64, bg-gray-50)           │
│  ┌─────────────────────────────────────┐    │
│  │  TOP BAR: breadcrumb + user info    │    │
│  ├─────────────────────────────────────┤    │
│  │  PAGE CONTENT (@yield('content'))   │    │
│  └─────────────────────────────────────┘    │
└─────────────────────────────────────────────┘
```

### Pengelola Layout (`layouts/pengelola.blade.php`)
Sidebar warna `emerald-600`. Menu:
- Dashboard
- Instruksi Penyimpanan
- Pengeluaran Gabah
- Monitor Stok

### Petani Layout (`layouts/petani.blade.php`)
Sidebar warna `amber-600`. Menu:
- Dashboard
- Stok Gabah Saya
- Permintaan Pengambilan

---

## 7. Page-Specific Patterns

### Dashboard Cards Order
1. Stat cards (4 kolom) — angka besar
2. Alert/notifikasi sistem (jika ada)
3. Tabel pending actions (permintaan/instruksi)
4. Ringkasan kapasitas lumbung

### Index/List Pages
```
[Page Header: Judul + Tombol Tambah]
[Filter bar: search input + dropdown filter + tombol reset]
[Table: header + rows + pagination]
```

### Create/Edit Pages
```
[Page Header: Judul + Tombol Kembali]
[Flash messages]
[Form card: field-field + tombol submit]
```

### Show/Detail Pages
```
[Page Header: Judul + Tombol Edit + Tombol Kembali]
[Stat mini cards (jika ada)]
[Detail card: label-value pairs]
[Related data tables]
```

### Pengelola di Lumbung (Many-to-Many)
Relasi pengelola ↔ lumbung bersifat **many-to-many** melalui tabel pivot `lumbung_pengelola`.
Satu lumbung bisa memiliki banyak pengelola, dan satu pengelola bisa mengelola banyak lumbung.

Kolom pivot `peran` bernilai:
- `pemilik_akun` — penanggungjawab utama lumbung
- `anggota` — pengelola pembantu

**Cara render daftar pengelola di halaman lumbung:**
```html
{{-- Loop pengelola via pivot --}}
@foreach($lumbung->pengelola as $pengelola)
  <div class="flex items-center justify-between py-2">
    <div>
      <p class="text-sm font-medium text-gray-900">{{ $pengelola->nama_pengelola }}</p>
      <p class="text-xs text-gray-500">{{ $pengelola->no_hp }}</p>
    </div>
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium
      {{ $pengelola->pivot->peran === 'pemilik_akun' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
      {{ $pengelola->pivot->peran === 'pemilik_akun' ? 'Pemilik Akun' : 'Anggota' }}
    </span>
  </div>
@endforeach
```

**Cara render lumbung yang dikelola di halaman pengelola:**
```html
@foreach($pengelola->lumbung as $lumbung)
  <tr>
    <td>{{ $lumbung->nama_lumbung }}</td>
    <td>
      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium
        {{ $lumbung->pivot->peran === 'pemilik_akun' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
        {{ $lumbung->pivot->peran === 'pemilik_akun' ? 'Pemilik Akun' : 'Anggota' }}
      </span>
    </td>
  </tr>
@endforeach
```

### FIFO Warning Display
Jika ada pelanggaran FIFO (gabah lebih lama ada tapi tidak dipilih petani):
```html
<div class="p-4 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3">
  <!-- ExclamationTriangle icon amber-500 -->
  <div>
    <p class="text-sm font-medium text-amber-800">Rekomendasi FIFO</p>
    <p class="text-sm text-amber-700 mt-0.5">
      Ada gabah jenis yang sama yang lebih lama tersimpan. Disarankan ambil dari lot yang lebih lama terlebih dahulu.
    </p>
  </div>
</div>
```

### Capacity Bar Color Rules
- 0–59%: `bg-emerald-500` (aman)
- 60–79%: `bg-amber-400` (perhatian)
- 80–100%: `bg-red-500` (hampir penuh / kritis)

---

## 8. Blade-Specific Rules

### Layout Inheritance
```blade
{{-- Setiap view harus extend layout role-nya --}}
@extends('layouts.admin')

@section('title', 'Daftar Petani')

@section('content')
{{-- Isi halaman --}}
@endsection
```

### CSRF
Setiap form POST/PUT/DELETE HARUS memiliki `@csrf` dan `@method('PUT')` jika diperlukan.

### Route Helpers
Selalu gunakan `route()` helper, tidak pernah hard-code URL:
```blade
href="{{ route('admin.petani.index') }}"
action="{{ route('admin.petani.store') }}"
```

### Old Input
Semua input form HARUS menggunakan `old()` untuk repopulate setelah validation error:
```blade
value="{{ old('nama_petani', $petani->nama_petani ?? '') }}"
```

### Conditional Active State (Sidebar)
```blade
class="{{ request()->routeIs('admin.petani.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }}"
```

---

## 9. Icons

Gunakan **Heroicons** (SVG inline) untuk konsistensi. Pattern:
```html
<!-- Outline 20px untuk UI kecil (tombol, nav) -->
<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" d="..." />
</svg>
```

Icons yang wajib ada:
- Dashboard: `squares-2x2`
- Petani: `user-group`
- Panen: `sun` atau `calendar`
- Lumbung/stok: `building-storefront`
- Slot: `archive-box`
- Permintaan: `clipboard-document-list`
- Laporan: `chart-bar`
- Akun/Login: `key`
- Notifikasi: `bell`
- Tambah: `plus`
- Edit: `pencil`
- Hapus: `trash`
- Detail: `eye`
- Kembali: `arrow-left`
- Konfirmasi: `check-circle`
- Tolak: `x-circle`
- Warning: `exclamation-triangle`
- Info: `information-circle`

---

## 10. Anti-Patterns — Jangan Dilakukan

- ❌ Jangan hard-code URL — selalu `route()`
- ❌ Jangan hapus `@csrf` dari form manapun
- ❌ Jangan gunakan inline `style=""` kecuali untuk nilai dinamis (progress bar width)
- ❌ Jangan pakai warna aksen (indigo/emerald/amber) untuk role yang berbeda
- ❌ Jangan tampilkan angka tanpa format ribuan untuk nilai > 999
- ❌ Jangan buat form tanpa `old()` pada setiap input
- ❌ Jangan buat tombol hapus tanpa konfirmasi
- ❌ Jangan gunakan `<div>` untuk navigasi — gunakan `<a>`
- ❌ Jangan campur warna status (pending ≠ merah, tersimpan ≠ biru)

---

## 11. Referensi Session Variables

Di semua view, variabel session yang tersedia dari hasil login:
```php
session('login_id')   // id dari tabel login
session('role')       // 'admin' | 'pengelola' | 'petani'
session('nama')       // nama tampilan pengguna
session('ref_id')     // id_petani / id_pengelola / id_admin sesuai role
```

Tampilkan `session('nama')` di sudut kanan atas navbar/topbar semua layout.
