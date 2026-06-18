# 🛒 Sistem E-Commerce OpenCart

Project ini adalah sistem e-commerce berbasis **OpenCart 4** yang sudah dikonfigurasi agar dapat langsung dijalankan di lingkungan lokal tanpa perlu install ulang.

---

# 📌 Persyaratan Sistem

### Untuk Linux:

- Apache (LAMP)
- PHP 8.0.2 atau lebih baru, dengan extension **GD** aktif
- MySQL / MariaDB
- Composer

### Untuk Windows:

- XAMPP / Laragon / WAMP
- Composer (untuk generate folder `vendor`, lihat langkah 5)

---

# 🚀 CARA MENJALANKAN (LINUX 🐧)

## 1. Clone Repository

```bash
git clone <URL_REPOSITORY>
```

---

## 2. Pindahkan ke Web Server

```bash
sudo mv ecommerce-opencart /var/www/html/
```

---

## 3. Import Database

Masuk MySQL:

```bash
mysql -u root -p
```

Buat database:

```sql
CREATE DATABASE ECommerce;
```

Import:

```bash
mysql -u root -p ECommerce < database.sql
```

---

## 4. Konfigurasi Database & Path

Edit file:

```bash
nano /var/www/html/ecommerce-opencart/upload/config.php
nano /var/www/html/ecommerce-opencart/upload/admin/config.php
```

Sesuaikan kredensial database:

```php
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'ECommerce');
```

Pastikan `DIR_OPENCART` mengarah ke lokasi project di server kamu:

```php
define('DIR_OPENCART', '/var/www/html/ecommerce-opencart/upload/');
```

> ⚠️ **Soal `DIR_STORAGE`:** folder dependency Composer (`vendor/`, isinya Twig, AWS SDK, dll) **sudah ter-generate secara default di dalam** `upload/system/storage/vendor/` (lihat `composer.json` → `vendor-dir`). Supaya OpenCart bisa menemukan folder vendor ini, **`DIR_STORAGE` wajib mengarah ke lokasi yang sama**, contoh:
> ```php
> define('DIR_STORAGE', '/var/www/html/ecommerce-opencart/upload/system/storage/');
> ```
> Kalau kamu pindahkan `DIR_STORAGE` ke folder lain (di luar web root, demi keamanan — opsional), folder `vendor` **harus ikut dipindahkan juga** ke lokasi yang baru, atau Twig/AWS SDK/dll tidak akan ketemu (`Class "Twig\Loader\FilesystemLoader" not found`).

---

## 5. Pastikan Folder `vendor` Sudah Ada

Cek folder `upload/system/storage/vendor/` — kalau sudah ada isinya (folder `twig`, `aws`, `symfony`, dll), berarti dependency sudah siap. Kalau belum ada atau project di-clone tanpa folder ini, generate dengan Composer:

```bash
cd /var/www/html/ecommerce-opencart/upload
composer install --no-dev
```

---

## 6. Set Permission

```bash
sudo chown -R www-data:www-data /var/www/html/ecommerce-opencart
sudo chmod -R 775 /var/www/html/ecommerce-opencart
```

---

## 7. Jalankan di Browser

```
http://localhost/ecommerce-opencart/upload/
```

---

# 🚀 CARA MENJALANKAN (WINDOWS 🪟)

## 1. Clone Repository

```bash
git clone <URL_REPOSITORY>
```

---

## 2. Pindahkan ke htdocs (XAMPP)

Copy folder ke:

```
C:\xampp\htdocs\
```

Hasil:

```
C:\xampp\htdocs\ecommerce-opencart
```

---

## 3. Jalankan XAMPP

- Start Apache
- Start MySQL

---

## 4. Import Database

1. Buka phpMyAdmin:

```
http://localhost/phpmyadmin
```

2. Buat database:

```
ECommerce
```

3. Import file:

```
database.sql
```

---

## 5. Konfigurasi Database & Path

Edit file:

```
upload/config.php
upload/admin/config.php
```

Sesuaikan kredensial database:

```php
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'ECommerce');
```

> ⚠️ **Penting untuk Windows:** repository ini awalnya dikonfigurasi untuk path Linux. Pastikan `DIR_OPENCART` di **kedua** file config (`upload/config.php` dan `upload/admin/config.php`) sudah diubah ke path Windows:

```php
define('DIR_OPENCART', 'C:/xampp/htdocs/ecommerce-opencart/upload/');
```

> ⚠️ **Soal `DIR_STORAGE` — JANGAN dipindahkan ke folder eksternal** kecuali kamu juga memindahkan folder `vendor` (hasil Composer) ke lokasi yang sama. Secara default, folder `vendor` ada di dalam `upload/system/storage/vendor/`, jadi `DIR_STORAGE` harus mengarah ke situ:

```php
define('DIR_STORAGE', 'C:/xampp/htdocs/ecommerce-opencart/upload/system/storage/');
```

