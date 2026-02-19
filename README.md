# ✨ ScheApp: Modern Task & Analytics Dashboard 🚀

[![Laravel v12](https://img.shields.io/badge/Laravel-v12-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![SQLite Database](https://img.shields.io/badge/Database-SQLite-003B57?style=for-the-badge&logo=sqlite)](https://www.sqlite.org/)
[![Vite JS/CSS Bundler](https://img.shields.io/badge/Bundler-Vite-646CFF?style=for-the-badge&logo=vite)](https://vitejs.dev/)
[![Tailwind CSS](https://img.shields.io/badge/Styling-TailwindCSS-06B6D4?style=for-the-badge&logo=tailwindcss)](https://tailwindcss.com)

**ScheApp** adalah aplikasi manajemen agenda harian yang menggabungkan efisiensi operasional dengan analitik performa dalam antarmuka *Glassmorphism* yang elegan.

---

## 📸 Tampilan Aplikasi

| Dashboard Statistik | Daftar Agenda |
| <img width="1827" height="248" alt="image" src="https://github.com/user-attachments/assets/bd181655-d841-47ba-9ae1-76f10f3c263d" />
<img width="1827" height="248" alt="image" src="https://github.com/user-attachments/assets/bd181655-d841-47ba-9ae1-76f10f3c263d" />
| :---: |
| ![Dashboard](https://raw.githubusercontent.com/google/gemini-assets/main/dashboard-placeholder.png) | ![List](https://raw.githubusercontent.com/google/gemini-assets/main/list-placeholder.png) |

---

## ✨ Fitur Unggulan

### 📊 Dashboard Analitik
* **Visualisasi Produktivitas**: Menggunakan *Doughnut Chart* (Chart.js) untuk memantau progres tugas secara visual.
* **Statistik Real-time**: Kalkulasi otomatis total agenda, tugas selesai, dan skor produktivitas.
* **Glassmorphism UI**: Antarmuka transparan dengan efek blur yang modern dan responsif.

### 📅 Manajemen Agenda
* **Smart Tracking**: Klasifikasi tugas berdasarkan kategori (Olahraga, Belajar, Rapat) dan prioritas.
* **Live Interaction**: Tandai tugas selesai secara instan menggunakan sistem AJAX tanpa *reload* halaman.
* **Search Engine**: Pencarian agenda

2. Setup Database
Bash
touch database/database.sqlite
php artisan migrate

3. Menjalankan Aplikasi
Buka dua terminal terpisah:

Terminal 1 (Vite Assets):

Bash
npm install
npm run dev
Terminal 2 (Laravel Server):

Bash
php artisan serve
