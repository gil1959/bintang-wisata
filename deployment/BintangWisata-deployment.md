# Menyiapkan API Bintang Wisata di cPanel

Pilih database aplikasi berdasarkan nilai `DB_DATABASE` di `.env`. Impor `BintangWisata-api-database.sql` melalui tab Import di phpMyAdmin. Error 1044 yang menyebut `information_schema` berarti database sistem yang dipilih. Gunakan database aplikasi, lalu impor ulang.

SQL menambahkan tabel API dan identitas Google, serta mencatat migration di tabel `migrations`. Tidak ada perintah penghapusan data. `CREATE TABLE IF NOT EXISTS` tidak memperbaiki struktur tabel yang sudah ada tetapi berbeda. Kalau checkout masih error, lihat pesan terbaru di `storage/logs/laravel.log`.

Upload kode web terbaru termasuk `routes`, `app`, `config`, `resources`, dan migration. Jalankan Bersihkan cache situs melalui panel admin setelah upload. Perubahan ini belum dikirim ke GitHub atau production otomatis.

## Google

1. Buat OAuth Client bertipe Web application di Google Cloud dengan consent screen yang sesuai.
2. Isi Authorized redirect URI: `https://bintangwisataholiday.com/auth/google/callback`.
3. Tambahkan `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` ke `.env` hosting melalui File Manager. Secret hanya berada di server.
4. Bersihkan cache konfigurasi situs melalui panel admin.
5. Aktifkan Login Google di Pengaturan umum. Tombol hanya tampil jika pengaturan aktif, kredensial lengkap, dan tabel `google_identities` tersedia.
6. Pasang APK terbaru. Android membaca `auth/config` saat layar login muncul dan ketika aplikasi kembali aktif.

Google dibuka melalui browser resmi untuk autentikasi, lalu pengguna kembali ke Android. Token aplikasi ditukar melalui kode permintaan sekali pakai yang terikat verifier dari perangkat. Registrasi baru selalu mendapatkan role user. Akun partner atau admin yang sudah terverifikasi mempertahankan role yang ada. Email akun manual yang belum terverifikasi harus diverifikasi lebih dahulu.

Dokumentasi: https://developers.google.com/identity/protocols/oauth2/web-server

## Pembayaran

Aktifkan rekening transfer atau gateway beserta channel melalui panel admin. API hanya mengirim opsi yang aktif, tanpa kredensial gateway.

Transfer manual menampilkan rekening dan total server termasuk kode unik. Upload bukti JPG atau PNG maksimal 2 MB. Status menunggu verifikasi berubah setelah admin memverifikasi.

Gateway membuka URL pembayaran HTTPS dari penyedia setelah data penagihan tersimpan. Saat kembali ke aplikasi, status dimuat ulang. Konfigurasikan webhook penyedia ke endpoint `/api/webhooks/{nama_gateway}` sesuai pengaturan web. Pembayaran dinyatakan berhasil berdasarkan status server, bukan hanya karena pengguna kembali ke aplikasi.

Pengujian lokal memakai database SQLite di memori, Google HTTP palsu, gateway palsu dan callback webhook simulasi. Tidak ada pembayaran nyata dalam pengujian ini.
