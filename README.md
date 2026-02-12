# 🗓️ Dokumentasi Project (Progress Report)

## ScheApp - Platform Manajemen Jadwal Pintar

![Laravel](https://img.shields.io/badge/Laravel-11-red) ![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue) ![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.x-cyan) ![SQLite](https://img.shields.io/badge/SQLite-3.x-lightgrey)

---

## 📖 Deskripsi
**ScheApp** adalah aplikasi manajemen jadwal berbasis web yang dirancang untuk membantu pengguna mengatur agenda harian secara efisien. Proyek ini menggunakan Laravel 11 dan SQLite sebagai basis datanya.

### Tujuan Utama:
* Menyediakan platform pencatatan jadwal yang intuitif.
* Mengatur prioritas tugas agar pengguna tetap produktif.
* Memungkinkan admin mengelola data secara efisien.

## 🛠️ Tech Stack
* **Backend:** Laravel 11
* **Frontend:** Blade Templates + Alpine.js
* **Styling:** TailwindCSS 4
* **Database:** SQLite
* **Build Tool:** Vite

---

## 📋 Scrum Product Backlog
### EPIC: User Authentication & Authorization
* **FEATURE 1: User Registration**
  * **User Story 1.1:** Sebagai pengguna, saya ingin mendaftarkan akun agar dapat mengakses sistem ScheApp.
  * **Acceptance Criteria:** Sistem menyediakan form registrasi berisi nama, email, dan kata sandi.

---

## 🔄 SDLC (Software Development Life Cycle)
Menggunakan metodologi **Waterfall dengan Iterasi**:

| Phase | Aktivitas | Output |
| :--- | :--- | :--- |
| **1. Planning** | Requirement gathering, user story | PRD, User Stories |
| **2. Analysis** | SRS, feature prioritization | Feature List, SRS Doc |
| **3. Design** | UML diagrams, database design | UML, ERD, Mockups |
| **4. Development** | Coding, unit testing | Source code, tests |
| **5. Testing** | Feature testing, security audit | Test cases (16 tests) |

---

## 📸 Preview Interface
Berikut adalah tampilan antarmuka dari ScheApp:

### 1. Halaman Login
Halaman Login(img/login.png)<img width="1919" height="1007" alt="Screenshot 2026-02-12 095852" src="https://github.com/user-attachments/assets/6209ad8c-3c2a-4ecd-b720-988a7e852809" />


### 2. Dashboard Jadwal
[Dashboard(img/dashboard.png)<img width="1917" height="1004" alt="Screenshot 2026-02-12 100456" src="https://github.com/user-attachments/assets/56e14e31-6ac1-4f1a-b8f6-4eebad66032c" />


### 3. Tambah Jadwal
Tambah Jadwal(img/create_schedule.png)<img width="1919" height="1005" alt="Screenshot 2026-02-12 100538" src="https://github.com/user-attachments/assets/c6c478ff-8b81-4994-8e9e-561ef5bae617" />

📊 Database Design – Entity Relationship Diagram (ERD)
📌 Overview

Pada tahap Design (SDLC Phase 3), dilakukan perancangan struktur basis data menggunakan Entity Relationship Diagram (ERD).
ERD ini bertujuan untuk menggambarkan hubungan antar data utama dalam aplikasi ScheApp – Platform Manajemen Jadwal Pintar agar sistem terstruktur, konsisten, dan mudah dikembangkan.

Aplikasi ScheApp memiliki tiga entitas utama, yaitu Roles, Users, dan Schedules, yang saling terhubung untuk mendukung fitur autentikasi pengguna dan manajemen jadwal berbasis akun.

🗂️ Entitas dan Atribut
1️⃣ Entity: ROLES

Digunakan untuk mengelola hak akses pengguna dalam sistem (Admin dan User).

Attribute	Type	Description
id	Primary Key	Identitas unik role
name	String	Jenis role (admin / user)
created_at	Timestamp	Waktu pembuatan data
updated_at	Timestamp	Waktu pembaruan data

Fungsi:
Menentukan peran pengguna dalam sistem sehingga pengelolaan hak akses lebih terstruktur.

2️⃣ Entity: USERS

Menyimpan data akun pengguna yang dapat mengakses aplikasi ScheApp.

Attribute	Type	Description
id	Primary Key	Identitas unik user
role_id	Foreign Key	Relasi ke tabel roles
name	String	Nama pengguna
email	String (Unique)	Email untuk login
password	String	Password terenkripsi
created_at	Timestamp	Waktu pembuatan akun
updated_at	Timestamp	Waktu pembaruan akun

Fungsi:
Sebagai identitas pengguna yang dapat membuat dan mengelola jadwal.

3️⃣ Entity: SCHEDULES

Menyimpan data jadwal kegiatan yang dibuat oleh pengguna.

Attribute	Type	Description
id	Primary Key	Identitas unik jadwal
user_id	Foreign Key	Pemilik jadwal
title	String	Judul kegiatan
description	Text	Deskripsi kegiatan
date	Date	Tanggal kegiatan
start_time	Time	Waktu mulai
end_time	Time	Waktu selesai
priority	Enum	low / medium / high
status	Enum	pending / done
created_at	Timestamp	Waktu pembuatan
updated_at	Timestamp	Waktu pembaruan

Fungsi:
Mengelola aktivitas harian pengguna beserta prioritas dan status penyelesaiannya.

🔗 Relasi Antar Entitas
Entity 1	Relationship	Entity 2
Roles	1 : N	Users
Users	1 : N	Schedules

Penjelasan Relasi:

Satu Role dapat dimiliki oleh banyak User

Satu User dapat memiliki banyak Schedule

Satu Schedule hanya dimiliki oleh satu User

<img width="617" height="499" alt="Screenshot 2026-02-12 104303" src="https://github.com/user-attachments/assets/38466873-3f2f-415d-8e4f-c9762ae9b42f" />

