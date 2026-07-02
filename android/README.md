# 📱 ScheApp Android (WebView Wrapper)

Project Android Studio **siap pakai** — membungkus web app Laravel ScheApp
menjadi aplikasi Android. Tidak perlu edit kode apa pun.

## Cara Menjalankan (Emulator)

1. **Nyalakan server Laravel** di laptop (CMD, folder repo):
   ```
   php artisan serve --host=0.0.0.0 --port=8000
   ```
2. **Buka folder ini di Android Studio**: `File → Open` → pilih folder `android`
   (bukan root repo!) → tunggu Gradle sync selesai.
3. Klik **▶ Run**. Selesai — app terhubung otomatis ke `http://10.0.2.2:8000`.

## Pakai HP Fisik

1. HP & laptop harus di **WiFi yang sama**.
2. Cek IP laptop: `ipconfig` → catat IPv4 (mis. `192.168.1.10`).
3. Buka `local.properties` (di folder ini), tambahkan:
   ```
   scheapp.baseUrl=http://192.168.1.10:8000
   ```
4. `File → Sync Project with Gradle Files` → Run ke HP (aktifkan USB debugging).

## Fitur Konfigurasi & Keamanan

| Aspek | Implementasi |
|---|---|
| URL server | `local.properties` → `BuildConfig.BASE_URL` (tidak hardcode, tidak masuk git) |
| Fallback | Debug: otomatis `10.0.2.2:8000` • Release: wajib diisi atau app menolak jalan |
| Cookie session Laravel | `CookieManager` aktif + `flush()` saat app di-background |
| CSRF Laravel | Berfungsi normal — cookie & DOM storage aktif, tidak perlu menonaktifkan apa pun |
| HTTP vs HTTPS | `network_security_config.xml`: debug bebas HTTP (untuk IP lokal), release **wajib HTTPS** |
| Link eksternal | Dibuka di browser HP; domain sendiri & OAuth Google tetap di dalam app |
| Error koneksi | Halaman fallback lokal (assets/error.html) bertema Arctic Breeze + tombol retry |
| Akses file | `allowFileAccess=false` — JavaScript tidak bisa membaca file perangkat |