Gunakan forward slash (`/`), bukan backslash (`\`), agar tidak konflik dengan karakter escape PHP.

---

## 6. Install Dependency Composer (folder `vendor`)

OpenCart 4 butuh beberapa library (Twig, AWS SDK, dll) yang di-manage lewat Composer. Kalau folder `upload/system/storage/vendor/` belum ada/kosong:

1. Pastikan **Composer** sudah terinstall (`composer -v` di terminal). Kalau belum, install dari https://getcomposer.org/Composer-Setup.exe, arahkan ke PHP yang dipakai (`C:\xampp\php\php.exe`), lalu **restart total** terminal/VSCode setelah instalasi (PATH baru tidak terbaca di session yang sudah terbuka).
2. Jalankan:
```cmd
cd C:\xampp\htdocs\ecommerce-opencart\upload
composer install --no-dev
```
3. Pastikan setelah selesai, folder `upload\system\storage\vendor\` sudah terisi (ada `twig`, `aws`, `symfony`, dll beserta `autoload.php`).

---

## 7. Aktifkan Extension PHP GD

OpenCart butuh extension **GD** untuk pengolahan gambar produk. Edit:
```
C:\xampp\php\php.ini
```
Cari baris:
```ini
;extension=gd
```
Hapus titik koma di depannya jadi:
```ini
extension=gd
```
Save, lalu restart Apache lewat XAMPP Control Panel.

---

## 8. Jalankan Project

```
http://localhost/ecommerce-opencart/upload/
```

---

# 🔐 Login Admin

Nama folder admin **bisa berbeda-beda** tergantung konfigurasi saat install (demi keamanan, sering di-rename dari `admin` ke nama custom). Cek dulu nama folder sebenarnya:

```cmd
dir C:\xampp\htdocs\ecommerce-opencart\upload
```

Pada project ini, nama foldernya adalah **`useradmin`**, jadi URL login admin-nya:

```
http://localhost/ecommerce-opencart/upload/useradmin
```

Kalau lupa username/password admin, reset manual lewat phpMyAdmin di tabel `oc_user`.

---

# 🛠️ Troubleshooting

| Error | Sebab | Solusi |
|---|---|---|
| `Failed opening required '.../system/startup.php'` | `DIR_OPENCART` di `config.php` masih mengarah ke path Linux/server lama | Edit `DIR_OPENCART` di `upload/config.php` (dan `admin/config.php`) agar sesuai lokasi project saat ini |
| `fopen(.../logs/error.log): Failed to open stream` | Folder `DIR_STORAGE` (atau subfolder `logs`, `cache`, dll) tidak ditemukan, biasanya karena `DIR_STORAGE` diarahkan ke folder yang belum dibuat/salah lokasi | Pastikan `DIR_STORAGE` mengarah ke `upload/system/storage/` (lokasi default, tempat folder `vendor` juga berada), dan folder tersebut memang ada |
| `fclose(): Argument #1 ($stream) must be of type resource, bool given` | Lanjutan dari error `fopen` di atas | Sama seperti solusi di atas |
| `Class "Twig\Loader\FilesystemLoader" not found` | `DIR_STORAGE` tidak mengarah ke lokasi yang sama dengan folder `vendor` hasil Composer (`upload/system/storage/vendor/`) | Set `DIR_STORAGE` ke `.../upload/system/storage/` (jangan dipindah ke folder lain kecuali folder `vendor` juga dipindah), atau jalankan `composer install --no-dev` di folder `upload/` kalau folder `vendor` belum ada sama sekali |
| `composer` tidak dikenali / `CommandNotFoundException` | Composer belum terinstall, atau PATH belum ter-update di session terminal yang aktif | Install Composer, lalu **tutup total** dan buka ulang terminal/VSCode (PATH baru tidak terbaca otomatis di session lama) |
| `PHP GD is not installed!` | Extension `gd` belum diaktifkan di `php.ini` | Buka `php.ini` yang dipakai Apache, uncomment `extension=gd`, lalu restart Apache |
| Error permission/akses ditolak (Linux) | Folder belum dimiliki user `www-data` atau permission terlalu ketat | Jalankan `chown` dan `chmod` seperti pada langkah 6 (Linux) |
| Halaman blank / redirect ke `install/index.php` | `config.php` belum ada atau `DIR_APPLICATION` belum terdefinisi | Pastikan file `config.php` ada di folder `upload/` dan isinya sudah benar |
| 404 saat akses halaman admin | Nama folder admin sudah di-rename | Cek nama folder asli dengan `dir upload/`, lalu sesuaikan URL |

---

# ⚠️ Catatan Penting

- Folder `upload/` adalah root OpenCart
- File `database.sql` wajib diimport
- `DIR_OPENCART` **harus disesuaikan** dengan lokasi aktual project di komputer/server kamu — path bawaan di repo ini mengikuti setup Linux dan tidak otomatis cocok di Windows
- `DIR_STORAGE` **sebaiknya tetap di lokasi default** (`upload/system/storage/`) karena folder `vendor` (dependency Composer: Twig, AWS SDK, dll) ada di dalamnya. Pindahkan ke luar web root hanya jika kamu juga memindahkan folder `vendor`-nya
- Folder `vendor` di-generate lewat `composer install --no-dev`, dijalankan dari dalam folder `upload/`
- Extension PHP **GD** wajib aktif di `php.ini`
- Nama folder admin bisa berbeda dari default (`admin`) — cek langsung di folder `upload/` (pada project ini: `useradmin`)
- Jika error di Linux, biasanya karena permission
- Jika error di Windows, biasanya karena Apache/MySQL belum jalan, Composer/GD belum di-setup, atau path di `config.php` belum disesuaikan

---

# 📂 Struktur Project

```
ecommerce-opencart/
│
├── upload/
│   └── system/
│       └── storage/
│           ├── vendor/      ← dependency Composer (Twig, AWS SDK, dll)
│           ├── cache/
│           ├── download/
│           ├── logs/
│           ├── session/
│           └── upload/
├── database.sql
└── README.md
```

---

# ✅ Status Project

✔ Siap dijalankan di Linux & Windows
✔ Tidak perlu install ulang OpenCart
✔ Cocok untuk tugas / demo

---

# 👨‍💻 Author