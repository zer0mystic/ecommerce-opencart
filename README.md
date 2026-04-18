# 🛒 Sistem E-Commerce OpenCart

Project ini adalah sistem e-commerce berbasis **OpenCart** yang sudah dikonfigurasi agar dapat langsung dijalankan di lingkungan lokal tanpa perlu install ulang.

---

# 📌 Persyaratan Sistem

### Untuk Linux:

- Apache (LAMP)
- PHP 7.x / 8.x
- MySQL / MariaDB

### Untuk Windows:

- XAMPP / Laragon / WAMP

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

## 4. Konfigurasi Database

Edit file:

```bash
nano /var/www/html/ecommerce-opencart/upload/config.php
nano /var/www/html/ecommerce-opencart/upload/admin/config.php
```

Sesuaikan:

```php
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'ECommerce');
```

---

## 5. Set Permission

```bash
sudo chown -R www-data:www-data /var/www/html/ecommerce-opencart
sudo chmod -R 775 /var/www/html/ecommerce-opencart
```

---

## 6. Jalankan di Browser

```
http://localhost/ecommerce-opencart/
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

## 5. Konfigurasi Database

Edit file:

```
upload/config.php
upload/admin/config.php
```

Sesuaikan:

```php
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'ECommerce');
```

---

## 6. Jalankan Project

```
http://localhost/ecommerce-opencart/
```

---

# 🔐 Login Admin

```
http://localhost/ecommerce-opencart/upload/admin
```

Atau:

```
http://localhost/ecommerce-opencart/upload/useradmin
```

---

# ⚠️ Catatan Penting

- Folder `upload/` adalah root OpenCart
- File `database.sql` wajib diimport
- Jika error di Linux, biasanya karena permission
- Jika error di Windows, biasanya karena Apache/MySQL belum jalan

---

# 📂 Struktur Project

```
ecommerce-opencart/
│
├── upload/
├── storage/
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
