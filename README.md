# 🕌 Tahfidz Cluster - Sistem Pengelompokan Karakteristik Akademik Santri

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js)](https://alpinejs.dev)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

**Tahfidz Cluster** adalah sebuah sistem informasi akademik premium berbasis web yang dirancang khusus untuk memetakan, menganalisis, dan mengelompokkan karakteristik kompetensi santri penghafal Al-Qur'an secara objektif menggunakan **Algoritma K-Means Clustering**. 

Sistem ini memproyeksikan data santri ke dalam ruang koordinat 3-Dimensi berdasarkan 3 kriteria utama:
*   **C1 (Hafalan)**: Nilai kelancaran setoran hafalan baru.
*   **C2 (Murojaah)**: Nilai pemeliharaan hafalan lama yang sudah disetor.
*   **C3 (Tahsin)**: Nilai kefasihan tajwid, makhorijul huruf, dan keindahan bacaan.

---

## ✨ Fitur-Fitur Unggulan

*   🚀 **Inisialisasi Centroid Awal Dinamis**: Penentuan centroid awal otomatis yang 100% dinamis berdasarkan persentil nilai rata-rata populasi ($P_{15}$ untuk Cukup, $P_{50}$ untuk Baik, dan $P_{85}$ untuk Sangat Baik) — bebas dari bias *hardcode* manual.
*   📊 **Perhitungan K-Means Step-by-Step Transparan**: Visualisasi lengkap rincian iterasi demi iterasi, mulai dari koordinat centroid pergeseran, lembar hitung jarak Euclidean terkecil, hingga titik konvergensi akhir.
*   🥇 **Sistem Pemeringkatan (Leaderboard) Premium**: Fitur pemeringkatan santri otomatis berdasarkan nilai rata-rata kombinasi tiga kriteria, lengkap dengan lencana medali prestisius (🥇 Peringkat 1, 🥈 Peringkat 2, 🥉 Peringkat 3).
*   📐 **Integrasi Notasi Matematika Tinggi**: Menggunakan **MathJax v3** untuk me-render formula matematika dan rumus jarak di halaman dashboard serta proses perhitungan secara jernih dan elegan.
*   🎨 **UI/UX Modern & Estetik**: Tampilan bernuansa islami modern dengan palet warna emerald/teal, dipadukan dengan aksen glassmorphism, tata letak responsif penuh (mobile-friendly), serta animasi mikro transisi.
*   ⚙️ **Manajemen Data Komprehensif**: Fitur CRUD dinamis untuk data Santri, Kriteria Penilaian, dan Nilai Santri.

---

## 🛠️ Tumpukan Teknologi (Tech Stack)

*   **Framework Utama**: Laravel 11 (PHP 8.2+)
*   **Database**: MySQL / MariaDB
*   **Desain & Styling**: Tailwind CSS
*   **Interaktivitas Frontend**: Alpine.js
*   **Rendering Matematika**: MathJax v3 CDN
*   **Ikonografi**: FontAwesome 6 Free

---

## 💻 Panduan Instalasi (Installation Guide)

Ikuti langkah-langkah di bawah ini untuk memasang dan menjalankan proyek **Tahfidz Cluster** di lingkungan lokal Anda.

### 📋 Prasyarat Sistem
Pastikan perangkat Anda sudah terpasang:
*   PHP >= 8.2
*   Composer (Dependency Manager PHP)
*   Node.js (versi 18+) & NPM / Bun (Rekomendasi: Bun untuk kompilasi ultra cepat)
*   MySQL Server

---

### 🚀 Langkah Demi Langkah

#### 1. Klon Repositori
Klon proyek ini dari repositori Git:
```bash
git clone https://github.com/hisamaszaini/tahfidzcluster.git
cd tahfidzcluster
```

#### 2. Pasang Dependensi PHP (Composer)
Unduh dan pasang semua paket library backend:
```bash
composer install
```

#### 3. Pasang Dependensi Frontend (NPM/Bun)
Unduh paket library frontend untuk Tailwind CSS dan build assets:
*Menggunakan Bun (Sangat Direkomendasikan):*
```bash
bun install
```
*Atau menggunakan NPM biasa:*
```bash
npm install
```

#### 4. Konfigurasi Environment File
Salin berkas template konfigurasi `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka berkas `.env` yang baru dibuat di editor teks Anda, kemudian sesuaikan dengan koneksi basis data MySQL Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tahfidz
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Generate Application Key
Buat kunci pengaman aplikasi Laravel Anda:
```bash
php artisan key:generate
```

#### 6. Setup Database & Seeding Data
Jalankan migrasi tabel basis data sekaligus isi dengan data awal/simulasi secara otomatis:
```bash
php artisan migrate --seed
```
*Catatan: Seed data sudah mencakup akun Administrator default (`admin@admin.com`), data kriteria C1-C3, serta puluhan data santri sampel untuk kalkulasi K-Means.*

#### 7. Kompilasi Aset Frontend (Vite)
Build dan kompilasi seluruh aset visual Tailwind CSS untuk mode pengembangan atau produksi:
*Mode Pengembangan (Real-time reload):*
```bash
bun run dev      # atau: npm run dev
```
*Mode Produksi (Kompilasi final statis):*
```bash
bun run build    # atau: npm run build
```

#### 8. Jalankan Local Server
Nyalakan server lokal Laravel Anda:
```bash
php artisan serve
```
Aplikasi Anda siap diakses di web browser melalui alamat: **`http://127.0.0.1:8000`**

---

## 🔑 Akun Akses Default (Seed Data)
Setelah melakukan database seeding (`--seed`), Anda dapat masuk ke dalam sistem menggunakan akun administrator default berikut:
*   **Email**: `admin@admin.com`
*   **Password**: `password`

---

## 📐 Penjelasan Metodologi Matematika K-Means

Sistem ini mengelompokkan santri ke dalam $K = 3$ kategori: **Cukup** (C1), **Baik** (C2), dan **Sangat Baik** (C3).

### 1. Inisialisasi Centroid Awal
Centroid ditentukan secara deterministik-dinamis dengan mengurutkan skor rata-rata santri $\bar{x} = \frac{C_1+C_2+C_3}{3}$:
*   **Kluster Cukup (C1)**: Santri pada indeks Persentil 15 ($P_{15}$).
*   **Kluster Baik (C2)**: Santri pada indeks Persentil 50 ($P_{50}$ / Median).
*   **Kluster Sangat Baik (C3)**: Santri pada indeks Persentil 85 ($P_{85}$).

### 2. Rumus Jarak Euclidean 3D
Setiap koordinat santri $p(p_{C1}, p_{C2}, p_{C3})$ akan dihitung jarak terdekatnya ke masing-masing pusat kluster $q(q_{C1}, q_{C2}, q_{C3})$ menggunakan persamaan:
$$d(p, q) = \sqrt{(p_{C1} - q_{C1})^2 + (p_{C2} - q_{C2})^2 + (p_{C3} - q_{C3})^2}$$
Santri otomatis dialokasikan ke kluster dengan jarak $d(p, q)$ terkecil.

---

## 📝 Lisensi
Proyek ini dilisensikan di bawah **[MIT License](LICENSE)**.
