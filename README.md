# EduToraja — Chatbot NLP Edukasi Pariwisata Toraja

EduToraja adalah aplikasi web edukasi pariwisata dan budaya Toraja yang dibangun menggunakan PHP native dan MariaDB/MySQL. Aplikasi menyediakan informasi destinasi, budaya, materi edukasi, peta lokasi, dashboard admin, serta chatbot berbasis Natural Language Processing (NLP) dengan metode TF-IDF dan Cosine Similarity.

## Informasi Repository

**Nama repository yang disarankan:**

```text
edutoraja-chatbot-nlp
```

**Deskripsi repository GitHub:**

```text
Website edukasi pariwisata Toraja berbasis PHP dan MariaDB dengan chatbot NLP menggunakan TF-IDF dan Cosine Similarity serta dashboard admin untuk mengelola destinasi, budaya, materi, knowledge base, dan riwayat percakapan.
```

**Topics GitHub yang disarankan:**

```text
php, mariadb, mysql, nlp, chatbot, tf-idf, cosine-similarity, toraja, tourism, xampp
```

## Fitur Utama

### Halaman Pengunjung

- Informasi singkat mengenai Tana Toraja.
- Daftar destinasi wisata Toraja.
- Daftar budaya dan adat Toraja.
- Halaman materi edukasi yang lebih lengkap.
- Peta lokasi menggunakan Leaflet dan OpenStreetMap.
- Tombol petunjuk arah menuju Google Maps.
- Chatbot interaktif untuk menjawab pertanyaan tentang Toraja.
- Tampilan responsif untuk desktop dan perangkat seluler.

### Chatbot NLP

Chatbot bekerja tanpa API AI eksternal. Jawaban diperoleh dari data yang tersimpan di database melalui proses berikut:

1. Case folding atau mengubah teks menjadi huruf kecil.
2. Menghapus tanda baca.
3. Tokenisasi atau memecah kalimat menjadi kata.
4. Menghapus stopword bahasa Indonesia.
5. Stemming sederhana.
6. Menghitung bobot TF-IDF.
7. Menghitung kemiripan menggunakan Cosine Similarity.
8. Memilih jawaban dengan nilai kemiripan tertinggi.
9. Menyimpan pertanyaan, jawaban, dan skor kemiripan ke tabel `chat_logs`.

Nilai ambang kemiripan chatbot saat ini adalah `0.20` dan dapat diubah pada file:

```text
api/chat.php
```

### Dashboard Admin

- Login dan logout admin.
- Dashboard statistik data dan percakapan chatbot.
- CRUD data destinasi.
- CRUD data budaya.
- CRUD materi edukasi.
- CRUD knowledge base chatbot.
- Pengelolaan tautan Google Maps pada knowledge base.
- Pengelolaan akun admin dan foto profil.
- Riwayat chat dan skor Cosine Similarity.
- Unggah gambar untuk destinasi, budaya, materi, dan editor konten.

## Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| PHP Native | Logika aplikasi dan pemrosesan server |
| MariaDB/MySQL | Penyimpanan data |
| MySQLi | Koneksi PHP ke database |
| HTML, CSS, JavaScript | Antarmuka pengguna |
| Tailwind CSS CDN | Antarmuka dashboard admin |
| Font Awesome | Ikon halaman pengunjung |
| Material Symbols | Ikon dashboard admin |
| CKEditor 5 | Editor materi pada dashboard admin |
| Leaflet | Tampilan peta interaktif |
| OpenStreetMap | Sumber tile peta |
| Nominatim | Pencarian dan reverse geocoding lokasi |
| TF-IDF | Pembobotan kata pada chatbot |
| Cosine Similarity | Pengukuran kemiripan pertanyaan |

## Struktur Folder

```text
edukasi_pariwisata/
├── admin/                     # Dashboard dan proses CRUD admin
│   ├── index.php              # Login admin
│   ├── dashboard.php          # Dashboard statistik
│   ├── destinasi.php          # Kelola destinasi
│   ├── budaya.php             # Kelola budaya
│   ├── materi.php             # Kelola materi
│   ├── knowledge_base.php     # Kelola basis pengetahuan chatbot
│   └── chat_logs.php          # Riwayat percakapan chatbot
├── api/
│   └── chat.php               # Endpoint chatbot NLP
├── nlp/
│   └── TextProcessor.php      # Preprocessing, TF-IDF, dan Cosine Similarity
├── uploads/                   # Berkas gambar hasil unggahan
├── config.example.php         # Contoh konfigurasi database
├── config.php                 # Konfigurasi lokal, tidak dikirim ke GitHub
├── database.sql               # Struktur database dan data awal
├── index.php                  # Halaman utama
├── materi.php                 # Halaman detail materi
├── alter_db.php               # Utilitas migrasi lama
└── update_existing_maps.php   # Utilitas pembaruan koordinat data awal
```

