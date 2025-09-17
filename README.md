# 📚 Laravel Book Management API

Take Home Test - Laravel Programmer (Adhivasindo)

---

## 🚀 Fitur

1. **Manajemen Buku**

    - Field: `title`, `author`, `published_year`, `isbn`, `stock`
    - List buku dengan pagination dan search & filter (`title`, `author`, `published_year`)
    - Tambah, ubah, hapus buku
    - Validasi:
        - ISBN harus unik
        - Tahun terbit 4 digit
        - Stok minimal 0

2. **Peminjaman & Pengembalian Buku**

    - User bisa meminjam buku jika stok tersedia
    - Stok otomatis berkurang saat dipinjam, bertambah saat dikembalikan
    - Validasi otomatis pada request
    - Pengembalian buku:
        - `POST /api/loans/return` → kembalikan buku
        - Status peminjaman diupdate menjadi `returned`

3. **Riwayat Peminjaman**

    - `GET /api/loans/{user_id}` → daftar buku yang sedang dipinjam user
    - Menampilkan status peminjaman (`borrowed`, `returned`) dan tanggal

4. **Notifikasi Email**

    - Dikirim via **Queue Job** saat peminjaman berhasil
    - Mailer default: `log` (cek di `storage/logs/laravel.log`)

---

## 🛠️ Instalasi & Setup

1. Clone repo & masuk folder project:

```bash
git clone https://github.com/astroceilo/book-management-api.git
cd laravel-book-management
```

2. Install dependency:

```bash
composer install
npm install && npm run dev
```

3. Copy file environment & generate app key:

```bash
cp .env.example .env
php artisan key:generate
```

4. Setup database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookapi
DB_USERNAME=root
DB_PASSWORD=
```

5. Migrasi & seeder (otomatis bikin 10 user + 30 buku):

```bash
php artisan migrate --seed
```

6. Jalankan server & queue worker (dua terminal terpisah):

```bash
php artisan serve
php artisan queue:work
```

---

## 🔑 Autentikasi (Sanctum)

-   **Register:** `POST /api/register`
-   **Login:** `POST /api/login`
-   **Logout:** `POST /api/logout` (Bearer token)

**Contoh Register:**

```http
POST /api/register
Content-Type: application/json

{
  "name": "Odo Dev",
  "email": "odo@example.com",
  "password": "password"
}
```

**Contoh Login:**

```http
POST /api/login
Content-Type: application/json

{
  "email": "odo@example.com",
  "password": "password"
}
```

**Contoh Logout:**

```http
POST /api/logout
Authorization: Bearer <your_token_here>
```

---

## 📌 Endpoint Utama

### Buku

| Method | Endpoint        | Keterangan                      |
| ------ | --------------- | ------------------------------- |
| GET    | /api/books      | List buku (pagination + filter) |
| POST   | /api/books      | Tambah buku                     |
| GET    | /api/books/{id} | Detail buku                     |
| PUT    | /api/books/{id} | Update buku                     |
| DELETE | /api/books/{id} | Hapus buku                      |

**Filter contoh:**

```
GET /api/books?author=J.K.Rowling
GET /api/books?published_year=2020
GET /api/books?title=Laravel
```

**Contoh tambah buku:**

```
POST /api/books
Authorization: Bearer <token>
Content-Type: application/json

{
  "title": "Belajar Laravel",
  "author": "Odo",
  "published_year": "2025",
  "isbn": "1234567890125",
  "stock": 5
}
```

**Contoh update buku:**

```
POST /api/books/31
Authorization: Bearer <token>
Content-Type: application/json

{
  "stock": 10
}
```

### Peminjaman & Pengembalian

| Method | Endpoint          | Keterangan                                           |
| ------ | ----------------- | ---------------------------------------------------- |
| GET    | /api/loans/       | Daftar buku yang dipinjam (otomatis dari user login) |
| POST   | /api/loans/borrow | Pinjam buku (stok otomatis berkurang)                |
| POST   | /api/loans/return | Kembalikan buku (stok bertambah)                     |

**Contoh pinjam buku:**

```http
POST /api/loans/borrow
Authorization: Bearer <token>
Content-Type: application/json

{
  "book_id": 5
}
```

**Contoh kembalikan buku:**

```http
POST /api/loans/return
Authorization: Bearer <token>
Content-Type: application/json

{
    "book_id": 5
}
```

---

## 🧪 Testing

Jalankan semua test:

```bash
php artisan test
```

---

## 📧 Email

-   Driver: `log`
-   Cek di: `storage/logs/laravel.log`
-   Otomatis dikirim saat peminjaman buku berhasil

---
