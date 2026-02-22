📋 ScheApp - Dashboard Manajemen Tugas Poltek SSN
Platform manajemen tugas (Task Management) berbasis Laravel dengan desain modern Glassmorphism untuk membantu mahasiswa Poltek SSN mengelola beban kegiatan kedinasan dan akademik yang dinamis secara efisien.

💻 Bahasa & Teknologi yang Digunakan:
PHP 8.2+ (Bahasa pemrograman utama/Backend)

JavaScript (Interaktivitas frontend dengan Alpine.js)

SQL (Manajemen database MySQL)

HTML5 & CSS3 (Struktur dan styling dengan Tailwind CSS)

📖 Daftar Isi

1.1 Latar Belakang Masalah

1.2 Tujuan Pengembangan Aplikasi


2.1 Autentikasi Keamanan (Bcrypt)

2.2 Integrasi Google OAuth 2.0

2.3 Manajemen Penjadwalan Dinamis

2.4 Dashboard Analytics & Visualisasi


3.1 Backend & Database

3.2 Frontend & UI Framework

3.3 Development Tools


4.1 Analisis Kebutuhan Mahasiswa Poltek SSN

4.2 Skenario Penggunaan Harian


5.1 Kebutuhan Perangkat Keras (Hardware)

5.2 Kebutuhan Perangkat Lunak (Software)


6.1 Kloning Repository

6.2 Pengaturan Environment (.env)


7.1 Migrasi & Seeding Database

7.2 Kompilasi Asset Frontend (Vite)


8.1 Layout Dashboard Utama

8.2 Komponen Form & Modal


9.1 Arsitektur Folder Laravel


10.1 Endpoint Task Management


11.1 Metode Black Box Testing


12.1 Metodologi Waterfall

12.2 State Diagram & Use Case


13.1 Analisis Algoritma Bcrypt


14.1 Kontribusi & Lisensi

🎯 1. Deskripsi Projek
Aplikasi ScheApp dikembangkan sebagai solusi digital bagi mahasiswa Politeknik Siber dan Sandi Negara dalam menghadapi dinamika kegiatan kampus yang sangat tinggi dan padat setiap harinya. Melalui pemanfaatan framework Laravel 11 dan desain Glassmorphism yang modern, aplikasi ini hadir untuk meminimalisir risiko kelalaian tugas atau tanggung jawab yang sering kali terlupakan akibat jadwal yang berubah-ubah. Sistem ini mengintegrasikan fitur manajemen jadwal yang presisi di mana pengguna dapat menentukan jam, hari, hingga tanggal tenggat waktu secara spesifik untuk setiap rencana kegiatan mereka. Keamanan data pengguna menjadi perhatian utama dalam pengembangan ini dengan diterapkannya algoritma hashing Bcrypt untuk melindungi kredensial login dari potensi ancaman siber. Dashboard analitik pada ScheApp memberikan ringkasan statistik yang informatif mengenai jumlah tanggungan aktif sehingga mahasiswa dapat menentukan skala prioritas secara lebih objektif dan terukur. Antarmuka yang responsif memastikan aplikasi dapat diakses dengan optimal melalui berbagai perangkat seperti laptop maupun smartphone di area ksatrian Poltek SSN. Selain itu, integrasi Google OAuth melalui Laravel Socialite memberikan kemudahan akses bagi pengguna tanpa harus mengorbankan standar keamanan sistem yang telah ditetapkan. Setiap data jadwal yang tersimpan direkam secara kronologis ke dalam database MySQL untuk memudahkan pemantauan riwayat progres akademik pengguna secara jangka panjang. Implementasi kodingan yang modular dengan pola MVC memastikan aplikasi ini memiliki skalabilitas yang baik untuk pengembangan fitur-fitur di masa yang akan datang. Dengan ScheApp, manajemen waktu mahasiswa tidak lagi menjadi kendala manual, melainkan sebuah proses digital yang terstruktur, aman, dan sangat user-friendly bagi seluruh civitas akademika.

✨ 2. Fitur Utama
🔐 Autentikasi & Akun
Secure Registration: Pendaftaran akun baru dengan validasi email unik.

Bcrypt Encryption: Semua password di-hash menggunakan standar Bcrypt 10 rounds.

