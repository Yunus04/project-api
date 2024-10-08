# Proyek Laravel dengan JWT Auth dan Swagger

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Tentang Proyek

Proyek ini adalah aplikasi berbasis Laravel yang menggunakan autentikasi JWT dan Swagger untuk mendokumentasikan API. Proyek ini juga mencakup validasi permintaan menggunakan `FormRequest`.

## Langkah-langkah untuk Memulai

Berikut adalah langkah-langkah yang perlu diikuti untuk menjalankan proyek ini:

### 1. Prasyarat

- PHP (versi 8.0 atau lebih baru)
- Composer
- Database (MySQL, PostgreSQL, atau SQLite)
- Node.js dan npm (untuk mengelola frontend, jika diperlukan)

### 2. Mengunduh Proyek

Clone repositori ini ke dalam direktori lokal Anda:

```bash
git clone https://github.com/Yunus04/project-api
cd repo
```

### 3. Menginstal Dependensi

```bash
composer install
```

### 4. Menyiapkan File .env


```bash
cp .env.example .env
```

Kemudian, buka file .env dan sesuaikan konfigurasi database dan kunci aplikasi:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

JWT_SECRET=your_jwt_secret_key

```

### 5. Menjalankan Migrasi

```bash
php artisan migrate
```

### 6. Menghasilkan Kunci Aplikasi

```bash
php artisan key:generate
```

### 7. Menjalankan Server

```bash
php artisan serve
```

### 8. Mengakses API

Setelah server berjalan, Anda dapat mengakses API di http://localhost:8000/api. Pastikan untuk menggunakan Swagger untuk melihat dokumentasi API.


### 9. Menggunakan Swagger
Swagger telah diimplementasikan untuk mendokumentasikan API. Anda dapat mengakses antarmuka Swagger di:
```bash
http://localhost:8000/api/documentation
```

### 10. Autentikasi
Anda dapat mendaftar dan masuk menggunakan endpoint berikut:

- Register: POST /api/register
- Login: POST /api/login
- Logout: POST /api/logout
Pastikan untuk mengirim permintaan dengan format JSON yang sesuai, menggunakan validasi yang telah diterapkan.
