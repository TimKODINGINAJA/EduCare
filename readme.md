<div align="center">

# 🎓 EduCare

### Platform Digital Sekolah — Belajar Online, Quiz & Sertifikat Terpadu dengan Sistem Pengaduan Sekolah (SiLapor)

[![Live Demo](https://img.shields.io/badge/🚀_Live_Demo-Visit_Site-success?style=for-the-badge)](https://[URL_DEMO])

[![GitHub](https://img.shields.io/badge/GitHub-Repository-181717?style=for-the-badge&logo=github)](https://[URL_REPO])

[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

**Submission for ITECHNO CUP 2026 - Web Development**

**By [KODINGINAJA]**

</div>

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Unggulan](#-fitur-unggulan)
- [Demo & Screenshot](#-demo--screenshot)
- [Teknologi](#-teknologi)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Instalasi & Setup](#-instalasi--setup)
- [Penggunaan](#-penggunaan)
- [API Documentation](#-api-documentation)
- [Testing](#-testing)
- [Tim Developer](#-tim-pengembang)
- [Lisensi](#-lisensi)

---

## 👥 Tim Developer

| Nama                          | Peran                               | GitHub                                     |
| ----------------------------- | ----------------------------------- | ------------------------------------------ |
| **[Lutfi Andika]**            | Project Lead & Full Stack Developer | [GitHub](https://github.com/lutfi-dika)    |
| **[Khaerul Fakhri]**          | Frontend Developer                  | [GitHub](https://github.com/KhaerulFakhri) |
| **[Morren Bangkit Al Fatih]** | Backend Developer                   | [GitHub](https://github.com/MorrenBA)      |

---

## 🎯 Tentang Proyek

### Latar Belakang

Banyak sekolah masih mengandalkan proses manual dalam pembelajaran dan pengelolaan pelaporan siswa — materi tidak terpusat, progress belajar sulit dipantau, dan pengaduan siswa (bullying, fasilitas rusak, kebersihan, dll) sering tidak terdokumentasi dan tidak ditindaklanjuti secara transparan. Hal ini menghambat digitalisasi pendidikan yang semakin didorong di era merdeka belajar.

### Solusi yang Ditawarkan

**EduCare** hadir sebagai platform digital terpadu yang menggabungkan **Learning Management System (LMS)** dan **sistem pengaduan sekolah (SiLapor)** dalam satu aplikasi web ringan berbasis PHP Native. Siswa dapat belajar materi, menonton video, mengerjakan quiz, memantau progress & XP, hingga mengunduh sertifikat — sekaligus melaporkan permasalahan sekolah dan memantau statusnya secara transparan. Guru dapat mengelola materi, bank soal, memantau nilai siswa, dan menindaklanjuti laporan dalam satu dashboard.

### Tujuan Proyek

- 🎯 **Tujuan Utama**: Mendigitalisasi pembelajaran dan pelaporan sekolah dalam satu platform yang mudah dipakai.
- 📊 **Target Pengguna**: Siswa, guru, dan pihak sekolah (admin).
- 💡 **Value Proposition**: PHP Native + database JSON membuat aplikasi ringan, mudah dipasang tanpa setup MySQL, namun sudah mencakup fitur LMS lengkap (materi, video, quiz, sertifikat, progress) plus sistem pengaduan terintegrasi.

---

## ✨ Fitur Unggulan

### Fitur Utama

| Fitur                                               | Deskripsi                                                                                                                                                           | Keunggulan                                                             |
| --------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------- |
| **Belajar Online (`/belajar`)**                     | Portal materi per kategori (Web Development, Matematika, IPA, IPS, Bahasa, dll) dengan detail materi, video pembelajaran, dan syntax highlighting untuk contoh kode | Konten bertahap per modul dengan progress otomatis tersimpan per siswa |
| **Quiz & Sertifikat**                               | Quiz interaktif per materi lengkap dengan halaman hasil, diakhiri sertifikat belajar setelah materi/quiz diselesaikan                                               | Evaluasi pembelajaran terukur dan penghargaan otomatis                 |
| **Gamifikasi (XP & Leaderboard)**                   | Siswa memperoleh XP (level = 1 + XP/250) disertai leaderboard dan notifikasi dalam aplikasi                                                                         | Meningkatkan motivasi belajar siswa                                    |
| **SiLapor — Sistem Pengaduan Sekolah (`/silapor`)** | Siswa membuat laporan (bullying, fasilitas rusak, kebersihan, barang hilang) dan memantau status (Menunggu / Diproses / Selesai)                                    | Transparansi pelaporan dengan tindak lanjut langsung oleh guru         |
| **Multi-role Dashboard**                            | Dashboard khusus Guru (kelola materi, bank soal, laporan, pengguna) dan Siswa (progress, quiz, laporan, profil)                                                     | Proteksi akses per halaman via `requireRole()`                         |

### Fitur Tambahan

- **Reset Password via Email (SMTP Gmail + PHPMailer)** — pemulihan akun otomatis tanpa intervensi admin
- **Notifikasi & Log Aktivitas** — riwayat kegiatan dan notifikasi real-time dalam aplikasi (`notifications.json`, `activities.json`)
- **Renderer Markdown Materi** — isi materi ditulis markdown ringan, dirender dengan syntax highlighting + tombol copy kode
- **Remember Me (cookie)** — sesi tetap tersimpan di perangkat pengguna
- **Responsive Modern UI** — Tailwind CSS v4 + Alpine.js + Lucide Icons

---

## 📸 Demo & Screenshot

### Live Demo

🔗 **[Kunjungi Website](http://educare-timkodinginaja.page.gd)**

### Link Github
🔗 **[Kunjungi Repo Github EduCare](https://github.com/TimKODINGINAJA/EduCare)**

### Screenshot Aplikasi

<div align="center">

<h1>Lending Page</h1>

  <img src="./assets/img/Lending-Page/LandingPage1.png" alt="Homepage" width="800"/>
  <p><em>Tampilan utama aplikasi</em></p>

   <img src="./assets/img/Lending-Page/LandingPage2.png" alt="Homepage" width="800"/>
  <p><em>Educare Memilikli 2 role yaitu murid dan guru</em></p>

   <img src="./assets/img/Lending-Page/LandingPage3.png" alt="Homepage" width="800"/>
  <p><em>Materi-Materi Yang Ada Di Website EduCare</em></p>

   <img src="./assets/img/Lending-Page/LandingPage4.png" alt="Homepage" width="800"/>
  <p><em>Cara Kerja Website EduCare Dan Testimoni Website EduCare</em></p>

   <img src="./assets/img/Lending-Page/LandingPage5.png" alt="Homepage" width="800"/>
  <p><em>Pertanyaan Yang Sering Di Tanyakan Tentang EduCare</em></p>

  <h1>Login & Register</h1>

<img src="./assets/img/Login&Register/Register.png" alt="Homepage" width="800"/>
  <p><em>Register</em></p>


<img src="./assets/img/Login&Register/Login.png" alt="Homepage" width="800"/>
  <p><em>Login</em></p>

<img src="./assets/img/Login&Register/Forgot.png" alt="Homepage" width="800"/>
  <p><em>Forgot Password</em></p>

  <h1>Dashboard Siswa</h1>
<img src="./assets/img/Dashboard-Siswa/Siswa1.png" alt="Homepage" width="800"/>
  <p><em>Tampilan Awal Dashboard Siswa</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa2.png" alt="Homepage" width="800"/>
  <p><em>Profile</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa3.png" alt="Homepage" width="800"/>
  <p><em>Pengaturan</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa4.png" alt="Homepage" width="800"/>
  <p><em>Notofikasi</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa5.png" alt="Homepage" width="800"/>
  <p><em>Privacy</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa6.png" alt="Homepage" width="800"/>
  <p><em>Tampilan & Tema</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa7.png" alt="Homepage" width="800"/>
  <p><em>Halaman Materi Yang Sudah Di Kerjakan</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa8.png" alt="Homepage" width="800"/>
  <p><em>Halaman Materi</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa9.png" alt="Homepage" width="800"/>
  <p><em>Halaman Materi IT</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa10.png" alt="Homepage" width="800"/>
  <p><em>Halaman Materi Umum</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa11.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Siswa/Siswa12.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Siswa/Siswa13.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Siswa/Siswa14.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Siswa/Siswa15.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Siswa/Siswa16.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Siswa/Siswa17.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Siswa/Siswa18.png" alt="Homepage" width="800"/>
  <p><em>Materi IT</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa19.png" alt="Homepage" width="800"/>
  <p><em>Materi IT Yang Sudah Selesai</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa20.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Siswa/Siswa21.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Siswa/Siswa22.png" alt="Homepage" width="800"/>
  <p><em>Materi Umum</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa23.png" alt="Homepage" width="800"/>
  <p><em>Materi Umum Yang Sudah Selesai</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa24.png" alt="Homepage" width="800"/>
  <p><em>Lanjutan Materi</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa25.png" alt="Homepage" width="800"/>
  <p><em>Progres Belajar</em></p>
  
<img src="./assets/img/Dashboard-Siswa/Siswa26.png" alt="Homepage" width="800"/>
  <p><em>Quiz</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa27.png" alt="Homepage" width="800"/>
  <p><em>Halaman Quiz</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa28.png" alt="Homepage" width="800"/>
  <p><em>Memilih Jawaban Quiz</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa29.png" alt="Homepage" width="800"/>
  <p><em>Quiz Yang Sudah Selesai</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa30.png" alt="Homepage" width="800"/>
  <p><em>Laporan Siswa</em></p>

<img src="./assets/img/Dashboard-Siswa/Siswa31.png" alt="Homepage" width="800"/>
  <p><em>Peringkat Siswa</em></p>

  <h1>Dashboard Guru</h1>

<img src="./assets/img/Dashboard-Guru/Guru1.png" alt="Homepage" width="800"/>
  <p><em>Tampilan Awal Dashboard Guru</em></p>

<img src="./assets/img/Dashboard-Guru/Guru2.png" alt="Homepage" width="800"/>
  <p><em>Analitik Pembelejaran</em></p>

<img src="./assets/img/Dashboard-Guru/Guru3.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Guru/Guru4.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Guru/Guru5.png" alt="Homepage" width="800"/>
  <p><em>Kelola Kursus</em></p>

<img src="./assets/img/Dashboard-Guru/Guru6.png" alt="Homepage" width="800"/>
<img src="./assets/img/Dashboard-Guru/Guru7.png" alt="Homepage" width="800"/>
  <p><em>Kelola Quiz</em></p>

<img src="./assets/img/Dashboard-Guru/Guru8.png" alt="Homepage" width="800"/>
  <p><em>Nilai Siswa</em></p>

<img src="./assets/img/Dashboard-Guru/Guru9.png" alt="Homepage" width="800"/>
  <p><em>Data Siswa</em></p>

<img src="./assets/img/Dashboard-Guru/Guru10.png" alt="Homepage" width="800"/>
  <p><em>Aktifitas Siswa Siswa</em></p>

<img src="./assets/img/Dashboard-Guru/Guru11.png" alt="Homepage" width="800"/>
  <p><em>Laporan Siswa</em></p>
</div>



## 🛠️ Teknologi

### Tech Stack

#### Frontend

```
Framework    : HTML5 (server-side rendered)
UI Library   : Tailwind CSS v4 (Alpine.js untuk interaktivitas)
State Mgmt   : Alpine.js
Validation   : PHP (server-side) + JavaScript
```

#### Backend

```
Runtime      : PHP (native, tanpa framework)
Framework    : PHP Native (struktur modular via includes/)
Database     : File JSON (folder data/)
ORM          : Tidak ada (helper readJSON/writeJSON di function.php)
Auth         : Session + password hash (password_hash / password_verify)
```

#### DevOps & Tools

```
Deployment   : Local server (XAMPP / Laragon - Apache)
CI/CD        : -
Testing      : Manual (tanpa automated test framework)
Monitoring   : -
```

### Alasan Pemilihan Teknologi

| Teknologi                       | Alasan Pemilihan                                                                                                       |
| ------------------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| **PHP Native**                  | Ringan, mudah dipelajari, tanpa dependency framework berat — cocok untuk edukasi & demo, dan berjalan di hosting murah |
| **File JSON sebagai database**  | Tanpa perlu setup MySQL/database server, langsung jalan di XAMPP/Laragon — ideal untuk kebutuhan lomba & portofolio    |
| **Tailwind CSS v4 + Alpine.js** | Pengembangan UI cepat, konsisten, dan responsive tanpa menulis banyak CSS manual                                       |
| **PHPMailer + SMTP Gmail**      | Pengiriman email reset password yang reliable dengan dukungan App Password Gmail                                       |
| **Composer & NPM**              | Manajemen dependency PHP (PHPMailer) dan frontend (Tailwind CLI) secara terstandar                                     |

### Dependencies Utama

```json
// composer.json
{
  "require": {
    "phpmailer/phpmailer": "^7.1"
  }
}

// package.json
{
  "dependencies": {
    "@tailwindcss/cli": "^4.3.3",
    "alpinejs": "^3.15.12",
    "tailwindcss": "^4.3.3"
  }
}
```

---

## 🏗️ Arsitektur Sistem

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                       Browser (Client)                      │
│        HTML5 + Tailwind CSS v4 + Alpine.js + JavaScript     │
└───────────────────────────┬─────────────────────────────────┘
                            │  HTTP Request (Apache)
┌───────────────────────────▼─────────────────────────────────┐
│                    PHP Native (back-end)                    │
│  index.php → auth/ belajar/ silapor/ dashboard-siswa/       │
│  dashboard-guru/ views/ includes/                           │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│                   function.php (Helper)                     │
│  env() · readJSON/writeJSON · requireRole · addXp           │
│  addNotification · renderMateriContent · sendResetEmail     │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│              Database (File JSON — folder data/)            │
│  users · materi · kategori · quiz · progress · reports      │
│  notifications · activities · leaderboard · courses         │
└─────────────────────────────────────────────────────────────┘
```

### Database Schema

Seluruh data disimpan sebagai file JSON di folder `data/`:

```
users.json          → id, nama, email, password (hash), role (admin/guru/siswa), created_at
kategori.json       → id, nama kategori materi
materi.json         → id, judul, kategori_id, konten (markdown), video_url, dll
quiz.json           → id, materi_id, soal, pilihan, jawaban
progress.json       → user_id, materi_id, status, nilai, XP
reports.json        → id, user_id, jenis, deskripsi, status (Menunggu/Diproses/Selesai)
notifications.json  → user_id, pesan, read
activities.json     → user_id, aksi, timestamp
leaderboard.json    → user_id, xp, level
courses.json        → data kursus/pengaturan
```

> **Catatan keamanan**: File data berisi data pengguna asli (`users.json`, `progress.json`,
> `activities.json`, `notifications.json`, `reports.json`, `leaderboard.json`) **tidak ikut di-commit**
> ke git (lihat `.gitignore`). Akun **Guru** dibuat otomatis saat pertama kali halaman login diakses:
> `guru@gmail.com` / password `123`. Setelah clone, kunjungi halaman login satu kali agar akun
> tersebut disemai, lalu silakan daftar akun Siswa lewat halaman register.

### Folder Structure

```
EduCare/
├── assets/               # css, js, img (logo & gambar)
├── auth/                 # login, register, forgot/reset password, remember, logout
├── belajar/              # materi, video, quiz, hasil, sertifikat
├── silapor/              # tambah-laporan, riwayat, status, detail-laporan
├── dashboard-siswa/      # dashboard, quiz, mark-materi, profile, settings
├── dashboard-guru/       # dashboard, materi, kategori, quiz, laporan, user, pengaturan
├── views/                # landing page, about, kontak
├── includes/             # header, navbar, sidebar, footer, Faq
├── data/                 # "database" file JSON
├── vendor/               # Composer dependencies (PHPMailer)
├── function.php          # helper terpusat
├── index.php             # entry point / landing page
├── .env / .env.example   # konfigurasi SMTP
├── package.json          # frontend deps (Tailwind, Alpine)
└── composer.json         # PHP deps (PHPMailer)
```

---

## ⚙️ Instalasi & Setup

### Prerequisites

Pastikan Anda telah menginstall:

- **PHP** (v8.x) — bawaan XAMPP / Laragon
- **Composer** — untuk dependency PHP (PHPMailer)
- **Node.js & npm** (v18.x atau lebih tinggi) — opsional, untuk build ulang CSS Tailwind
- **Web Server** — Apache (XAMPP / Laragon)

### Langkah Instalasi

#### 1️⃣ Clone Repository

```bash
git clone https://github.com/[username]/[repo-name].git
cd [repo-name]
```

Atau salin folder `EduCare` ke direktori server lokal:

```
xampp/
└── htdocs/
    └── EduCare/
```

#### 2️⃣ Install Dependencies

```bash
# Dependency PHP (PHPMailer - untuk reset password via email)
composer install

# Dependency frontend (opsional - hanya jika ingin build ulang CSS Tailwind)
npm install
```

#### 3️⃣ Setup Environment Variables

Buat file `.env` di root directory (salin dari `.env.example`):

```env
# SMTP (untuk fitur reset password via email)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=emailku@gmail.com
SMTP_PASSWORD=app_password_gmail_kamu
SMTP_FROM_EMAIL=emailku@gmail.com
SMTP_FROM_NAME=EduCare Official
```

> Untuk `SMTP_PASSWORD`, gunakan **App Password** Gmail (bukan password login biasa). File `.env` otomatis dibaca oleh `function.php` lewat helper `env()`.

#### 4️⃣ Jalankan Web Server

Aktifkan **Apache** pada **XAMPP Control Panel** atau **Laragon**.

#### 5️⃣ Buka Aplikasi

```
http://localhost/EduCare
```

Aplikasi langsung berjalan tanpa setup database — seluruh data tersimpan otomatis di folder `data/`.

---

## 🚀 Penggunaan

### Menjalankan Aplikasi

```bash
# Development / run
Aktifkan Apache (XAMPP/Laragon) lalu buka http://localhost/EduCare

# Build CSS Tailwind (opsional, setelah mengubah source CSS)
npx @tailwindcss/cli -i ./assets/css/style.css -o ./assets/css/output.css --minify

# Install dependency PHP
composer install
```

### User Guide

#### Untuk Pengguna Umum (Siswa)

1. **Registrasi/Login**: Daftar akun baru melalui halaman **Daftar/Register**, lalu login.
2. **Belajar Online**: Buka menu **Belajar** → pilih **kategori** → pilih **materi** → tonton video & baca materi, lalu kerjakan **quiz**.
3. **Sertifikat**: Setelah materi & quiz selesai, unduh **sertifikat** dari halaman hasil.
4. **SiLapor**: Buka menu **SiLapor** → buat laporan → pantau statusnya (Menunggu / Diproses / Selesai) di halaman **Status/Riwayat**.
5. **Profil & Pengaturan**: Kelola data diri di **dashboard siswa** pada menu Profil & Pengaturan.

#### Untuk Admin / Guru

1. **Akses Admin/Guru Panel**: Login dengan akun **guru** — `guru@gmail.com` / password `123` (akun disemai otomatis saat pertama login), lalu masuk ke **Dashboard Guru**.
2. **Kelola Materi**: Tambah/edit **materi**, **kategori**, dan **video** pembelajaran.
3. **Kelola Bank Soal**: Buat dan kelola **quiz** beserta jawabannya.
4. **Tindak Lanjuti Laporan**: Buka menu **Laporan**, ubah status laporan siswa ke _Diproses_ / _Selesai_.
5. **Kelola Pengguna**: Kelola data siswa & guru di menu **User**.

---

## 📚 API Documentation

> Karena aplikasi ini **PHP Native (server-side rendered)**, "API" di sini adalah **route/endpoint halaman** (bukan REST API JSON).

### Base URL

```
Development: http://localhost/EduCare
Production:  https://[domain]
```

### Endpoints

#### Autentikasi

```
GET  /auth/login.php            → Form login
POST /auth/login_process.php    → Proses login (session)
GET  /auth/register.php         → Form register
POST /auth/register_process.php → Proses register
GET  /auth/forgot-password.php  → Form lupa password
POST /auth/process_forgot.php   → Kirim email reset via SMTP
GET  /auth/reset-password.php   → Form reset password (via token)
POST /auth/process_reset.php    → Proses reset password
GET  /auth/logout.php           → Logout & hapus session
```

#### Belajar (Siswa)

```
GET /belajar/index.php         → Landing belajar
GET /belajar/materi.php        → Daftar materi per kategori
GET /belajar/detail-materi.php → Isi materi + video + tombol quiz
GET /belajar/video.php         → Halaman video
POST /belajar/quiz.php         → Submit jawaban quiz
GET /belajar/hasil.php         → Hasil quiz
GET /belajar/sertifikat.php    → Unduh sertifikat
```

#### SiLapor (Siswa)

```
GET  /silapor/index.php            → Beranda SiLapor
GET  /silapor/tambah-laporan.php   → Form laporan baru
POST /silapor/tambah-laporan.php   → Simpan laporan
GET  /silapor/riwayat.php          → Riwayat laporan siswa
GET  /silapor/status.php           → Cek status laporan
GET  /silapor/detail-laporan.php   → Detail laporan
```

> > _Untuk proyek berbasis REST API, dokumentasi endpoint dapat diganti dengan format `GET/POST/PUT/DELETE /api/...`._

---

## 🧪 Testing

Project ini **belum menggunakan automated test framework**; pengujian dilakukan secara manual melalui browser. Area yang diuji:

### Running Tests

```bash
# Jalankan aplikasi lalu uji manual per alur berikut:
komposer install & konfigurasi .env (test reset password email)
```

### Skenario Manual yang Diuji

1. **Autentikasi** — register siswa baru, login, logout, remember me, reset password via email.
2. **Proteksi role** — siswa tidak bisa mengakses halaman guru (`dashboard-guru`) dan sebaliknya.
3. **Belajar** — buka materi, tonton video, kerjakan quiz, cek hasil & progress, unduh sertifikat.
4. **XP / Leaderboard** — cek kenaikan XP & level setelah quiz, urutan leaderboard.
5. **SiLapor** — buat laporan sebagai siswa, ubah status sebagai guru, cek riwayat & detail.
6. **Notifikasi & aktivitas** — cek munculnya notifikasi/aktivitas baru di dashboard.
7. **Responsive** — uji tampilan pada layar mobile, tablet, dan desktop.

### Test Coverage

```
Statements   : - (manual testing)
Branches     : -
Functions    : -
Lines        : -
```

---

## 📄 Lisensi

Proyek ini dibuat untuk kebutuhan pembelajaran, pengembangan portofolio, dan kompetisi web development. © 2026 EduCare. All Rights Reserved.

---

<div align="center">

**Made with ❤️ by [KODINGINAJA] for ITECHNO CUP 2026**

</div>