Google OAuth 2.0: Login sekali klik menggunakan akun Google via Laravel Socialite.

Profile Management: Fitur update nama, email, dan penggantian password secara mandiri.

Session Security: Perlindungan terhadap serangan Session Hijacking.

📝 Manajemen Penjadwalan (Core)
CRUD Operations: Create, Read, Update, Delete jadwal kegiatan secara real-time.

Precision Timing: Input spesifik Jam (Time), Hari (Day), dan Tanggal (Date).

Status Tracking: Klasifikasi (Belum Mulai, Proses, Selesai).

Priority Levels: Penandaan tingkat urgensi (Low, Medium, High) dengan badge warna.

Task Archiving: Menyimpan riwayat tugas yang telah selesai dikerjakan.

📊 Dashboard Analytics
Counter Cards: Ringkasan jumlah total tugas, tugas pending, dan tugas sukses.

Percentage Progress: Visualisasi progres penyelesaian tugas mahasiswa.

Due Soon Alerts: Menampilkan tugas yang mendekati tenggat waktu di baris teratas.

🛠 3. Tech Stack
👤 4. User Story: Manajemen Tugas di Lingkungan Poltek SSN
Sebagai mahasiswa di Politeknik Siber dan Sandi Negara, saya sering menghadapi jadwal yang berubah secara mendadak karena kegiatan ksatrian yang cukup padat. Kesibukan rutin seperti latihan Peraturan Baris Berbaris (PBB) tambahan, persiapan kunjungan pejabat yang tiba-tiba, hingga urusan organisasi yang mengharuskan kami untuk selalu standby sering kali membuat fokus terhadap tugas akademik terpecah. Kondisi ini membuat saya sulit mengingat semua tanggungan tugas jika hanya mengandalkan catatan manual atau ingatan saja. Oleh karena itu, saya membutuhkan aplikasi ScheApp untuk mencatat setiap jadwal dan tugas secara teratur. Saya ingin fitur yang memungkinkan saya memasukkan judul tugas, jam, serta tanggal tenggat waktu dengan jelas agar saya bisa melihat sisa waktu yang dimiliki di tengah kesibukan lapangan. Dengan adanya tingkatan prioritas dan status tugas, saya bisa langsung menentukan mana pekerjaan yang harus segera diselesaikan setelah kegiatan kedinasan selesai tanpa harus bingung memulainya dari mana.

Di sisi lain, sebagai mahasiswa yang belajar di bidang keamanan siber, saya tetap memperhatikan efisiensi dan keamanan aplikasi yang saya gunakan sehari-hari. Saya ingin proses masuk ke aplikasi bisa dilakukan dengan cepat melalui akun Google karena waktu istirahat yang kami miliki sering kali terbatas. Meskipun aksesnya cepat, saya tetap menginginkan keamanan data yang baik dengan adanya enkripsi password menggunakan Bcrypt di dalam sistem. Saya juga membutuhkan halaman dashboard yang menampilkan statistik jumlah tugas yang belum selesai dan yang sudah dikerjakan. Dengan melihat data tersebut, saya bisa memantau apakah urusan organisasi dan latihan lapangan saya sudah seimbang dengan tanggung jawab akademik saya sebagai mahasiswa. Secara keseluruhan, ScheApp diharapkan dapat membantu saya menjadi lebih disiplin dalam mengatur waktu di tengah dinamika kegiatan ksatrian yang sangat dinamis.

📋 5. Kebutuhan Sistem (System Requirements)
3.1 Kebutuhan Perangkat Keras (Hardware)
3.2 Kebutuhan Perangkat Lunak (Software)
Server Side: PHP 8.2+, MySQL 8.0+, Apache/Nginx.

Dev Tools: Composer 2.x, Node.js 18+, NPM.

Client Side: Browser modern (Chrome/Firefox/Edge) dengan dukungan HTML5/CSS3.

Account: Akun Gmail aktif untuk Google OAuth.

🚀 6. Instalasi & Konfigurasi
Kloning Repository

Instalasi Dependency

Setup Environment
Salin file .env.example menjadi .env dan atur konfigurasi database serta Google Client ID.

Generate Key & Link Storage

▶️ 7. Menjalankan Aplikasi
Migrasi Database

Build Frontend