## Persyaratan Sistem

Sebelum menjalankan aplikasi, siapkan:

- PHP 8.0 atau lebih baru.
- MariaDB 10 atau MySQL 8.
- Apache Web Server.
- Ekstensi PHP `mysqli`.
- XAMPP direkomendasikan untuk instalasi lokal.
- Koneksi internet untuk memuat Google Fonts, Tailwind CDN, Font Awesome, CKEditor, Leaflet, dan tile OpenStreetMap.

Proyek telah diperiksa menggunakan PHP 8.2 dan tidak ditemukan kesalahan sintaks pada file PHP.

## Instalasi Menggunakan XAMPP di macOS

### 1. Masuk ke folder `htdocs`

```bash
cd /Applications/XAMPP/xamppfiles/htdocs
```

### 2. Clone repository

Ganti `USERNAME_GITHUB` dengan username GitHub Anda.

```bash
git clone https://github.com/USERNAME_GITHUB/edutoraja-chatbot-nlp.git edukasi_pariwisata
```

Masuk ke folder proyek:

```bash
cd edukasi_pariwisata
```

### 3. Buat file konfigurasi

```bash
cp config.example.php config.php
```

Isi standar XAMPP adalah:

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "chatbot_toraja";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
```

### 4. Jalankan Apache dan MySQL

```bash
sudo /Applications/XAMPP/xamppfiles/xampp start
```

Periksa status:

```bash
sudo /Applications/XAMPP/xamppfiles/xampp status
```

Hasil yang diharapkan:

```text
Apache is running.
MySQL is running.
```

### 5. Import database

Gunakan perintah berikut dari Terminal macOS:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root < /Applications/XAMPP/xamppfiles/htdocs/edukasi_pariwisata/database.sql
```

Jika root menggunakan password:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root -p < /Applications/XAMPP/xamppfiles/htdocs/edukasi_pariwisata/database.sql
```

Alternatif melalui MariaDB monitor:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root
```

Kemudian jalankan:

```sql
SOURCE /Applications/XAMPP/xamppfiles/htdocs/edukasi_pariwisata/database.sql;
```

Periksa hasil import:

```sql
USE chatbot_toraja;
SHOW TABLES;
```

Tabel yang seharusnya tersedia:

```text
budaya
chat_logs
destinasi
knowledge_base
materi
users
```

### 6. Buka aplikasi

Jika Apache XAMPP menggunakan port `8080`:

```text
http://localhost:8080/edukasi_pariwisata/
```

Halaman admin:

```text
http://localhost:8080/edukasi_pariwisata/admin/
```

Jika Apache menggunakan port standar `80`:

```text
http://localhost/edukasi_pariwisata/
```

## Instalasi Menggunakan XAMPP di Windows

### 1. Clone atau salin proyek

Tempatkan proyek di:

```text
C:\xampp\htdocs\edukasi_pariwisata
```

### 2. Buat file konfigurasi

Di Command Prompt atau Git Bash:

```bash
copy config.example.php config.php
```

Anda juga dapat menyalin file secara manual melalui File Explorer.

### 3. Jalankan layanan XAMPP

Buka XAMPP Control Panel, lalu aktifkan:

- Apache
- MySQL

### 4. Import database

Buka:

```text
http://localhost/phpmyadmin/
```

Kemudian:

1. Pilih menu **Import**.
2. Pilih file `database.sql`.
3. Klik **Import** atau **Go**.

### 5. Buka aplikasi

```text
http://localhost/edukasi_pariwisata/
```

Halaman admin:

```text
http://localhost/edukasi_pariwisata/admin/
```

## Akun Admin Awal

Database menyediakan akun demo berikut:

```text
Username: admin
Password: admin123
```

Segera ubah password setelah login, khususnya apabila aplikasi akan dipasang pada server publik.

## Cara Menggunakan Aplikasi

### Sebagai Pengunjung

1. Buka halaman utama.
2. Pilih destinasi atau budaya yang ingin dipelajari.
3. Klik **Pelajari Materi** untuk membuka halaman detail.
4. Gunakan peta untuk melihat lokasi wisata.
5. Klik tombol chatbot di bagian kanan bawah.
6. Masukkan pertanyaan mengenai wisata atau budaya Toraja.

Contoh pertanyaan:

```text
Apa itu Tana Toraja?
Dimana letak Kete Kesu?
Apa itu Rambu Solo?
Bagaimana cuaca di Toraja?
Apa makanan khas Toraja?
```

### Sebagai Admin

1. Buka halaman `/admin/`.
2. Login menggunakan akun admin.
3. Gunakan menu dashboard untuk mengelola data.
4. Tambahkan destinasi, budaya, materi, dan knowledge base.
5. Periksa menu chat logs untuk melihat pertanyaan pengguna dan skor kemiripannya.

## Struktur Database

