# Verdana Health Design System - Implementation Guide for SILPD
**Tanggal:** 31 Mei 2026  
**Status:** Ready for Implementation

---

## 📋 OVERVIEW

Verdana Health Design System telah diimplementasikan untuk SILPD dengan fokus pada:
- **Calm & Trustworthy** - Warna navy dan sage yang menenangkan
- **Clinical Precision** - Typography yang jelas dan mudah dibaca
- **Accessibility** - Kontras warna yang baik dan spacing yang generous
- **Consistency** - Component library yang konsisten

---

## 📁 FILES CREATED

### 1. **CSS Design System**
**File:** `public/css/verdana-health.css`

**Isi:**
- CSS Variables untuk semua design tokens
- Typography utilities (Display, H1-H4, Body, Caption, Code)
- Button components (Primary, Secondary, Ghost, Destructive, Sage)
- Card components (Default, Elevated, with Header)
- Input components (Text, Textarea, Select)
- Chip/Badge components (Filter, Status)
- Table/List components
- Alert components
- Stat card components
- Utility classes

### 2. **New Layout File**
**File:** `resources/views/layouts/admin-verdana.blade.php`

**Features:**
- Verdana Health color scheme (Navy sidebar)
- Plus Jakarta Sans for headings
- DM Sans for body text
- Improved navigation with icons
- Better spacing and breathing room
- Flash message alerts with Verdana styling
- Consistent component usage

---

## 🎨 DESIGN TOKENS

### Colors
```css
--vh-navy: #0F172A          /* Primary actions, headers */
--vh-slate: #64748B         /* Secondary text, borders */
--vh-sage: #059669          /* Links, CTAs, highlights */
--vh-bg: #F8FAFC            /* Page background */
--vh-surface: #FFFFFF       /* Card backgrounds */
--vh-success: #22C55E       /* Success states */
--vh-warning: #EAB308       /* Warning states */
--vh-error: #EF4444         /* Error states */
--vh-info: #0EA5E9          /* Info states */
```

### Typography
```css
--vh-font-headline: 'Plus Jakarta Sans'
--vh-font-body: 'DM Sans'
--vh-font-mono: 'Fira Code'
```

### Spacing (8px base)
```css
--vh-space-xs: 4px
--vh-space-sm: 8px
--vh-space-md: 16px
--vh-space-lg: 24px
--vh-space-xl: 32px
--vh-space-2xl: 48px
--vh-space-3xl: 64px
```

### Border Radius
```css
--vh-radius-sm: 4px         /* Badges, small tags */
--vh-radius: 8px            /* Buttons, cards, inputs */
--vh-radius-md: 12px        /* Modals, dropdowns */
--vh-radius-lg: 16px        /* Large containers */
--vh-radius-full: 9999px    /* Avatars, status */
```

### Shadows
```css
--vh-shadow-sm: 0 1px 3px 0 rgba(15, 23, 42, 0.03)
--vh-shadow: 0 2px 6px 0 rgba(15, 23, 42, 0.05)
--vh-shadow-md: 0 4px 16px 0 rgba(15, 23, 42, 0.07)
--vh-shadow-lg: 0 8px 32px 0 rgba(15, 23, 42, 0.10)
```

---

## 🧩 COMPONENT USAGE

### Buttons

```html
<!-- Primary Button -->
<button class="vh-btn vh-btn-primary vh-btn-md">
    Simpan Data
</button>

<!-- Secondary Button -->
<button class="vh-btn vh-btn-secondary vh-btn-md">
    Batal
</button>

<!-- Ghost Button -->
<button class="vh-btn vh-btn-ghost vh-btn-sm">
    Lihat Detail
</button>

<!-- Sage Button (for positive actions) -->
<button class="vh-btn vh-btn-sage vh-btn-md">
    Setujui
</button>

<!-- Destructive Button -->
<button class="vh-btn vh-btn-destructive vh-btn-md">
    Hapus
</button>
```

### Cards

```html
<!-- Default Card -->
<div class="vh-card">
    <h3 class="vh-h3 mb-4">Card Title</h3>
    <p class="vh-body">Card content goes here...</p>
</div>

<!-- Elevated Card (with shadow) -->
<div class="vh-card-elevated">
    <h3 class="vh-h3 mb-4">Elevated Card</h3>
    <p class="vh-body">This card has a shadow...</p>
</div>

<!-- Card with Header -->
<div class="vh-card">
    <div class="vh-card-header">
        <h3 class="vh-h4">Data Petani</h3>
    </div>
    <p class="vh-body">Card content...</p>
</div>
```

### Forms