Jalankan Server

📝 8. Dokumentasi Antarmuka (UI/UX)
📊 Dashboard Utama: Menampilkan Greeting dinamis sesuai waktu, Stat Cards bergaya Glassmorphism, dan tabel tugas terbaru dengan efek hover.

➕ Create Task Form: Modal input terintegrasi untuk judul tugas, Jam (24 jam), Tanggal (Date picker), dan dropdown prioritas warna.

📁 9. Struktur Project (Detail)
🔌 10. API Documentation
Base URL: http://localhost:8000/api

Endpoints:

GET /api/tasks : Mengambil semua daftar tugas.

POST /api/tasks : Menambahkan tugas baru.

PUT /api/tasks/{id} : Memperbarui status tugas.

DELETE /api/tasks/{id} : Menghapus tugas.

📐 12. SDLC (Software Development Life Cycle)
Pengembangan aplikasi ScheApp mengikuti model Waterfall. Metodologi ini dipilih karena setiap tahapan harus diselesaikan secara berurutan agar pengembangan tetap terstruktur, terutama dalam memastikan aspek keamanan (Bcrypt) dan integrasi database telah matang sebelum lanjut ke tahap berikutnya.

1. Tahap Perencanaan & Analisis (Requirements Analysis)
Pada tahap awal, dilakukan observasi terhadap ritme kegiatan di Poltek SSN. Ditemukan masalah utama yaitu banyaknya kegiatan mendadak (PBB, kunjungan pejabat, organisasi) yang menyebabkan mahasiswa sulit melacak tugas akademik.

Kebutuhan Fungsional: Sistem harus bisa melakukan CRUD jadwal, autentikasi Bcrypt, login Google, dan menampilkan statistik dashboard.

Kebutuhan Non-fungsional: Keamanan data kredensial, antarmuka yang responsif (Mobile-friendly), dan performa yang cepat.

2. Tahap Desain Sistem (System Design)
Tahap ini menerjemahkan kebutuhan menjadi cetak biru teknis sebelum proses koding dimulai.

Arsitektur: Menggunakan pola MVC (Model-View-Controller) dari Laravel 11.

Database: Perancangan skema database MySQL untuk tabel users dan tasks.

UI/UX: Perancangan antarmuka dengan konsep Glassmorphism menggunakan Tailwind CSS untuk memberikan kesan modern dan bersih.

3. Tahap Implementasi (Coding/Construction)
Tahap di mana desain diubah menjadi baris kode program.

Backend: Membangun logika autentikasi menggunakan Laravel Breeze dan enkripsi Bcrypt. Mengintegrasikan Laravel Socialite untuk fitur Login Google.

Frontend: Implementasi Blade Templates dan Tailwind CSS. Menambahkan interaktivitas menggunakan Alpine.js agar aplikasi terasa ringan tanpa banyak reload halaman.

4. Tahap Pengujian (Testing)
Sebelum diserahkan, aplikasi diuji secara menyeluruh menggunakan metode Black Box Testing.

Unit Testing: Memastikan fungsi hashing Bcrypt bekerja (password tidak tersimpan dalam teks biasa).

Functional Testing: Mencoba input jadwal dengan berbagai kondisi (data kosong, format tanggal salah, dll) untuk memastikan validasi sistem berjalan.

User Acceptance: Memastikan dashboard menampilkan jumlah "tanggungan" yang akurat sesuai data di database.

5. Tahap Deployment & Pemeliharaan (Maintenance)
Aplikasi di-deploy ke server lokal (Localhost) atau hosting untuk digunakan.

Maintenance: Melakukan update berkala pada library/dependencies melalui Composer dan NPM untuk menambal celah keamanan dan memastikan kompatibilitas framework tetap terjaga.

State Diagram - Task Flow
🛡 12. Keamanan Sistem (Bcrypt Analysis)
ScheApp menggunakan Bcrypt melalui facade Hash::make() Laravel. Algoritma ini memiliki cost factor yang membuatnya lambat diproses secara brute-force, memberikan perlindungan ekstra bagi data mahasiswa.

🤝 13. Kontribusi & Lisensi
Projek ini dibuat untuk memenuhi tugas akademik (UAS). Lisensi: MIT.

Dibuat oleh: Reiza Gerrard
