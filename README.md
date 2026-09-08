# Sistem Poin Sekolah 🎓

> Aplikasi manajemen kedisiplinan, prestasi, dan rekapitulasi poin siswa terpadu berbasis sekolah dengan sistem Role-Based Access Control (RBAC) dan pemisahan logika poin pelanggaran & prestasi.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-4.x-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)
![Pest Tests](https://img.shields.io/badge/Tests-Passing-success?style=for-the-badge&logo=pest&logoColor=white)

---

## 📌 Gambaran Umum

**Sistem Poin Sekolah** adalah sistem informasi manajemen kedisiplinan siswa modern yang dirancang untuk membantu sekolah mencatat, memantau, dan menganalisis perilaku siswa baik berupa **pelanggaran tata tertib** maupun **raihan prestasi**. 

Sistem ini menerapkan **arsitektur pemisahan poin**: poin pelanggaran dan poin prestasi dihitung secara independen, sehingga prestasi siswa tidak menutupi catatan pelanggaran, dan rekam jejak siswa tercatat secara transparan dan akuntabel.

---

## ✨ Fitur Utama

### 1. 🛡️ Role-Based Access Control (RBAC) Bertingkat
- **Super Admin**: Mengelola data sekolah, lisensi langganan, dan manajemen global.
- **Admin Sekolah**: Pengendali penuh data sekolah (peraturan, kategori, tahun ajaran, staf, kelas, siswa, threshold, approval banding).
- **Kesiswaan**: Mengawasi kedisiplinan harian, approval log poin yang diinput guru, dan penanganan banding siswa setingkat di bawah Admin.
- **Guru BK (Counselor)**: Memonitor siswa yang melampaui ambang batas sanksi dan mencatat tindak lanjut konseling.
- **Wali Kelas (Homeroom)**: Memonitor kemajuan dan status poin seluruh siswa di kelas perwaliannya.
- **Guru Mata Pelajaran (Teacher)**: Menginput laporan pelanggaran atau prestasi siswa secara real-time.
- **Portal Orang Tua (Parent)**: Akses mandiri untuk orang tua/wali murid menggunakan NISN dan Access Code untuk melihat log poin, ranking perilaku, dan mengajukan banding resmi.

### 2. ⚖️ Pemisahan Logika Poin Pelanggaran & Prestasi
- Poin pelanggaran diakumulasikan untuk monitoring kedisiplinan menuju ambang batas (Threshold).
- Poin prestasi dicatat terpisah sebagai rekam jejak apresiasi dan penghargaan siswa.
- Log poin mencakup riwayat lengkap dengan tanggal kejadian, saksi/pelapor, kategori, dan deskripsi detail.

### 3. 📋 Manajemen Peraturan & Kategori Kustom Dinamis
- Admin dapat menambahkan peraturan baru untuk tipe **Pelanggaran** maupun **Prestasi**.
- Mendukung **Kategori Kustom** dinamis di luar kategori default melalui modal interaktif.
- Visual badge kategori solid dan terintegrasi rapi pada UI tabel dan dropdown.

### 4. 📊 Dashboard Interaktif & Statistik Real-Time
- **Tren Poin**: Line chart interaktif dengan gradient fill untuk melihat dinamika dan lonjakan pelanggaran harian.
- **Persebaran Siswa per Kelas**: Doughnut chart responsif yang menampilkan proporsi jumlah siswa di setiap kelas dengan counter total siswa dan breakdown pill yang compact.
- Ringkasan KPI: Total Pelanggaran, Total Prestasi, Siswa Bermasalah, Laporan Menunggu Verifikasi (Pending Approval), dan Top Pelanggaran.

### 5. 🚨 Ambang Batas Sanksi (Rule Thresholds)
- Penentuan batas akumulasi poin sanksi (misal: Peringatan Lisan, Surat Peringatan 1/2/3, Panggilan Orang Tua, Skorsing).
- Otomasi deteksi siswa yang melampaui batas untuk ditindaklanjuti oleh Wali Kelas, Guru BK, Kesiswaan, dan Admin.

### 6. 📄 Ekspor Laporan Komprehensif
- **Export Excel Multi-Sheet**: Berisi 4 sheet terstruktur (Ringkasan KPI, Top Siswa Pelanggar, Siswa Perlu Penanganan, dan Rekapitulasi per Kelas).
- **Export PDF Siap Cetak**:
  - Laporan perilaku per siswa (format A4 Portrait).
  - Laporan statistik dan rekapitulasi per kelas (format A4 Landscape).

### 7. ⚖️ Alur Banding (Appeals Workflow)
- Orang tua dapat mengajukan banding jika merasa poin yang diberikan tidak sesuai dengan fakta.
- Admin dan Kesiswaan dapat meninjau bukti, menyetujui (membatalkan poin), atau menolak banding disertai catatan resmi.

---

## 🛠️ Tech Stack

- **Backend**: [Laravel 12](https://laravel.com) (PHP 8.2+)
- **Frontend / Styling**: [Tailwind CSS v4](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev)
- **Visualisasi Data**: [Chart.js](https://www.chartjs.org/)
- **Ekspor Dokumen**: [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/), [Barryvdh Laravel-DomPDF](https://github.com/barryvdh/laravel-dompdf)
- **Komponen Interaktif**: [SweetAlert2](https://sweetalert2.github.io/), [TomSelect](https://tom-select.js.org/)
- **Testing**: [Pest PHP](https://pestphp.com/) (62+ unit & feature tests passing)

---

## 🚀 Panduan Instalasi & Menjalankan

### Persyaratan Sistem
- PHP >= 8.2 (dengan ekstensi `pdo`, `mbstring`, `openssl`, `gd`, `zip`, `xml`)
- Composer >= 2.x
- Node.js >= 18.x & npm
- Database SQLite / MySQL / PostgreSQL

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/Rasyyao/website-poin-sekolah-demo.git
   cd website-poin-sekolah-demo
   ```

2. **Install Dependensi PHP & Node.js**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Secara default konfigurasi menggunakan SQLite database yang siap digunakan langsung.*

4. **Migrasi Database & Seeder Demo**
   ```bash
   php artisan migrate --seed
   ```

5. **Kompilasi Aset Frontend**
   ```bash
   npm run build
   # atau untuk mode live reload / development:
   npm run dev
   ```

6. **Jalankan Web Server**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui: `http://127.0.0.1:8000`

---

## 🔑 Akun Demo Pengujian

Setelah menjalankan `php artisan migrate --seed`, Anda dapat menguji seluruh alur dengan akun berikut:

| Role | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@demo.sch.id` | `password` | Manajemen sekolah & lisensi |
| **Admin Sekolah** | `admin@smpn1demo.sch.id` | `password` | Akses penuh sekolah & peraturan |
| **Kesiswaan** | `kesiswaan@smpn1demo.sch.id` | `password` | Verifikasi poin & penanganan banding |
| **Guru BK** | `counselor@smpn1demo.sch.id` | `password` | Monitoring siswa melampaui batas |
| **Wali Kelas** | `budi@smpn1demo.sch.id` | `password` | Monitoring siswa kelas 7A |
| **Guru Staf** | `dewi@smpn1demo.sch.id` | `password` | Input pelanggaran & prestasi |
| **Portal Orang Tua** | Login via Portal Siswa | Akses NISN | Cek poin & ajukan banding |

---

## 🧪 Menjalankan Pengujian (Automated Tests)

Aplikasi dilengkapi dengan automated test suite menggunakan **Pest PHP**:

```bash
php artisan test
```

Semua pengujian mencakup:
- Hierarki role dan hak akses Admin vs Kesiswaan.
- Mutasi peraturan & validasi kategori kustom.
- Pemisahan poin pelanggaran dan prestasi.
- Ekspor multi-sheet Excel dan PDF.
- Siklus pengajuan dan peninjauan banding (Appeals).

---

## 📄 Lisensi

Proyek ini dirilis di bawah lisensi [MIT License](LICENSE).