```html
<!-- Text Input -->
<div class="mb-4">
    <label class="vh-label">Nama Petani</label>
    <input type="text" class="vh-input" placeholder="Masukkan nama...">
    <p class="vh-helper-text">Nama lengkap sesuai KTP</p>
</div>

<!-- Input with Error -->
<div class="mb-4">
    <label class="vh-label">Email</label>
    <input type="email" class="vh-input vh-input-error" value="invalid">
    <p class="vh-error-text">Format email tidak valid</p>
</div>

<!-- Textarea -->
<div class="mb-4">
    <label class="vh-label">Keterangan</label>
    <textarea class="vh-textarea" rows="4"></textarea>
</div>

<!-- Select -->
<div class="mb-4">
    <label class="vh-label">Kelompok Tani</label>
    <select class="vh-select">
        <option>Pilih kelompok...</option>
        <option>Kelompok A</option>
        <option>Kelompok B</option>
    </select>
</div>
```

### Chips/Badges

```html
<!-- Status Chips -->
<span class="vh-chip vh-chip-success">Tersimpan</span>
<span class="vh-chip vh-chip-warning">Pending</span>
<span class="vh-chip vh-chip-error">Ditolak</span>
<span class="vh-chip vh-chip-info">Baru</span>

<!-- Filter Chips -->
<span class="vh-chip vh-chip-filter">Semua</span>
<span class="vh-chip vh-chip-filter-active">Aktif</span>
```

### Tables

```html
<table class="vh-table">
    <thead>
        <tr>
            <th>Nama Petani</th>
            <th>Kelompok</th>
            <th>Luas Lahan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Budi Santoso</td>
            <td>Kelompok Tani A</td>
            <td class="vh-code">1,250 m²</td>
            <td>
                <button class="vh-btn vh-btn-ghost vh-btn-sm">Detail</button>
            </td>
        </tr>
    </tbody>
</table>
```

### Alerts

```html
<!-- Success Alert -->
<div class="vh-alert vh-alert-success">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="vh-body-sm font-medium">Berhasil!</p>
        <p class="vh-body-sm">Data berhasil disimpan.</p>
    </div>
</div>

<!-- Error Alert -->
<div class="vh-alert vh-alert-error">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="vh-body-sm font-medium">Error!</p>
        <p class="vh-body-sm">Terjadi kesalahan.</p>
    </div>
</div>
```

### Stat Cards

```html
<div class="vh-stat-card">
    <div class="flex items-center justify-between mb-3">
        <p class="vh-stat-label">Total Petani</p>
        <span class="p-2 bg-indigo-50 rounded-lg">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </span>
    </div>
    <p class="vh-stat-value">1,234</p>
    <p class="vh-stat-caption">Petani terdaftar</p>
</div>
```

---

## 🔄 MIGRATION STEPS

### Step 1: Backup Current Layout
```bash
cp resources/views/layouts/admin.blade.php resources/views/layouts/admin-backup.blade.php
```

### Step 2: Replace Layout File
```bash
cp resources/views/layouts/admin-verdana.blade.php resources/views/layouts/admin.blade.php
```

### Step 3: Update Dashboard View
File: `resources/views/admin/dashboard.blade.php`

**Replace stat cards:**
```html
<!-- OLD -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Petani</p>
    <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ $totalPetani }}</p>
</div>

<!-- NEW -->
<div class="vh-stat-card">
    <div class="flex items-center justify-between mb-3">
        <p class="vh-stat-label">Total Petani</p>
        <span class="p-2 rounded-lg" style="background-color: rgba(99, 102, 241, 0.1);">
            <svg class="w-5 h-5" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </span>
    </div>
    <p class="vh-stat-value">{{ $totalPetani }}</p>
    <p class="vh-stat-caption">Petani terdaftar</p>
</div>
```

**Replace tables:**
```html
<!-- OLD -->
<table class="w-full">
    <thead>
        <tr class="border-b border-gray-200 bg-gray-50">
            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Petani</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-sm text-gray-900">{{ $item->nama }}</td>
        </tr>
    </tbody>
</table>

<!-- NEW -->
<table class="vh-table">
    <thead>
        <tr>
            <th>Petani</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $item->nama }}</td>
        </tr>
    </tbody>
</table>
```

**Replace cards:**
```html
<!-- OLD -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Card Title</h3>
    </div>
    <div class="p-6">
        Content...
    </div>
</div>

<!-- NEW -->
<div class="vh-card">
    <div class="vh-card-header">
        <h3 class="vh-h4">Card Title</h3>
    </div>
    <div>
        Content...
    </div>
</div>
```

### Step 4: Update Form Views
Replace all form inputs in create/edit views:

```html
<!-- OLD -->
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
</div>

<!-- NEW -->
<div class="mb-4">
    <label class="vh-label">Nama</label>
    <input type="text" class="vh-input" placeholder="Masukkan nama...">
</div>
```

### Step 5: Update Button Styles
Replace all buttons:

```html
<!-- OLD -->
<button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
    Simpan
</button>

<!-- NEW -->
<button class="vh-btn vh-btn-primary vh-btn-md">
    Simpan
</button>
```

### Step 6: Update Status Badges
Replace all status badges:

```html
<!-- OLD -->
<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
    Tersimpan
</span>

<!-- NEW -->
<span class="vh-chip vh-chip-success">
    Tersimpan
</span>
```

---

## 📝 TYPOGRAPHY USAGE

### Headings
```html
<h1 class="vh-h1">Page Title</h1>
<h2 class="vh-h2">Section Title</h2>
<h3 class="vh-h3">Subsection Title</h3>
<h4 class="vh-h4">Card Title</h4>
```

### Body Text
```html
<p class="vh-body-lg">Large body text for emphasis</p>
<p class="vh-body">Regular body text</p>
<p class="vh-body-sm">Small body text for captions</p>
<p class="vh-caption">Tiny text for labels</p>
```

### Code/Numbers
```html
<span class="vh-code">1,234.56 kg</span>
<span class="vh-code">2024-05-31</span>
```

---

## 🎯 BEST PRACTICES

### 1. **Use Generous Whitespace**
```html
<!-- Good -->
<div class="p-8">
    <h2 class="vh-h2 mb-6">Title</h2>
    <p class="vh-body mb-8">Content...</p>
</div>

<!-- Bad -->
<div class="p-2">
    <h2 class="vh-h2 mb-1">Title</h2>
    <p class="vh-body mb-2">Content...</p>
</div>
```

### 2. **Navy + White for Primary Rhythm**
- Use Navy (#0F172A) for primary actions and headers
- Use White (#FFFFFF) for surfaces
- Reserve Sage (#059669) for interactive elements only

### 3. **Consistent Border Radius**
- Use 8px (vh-radius) for most components
- Use 4px (vh-radius-sm) for small badges
- Use 12px (vh-radius-md) for modals

### 4. **Soft Shadows**
- Use vh-shadow-sm for buttons and chips
- Use vh-shadow for cards
- Use vh-shadow-md for elevated cards
- Use vh-shadow-lg for modals

### 5. **Clear Iconography**
- Always pair icons with text labels
- Use 20px (w-5 h-5) for inline icons
- Use 24px (w-6 h-6) for standalone icons

### 6. **Tabular Data**
- Use vh-code class for numbers in tables
- Ensures proper alignment with Fira Code font

---

## 🔍 TESTING CHECKLIST

- [ ] Dashboard loads with new design
- [ ] All stat cards display correctly
- [ ] Tables are readable and hover states work
- [ ] Forms have proper focus states
- [ ] Buttons have correct hover states
- [ ] Flash messages display with correct colors
- [ ] Navigation highlights active page
- [ ] Sidebar scrolls properly
- [ ] Typography is legible at all sizes
- [ ] Colors have sufficient contrast
- [ ] Spacing feels generous and calm
- [ ] Mobile responsiveness (if applicable)

---

## 🚀 NEXT STEPS

1. **Test the new layout:**
   ```
   php artisan serve
   ```
   Navigate to admin dashboard and verify design

2. **Update remaining views:**
   - Petani index/create/edit
   - Kelompok Tani views
   - Jenis Gabah views
   - Lumbung views
   - Pengelola views
   - Panen views
   - Instruksi views
   - Permintaan views
   - Laporan views

3. **Create Pengelola and Petani layouts:**
   - Copy admin-verdana.blade.php
   - Adjust sidebar navigation for each role
   - Update topbar badge text

4. **Document component patterns:**
   - Create a component library page
   - Add examples of all components
   - Include do's and don'ts

---

## 📚 RESOURCES

- **Design System Spec:** `verdana-health-design-system-DESIGN.md`
- **CSS File:** `public/css/verdana-health.css`
- **Layout File:** `resources/views/layouts/admin-verdana.blade.php`
- **Fonts:**
  - Plus Jakarta Sans: https://fonts.google.com/specimen/Plus+Jakarta+Sans
  - DM Sans: https://fonts.google.com/specimen/DM+Sans
  - Fira Code: https://fonts.google.com/specimen/Fira+Code

---

**Implementation Guide Complete! Ready to transform SILPD with Verdana Health Design System. 🎨**
