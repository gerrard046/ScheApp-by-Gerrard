# 📋 ScheApp Pro - Elite Task Management System

Platform manajemen tugas (Task Management) berbasis **Laravel** dengan desain modern **Glassmorphism** dan dukungan **Mobile Android**. ScheApp dirancang khusus untuk meminimalisir risiko kelalaian tugas di tengah dinamika kegiatan yang padat, dilengkapi dengan fitur kolaborasi tim dan sistem dashboard admin yang canggih.

---

## 📋 Daftar Isi
- [🎯 Deskripsi](#-deskripsi)
- [✨ Fitur Utama](#-fitur-utama)
- [📊 User Flow & Use Case](#-user-flow--use-case)
- [🏗️ Arsitektur & SDLC](#-arsitektur--sdlc)
- [🛠 Tech Stack](#-tech-stack)
- [📱 Mobile App Integration](#-mobile-app-integration)
- [🚀 Instalasi & Konfigurasi](#-instalasi--konfigurasi)
- [🔌 API Endpoints](#-api-endpoints)
- [🤝 Kontribusi](#-kontribusi)
- [📄 Lisensi](#-lisensi)

---

## 🎯 Deskripsi
**ScheApp Pro** adalah evolusi dari aplikasi manajemen jadwal personal menjadi sistem kolaborasi tim yang lengkap. Aplikasi ini memungkinkan pengguna untuk tidak hanya mengatur jadwal pribadi, tetapi juga bekerja dalam grup, menerima broadcast tugas dari admin, dan berkompetisi secara sehat lewat sistem **Gamification (XP & Leveling)**.

Dengan antarmuka **Glassmorphism** yang mewah dan responsif, ScheApp memberikan pengalaman pengguna yang premium baik di browser desktop maupun di layar smartphone melalui wrapper Android Native.

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Keamanan
- **Bcrypt Security**: Enkripsi password tingkat tinggi.
- **Google OAuth**: Login sekali klik via akun Google (Laravel Socialite).
- **IsAdmin Middleware**: Proteksi rute khusus untuk level Administrator.

### 🤝 Kolaborasi Tim (Enterprise)
- **Group Management**: Admin dapat membuat grup dan mengelola anggota tim.
- **Schedule Broadcasting**: Admin membuat satu agenda, otomatis masuk ke kalender seluruh anggota grup.

### 📈 Admin Insights (Master Analytics)
- **Global Overview**: Pantau total tugas selesai seluruh user.
- **Top Performance**: Peringkat user berdasarkan perolehan XP.
- **Burnout Risk Detection**: Deteksi dini user yang kelelahan berdasarkan jumlah tugas terlewat.

### ✅ Verification System
- **Task Approval**: Tugas yang diselesaikan user membutuhkan verifikasi Admin.
- **Bonus XP**: User mendapatkan +5 XP tambahan setelah tugas diverifikasi sukses.

### 🎮 Gamification & Productivity
- **Dynamic XP & Leveling**: Naik level setiap 100 XP.
- **Productivity Heatmap**: Visualisasi produktivitas selama 7 hari terakhir.
- **Streak System**: Menjaga konsistensi aktivitas harian.

---

## 📊 User Flow & Use Case

### Use Case Diagram
Berikut adalah diagram fungsionalitas utama ScheApp Pro:

```mermaid
usecaseDiagram
    actor "Mahasiswa (User)" as U
    actor "Administrator" as A

    package "ScheApp System" {
        usecase "Manage Personal Task" as UC1
        usecase "Join Team Group" as UC2
        usecase "Earn XP & Level Up" as UC3
        usecase "Create & Manage Group" as UC4
        usecase "Broadcast Schedule" as UC5
        usecase "Verify User Task" as UC6
        usecase "View Admin Insights" as UC7
    }

    U --> UC1
    U --> UC2
    U --> UC3
    
    A --> UC4
    A --> UC5
    A --> UC6
    A --> UC7
    A --|> U : "Inheritance Role"
```

### Development Flow
*(Dokumentasi Flowchart Tambahan dapat diisi oleh pengguna)*

---

## 🏗️ Arsitektur & SDLC

### Arsitektur Sistem
ScheApp menggunakan pola arsitektur **MVC (Model-View-Controller)** yang disediakan oleh Laravel 11. 

- **Model**: Manajemen data via Eloquent ORM (User, Schedule, Group, SubTask).
- **View**: Template mesin Blade dengan gabungan Vanilla CSS (Glassmorphism) & JavaScript (Alpine.js).
- **Controller**: Logika bisnis yang memisahkan antara endpoint Web dan fungsionalitas Admin.

### Metode Pengembangan (SDLC)
Aplikasi ini dikembangkan menggunakan metode **Waterfall**, yang terdiri dari tahapan terstruktur:

1.  **Requirement Analysis**: Identifikasi kebutuhan mahasiswa Poltek SSN terhadap manajemen waktu.
2.  **System Design**: Perancangan skema database, UI Glassmorphism, dan alur kolaborasi tim.
3.  **Implementation**: Koding backend (Laravel), frontend (Blade/CSS), dan mobile wrapper.
4.  **Testing**: Pengujian fungsionalitas (Black Box) dan verifikasi alur verifikasi admin.
5.  **Deployment**: Push ke GitHub dan persiapan template Android Studio.

---

## 🛠 Tech Stack
- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL / SQLite (Development)
- **Frontend Layer**: Blade Templates, Vanilla CSS, Alpine.js
- **Mobile Layer**: Android Studio (Java & Kotlin Wrapper)
- **Authentication**: Laravel Socialite & Custom Auth Middleware

---

## 📱 Mobile App Integration
ScheApp kini tersedia dalam versi Android melalui pendekatan **WebView Native Wrapper**.

### Prasyarat Mobile
- Android Studio Jellyfish (atau lebih baru)
- Android Device / Emulator (API 24+)

### Dokumentasi Source Code
Source code untuk Android Studio tersedia di folder:
📁 `android_studio_kotlin_template`

---

## 🚀 Instalasi & Konfigurasi

### Step 1: Clone & Setup
```bash
git clone https://github.com/gerrard046/ScheApp-by-Gerrard
cd ScheApp-by-Gerrard
composer install
npm install
```

### Step 2: Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan sesuaikan database serta API Key Google:
```env
DB_CONNECTION=mysql
DB_DATABASE=scheapp
GOOGLE_CLIENT_ID=your_id
GOOGLE_CLIENT_SECRET=your_secret
```

### Step 3: Database & Key
```bash
php artisan key:generate
php artisan migrate
```

### Step 4: Menjalankan Server
```bash
# Agar bisa diakses dari HP di jaringan yang sama
php artisan serve --host=0.0.0.0
```

---

## 🔌 API Endpoints
Base URL: `http://localhost:8000/api`

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/tasks` | Mengambil semua daftar tugas |
| POST | `/api/tasks` | Menambahkan tugas baru |
| PUT | `/api/tasks/{id}` | Memperbarui status tugas |

---

## 🤝 Kontribusi
Kontribusi sangat terbuka! Silakan lakukan **Fork** dan **Pull Request** ke branch `main`. Pastikan mengikuti standar kodingan Laravel.

---

## 📄 Lisensi
Projek ini dibuat untuk memenuhi tugas akademik (UAS). Lisensi: **MIT**.

---

**Dibuat oleh:** [Reiza Gerrard](https://github.com/gerrard046) 
**Project Info:** Pengembangan Aplikasi Penjadwalan Dinamis Poltek SSN.
