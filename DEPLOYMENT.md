# 🚀 Panduan Deploy ScheApp ke Hosting (Laravel Cloud)

Panduan meng-online-kan ScheApp 24 jam dengan alamat `https://...` —
data tersimpan permanen di cloud dan aplikasi Android tinggal diarahkan
ke URL baru.

## Kenapa Laravel Cloud?
- Ada **paket gratis (Sandbox)** — cukup untuk demo/tugas kuliah
- Buatan tim Laravel sendiri: auto-detect project, nyaris tanpa konfigurasi
- Login pakai akun GitHub, deploy langsung dari repo ini
- Dapat subdomain `https://xxx.laravel.cloud` + HTTPS otomatis

---

## Langkah 1 — Daftar & Hubungkan Repo

1. Buka **https://cloud.laravel.com** → **Sign in with GitHub**
2. Izinkan akses ke akun GitHub `gerrard046`
3. Klik **New Application** (atau Create App)
4. Pilih repository **ScheApp-by-Gerrard**
5. Pilih branch: **`claude/loving-keller-tkzdf`**
6. Laravel Cloud otomatis mendeteksi ini project Laravel ✅

## Langkah 2 — Tambah Database

1. Di halaman aplikasi, cari bagian **Database** → **Create Database**
2. Pilih **Serverless Postgres** (tersedia di paket gratis)
3. Laravel Cloud otomatis menyuntikkan kredensial (`DB_*`) ke environment —
   tidak perlu menyalin manual

> Kenapa bukan SQLite? Filesystem di cloud bersifat sementara (hilang tiap
> deploy ulang). Database terpisah = data benar-benar permanen.

## Langkah 3 — Environment Variables

Di menu **Environment / Settings**, pastikan variabel berikut
(tambahkan yang belum ada):

| Variabel | Nilai | Alasan |
|---|---|---|
| `APP_ENV` | `production` | Mode production (HSTS aktif) |
| `APP_DEBUG` | `false` | Jangan bocorkan detail error |
| `SESSION_DRIVER` | `database` | Session awet walau instance di-restart (hindari 419) |
| `CACHE_STORE` | `database` | Sama — filesystem cloud tidak permanen |
| `QUEUE_CONNECTION` | `sync` | Tidak ada worker di paket gratis; notifikasi diproses langsung |

`APP_KEY` dibuat otomatis oleh Laravel Cloud. `APP_URL` diisi otomatis.

## Langkah 4 — Deploy Command

Di pengaturan **Deploy commands**, pastikan ada:

```
php artisan migrate --force
```

agar tabel dibuat/diperbarui otomatis setiap deploy.

## Langkah 5 — Deploy!

1. Klik **Deploy**
2. Tunggu build selesai (beberapa menit)
3. Buka URL yang diberikan, mis. `https://scheapp-xxxx.laravel.cloud`
4. **Daftar akun pertama kamu** — akun pertama otomatis jadi **admin**! 👑

## Langkah 6 — Arahkan Aplikasi Android ke Cloud

1. Buka `android/local.properties`, ubah:
   ```
   scheapp.baseUrl=https://scheapp-xxxx.laravel.cloud
   ```
2. Android Studio → **File → Sync Project with Gradle Files** → **Run**
3. Sekarang APK-mu jalan **tanpa laptop nyala** — dari jaringan mana pun 🎉

> Build release sudah otomatis menuntut HTTPS (network_security_config),
> dan URL cloud memang HTTPS — pas.

---

## Catatan & Batasan Paket Gratis

- **Hibernasi**: app "tidur" saat lama tidak diakses; request pertama
  setelah tidur agak lambat (beberapa detik). Data TIDAK hilang.
- **File upload** (foto bukti, lampiran) tersimpan di filesystem sementara —
  bisa hilang saat deploy ulang. Untuk tugas kuliah biasanya tidak masalah;
  solusi permanennya pakai S3/R2 (di luar cakupan panduan ini).
- Butuh reset data? Jalankan `php artisan migrate:fresh --force` dari menu
  Commands di dashboard.

## Alternatif Hosting Lain

| Hosting | Gratis? | Catatan |
|---|---|---|
| **Railway** | Trial $5 | Auto-detect Laravel (Nixpacks), plugin MySQL, kadang minta kartu |
| **Render** | Ya (terbatas) | Butuh Dockerfile untuk PHP; Postgres gratis kedaluwarsa 30 hari |
| **Shared hosting** (cPanel) | Murah | Upload manual, cocok kalau kampus menyediakan |

## Troubleshooting

| Gejala | Penyebab | Solusi |
|---|---|---|
| 419 di production | Proxy tidak dipercaya | Sudah ditangani: `trustProxies(at: '*')` di `bootstrap/app.php` |
| Mixed content / asset http | `APP_URL` masih http | Set `APP_URL=https://...` di environment |
| Tabel tidak ada | Migrasi belum jalan | Pastikan deploy command `php artisan migrate --force` |
| Notifikasi tidak muncul | Queue tanpa worker | Set `QUEUE_CONNECTION=sync` |
