Siap. Kita rapikan bahasanya supaya lebih formal, akademis, dan profesional—cocok untuk laporan proyek, README kampus, atau dokumentasi Scrum.

📘 SCRUM PRODUCT BACKLOG
ScheApp – User Authentication Module
🎯 EPIC: User Authentication & Authorization

Deskripsi:
Menyediakan mekanisme autentikasi pengguna yang aman dan andal agar hanya pengguna terdaftar yang dapat mengakses fitur aplikasi ScheApp.

📌 FEATURE 1: User Registration
User Story 1.1 – Registrasi Akun

Sebagai calon pengguna
Saya ingin mendaftarkan akun menggunakan email dan kata sandi
Sehingga saya dapat mengakses sistem ScheApp

Acceptance Criteria:

Sistem menyediakan form registrasi berisi nama, email, dan kata sandi.

Email yang didaftarkan harus bersifat unik.

Kata sandi dienkripsi menggunakan algoritma bcrypt.

Sistem menyimpan data pengguna ke dalam basis data.

Pengguna diarahkan ke halaman login atau dashboard setelah registrasi berhasil.

Sistem menampilkan pesan kesalahan jika validasi gagal.

Estimasi Story Point: 5

📌 FEATURE 2: User Login
User Story 2.1 – Login Pengguna

Sebagai pengguna terdaftar
Saya ingin masuk ke sistem menggunakan email dan kata sandi
Sehingga saya dapat mengakses fitur ScheApp sesuai hak akses

Acceptance Criteria:

Sistem menyediakan form login (email dan kata sandi).

Sistem memverifikasi kecocokan email dan kata sandi.

Kata sandi diverifikasi menggunakan bcrypt.

Jika autentikasi berhasil, pengguna diarahkan ke dashboard.

Jika autentikasi gagal, sistem menampilkan pesan kesalahan.

Estimasi Story Point: 3

📌 FEATURE 3: User Logout
User Story 3.1 – Logout Pengguna

Sebagai pengguna
Saya ingin keluar dari sistem
Sehingga keamanan akun saya tetap terjaga

Acceptance Criteria:

Sistem menyediakan fitur logout.

Session pengguna dihapus.

Pengguna diarahkan kembali ke halaman login.

Estimasi Story Point: 2

📌 FEATURE 4: Input Validation & Security
User Story 4.1 – Validasi Data

Sebagai pengguna
Saya ingin sistem memvalidasi data yang saya masukkan
Sehingga kesalahan input dapat dicegah sejak awal

Acceptance Criteria:

Seluruh field wajib diisi.

Format email harus valid.

Kata sandi memiliki panjang minimal 8 karakter.

Pesan validasi ditampilkan secara informatif.

Estimasi Story Point: 3

User Story 4.2 – Keamanan Kata Sandi

Sebagai pengguna
Saya ingin kata sandi saya disimpan secara aman
Sehingga data akun terlindungi dari penyalahgunaan

Acceptance Criteria:

Kata sandi tidak disimpan dalam bentuk plain text.

Sistem menggunakan hashing bcrypt bawaan Laravel.

Estimasi Story Point: 2

🗓️ Contoh Sprint Goal

Sprint 1 – Authentication Module

Mengimplementasikan fitur registrasi, login, logout, dan validasi data pengguna dengan standar keamanan aplikasi web.

📊 USE CASE DIAGRAM – ScheApp
Aktor:

User

Use Case:

Register Account

Login

Logout

Access Dashboard

📐 Use Case Diagram (PlantUML)

Dapat digunakan untuk generate diagram UML secara otomatis.

@startuml
left to right direction

actor User

rectangle "ScheApp" {
  User --> (Register Account)
  User --> (Login)
  User --> (Logout)
  User --> (Access Dashboard)

  (Register Account) --> (Encrypt Password with Bcrypt)
  (Login) --> (Verify Password)
}
@enduml

📝 Deskripsi Use Case Singkat
Use Case	Deskripsi
Register Account	Proses pendaftaran akun baru oleh pengguna
Login	Proses autentikasi pengguna
Logout	Proses mengakhiri sesi pengguna
Access Dashboard	Mengakses halaman utama aplikasi