| Tabel | Fungsi |
|---|---|
| `users` | Menyimpan akun dan foto profil admin |
| `knowledge_base` | Menyimpan pertanyaan, jawaban, dan tautan peta chatbot |
| `chat_logs` | Menyimpan riwayat pertanyaan, jawaban, skor, dan waktu chat |
| `destinasi` | Menyimpan daftar destinasi wisata |
| `budaya` | Menyimpan daftar budaya Toraja |
| `materi` | Menyimpan materi lengkap, gambar, slug, koordinat, dan alamat peta |

## Konfigurasi Port Apache

Untuk mengetahui port Apache XAMPP di macOS:

```bash
grep -nE '^[[:space:]]*Listen' /Applications/XAMPP/xamppfiles/etc/httpd.conf
```

Contoh hasil:

```text
Listen 8080
```

Jika hasilnya `Listen 8080`, tambahkan `:8080` pada URL localhost.

## Troubleshooting

### `ERR_CONNECTION_REFUSED`

Periksa apakah Apache berjalan:

```bash
sudo /Applications/XAMPP/xamppfiles/xampp status
```

Periksa port yang sedang digunakan Apache:

```bash
sudo lsof -nP -iTCP -sTCP:LISTEN | grep httpd
```

### HTTP 500

Periksa log PHP atau Apache:

```bash
tail -n 80 /Applications/XAMPP/xamppfiles/logs/php_error_log
```

Jika file tersebut tidak ada:

```bash
tail -n 80 /Applications/XAMPP/xamppfiles/logs/error_log
```

### Tabel database tidak ditemukan

Import ulang `database.sql`, kemudian periksa:

```sql
USE chatbot_toraja;
SHOW TABLES;
```

### Gambar unggahan tidak tampil

Pastikan folder `uploads` dapat ditulis oleh Apache. Untuk lingkungan lokal macOS:

```bash
chmod -R 775 uploads
```

Hindari menggunakan permission `777` pada server produksi.

### Tampilan atau peta tidak termuat

Beberapa aset aplikasi menggunakan CDN dan layanan peta eksternal. Pastikan perangkat memiliki koneksi internet dan layanan CDN tidak diblokir.

## Cara Push Proyek ke GitHub

### 1. Buat repository baru di GitHub

Gunakan data berikut:

```text
Repository name: edutoraja-chatbot-nlp
Description: Website edukasi pariwisata Toraja berbasis PHP dan MariaDB dengan chatbot NLP menggunakan TF-IDF dan Cosine Similarity serta dashboard admin.
Visibility: Public atau Private
```

Karena proyek lokal sudah memiliki `README.md`, jangan centang opsi **Add a README file** ketika membuat repository.

### 2. Buka Terminal pada folder proyek

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/edukasi_pariwisata
```

### 3. Inisialisasi Git

```bash
git init
```

### 4. Periksa file yang akan dikirim

```bash
git status
```

Pastikan `config.php` dan isi folder `uploads` tidak masuk ke daftar commit karena sudah diatur pada `.gitignore`.

### 5. Tambahkan file

```bash
git add .
```

### 6. Buat commit pertama

```bash
git commit -m "Initial commit: EduToraja chatbot NLP"
```

### 7. Gunakan branch `main`

```bash
git branch -M main
```

### 8. Hubungkan repository GitHub

Ganti `USERNAME_GITHUB` dengan username GitHub Anda.

```bash
git remote add origin https://github.com/USERNAME_GITHUB/edutoraja-chatbot-nlp.git
```

Periksa remote:

```bash
git remote -v
```

### 9. Push ke GitHub

```bash
git push -u origin main
```

Untuk pembaruan berikutnya:

```bash
git add .
git commit -m "Jelaskan perubahan yang dilakukan"
git push
```

## Catatan Keamanan

Proyek ini sesuai untuk pembelajaran, demonstrasi, atau pengembangan lokal. Sebelum digunakan pada server publik, lakukan peningkatan berikut:

- Ganti penggunaan `md5()` dengan `password_hash()` dan `password_verify()`.
- Ubah password akun admin bawaan.
- Batasi atau nonaktifkan pendaftaran admin publik.
- Lindungi fitur lupa password dengan verifikasi yang aman.
- Tambahkan CSRF token pada semua form admin.
- Validasi MIME type, ekstensi, dan ukuran file unggahan.
- Gunakan prepared statement untuk seluruh query yang menerima input pengguna.
- Simpan konfigurasi database pada environment variable.
- Gunakan HTTPS.
- Lindungi atau hapus script utilitas seperti `alter_db.php` dan `update_existing_maps.php` setelah selesai digunakan.

## Status Proyek

Proyek dapat dijalankan sebagai aplikasi edukasi lokal menggunakan XAMPP. Struktur database pada `database.sql` telah disesuaikan dengan kebutuhan kode aplikasi, termasuk kolom `foto_url` pada tabel `users` dan `maps_url` pada tabel `knowledge_base`.