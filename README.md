# DocDot - AI Health Consultation Platform

Platform konsultasi kesehatan berbasis AI dengan fitur RAG (Retrieval-Augmented Generation), monitoring dokter-pasien, dan Mood Tracker.

![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/vuejs-%2335495e.svg?style=for-the-badge&logo=vuedotjs&logoColor=%234FC08D)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white)
![Google Gemini](https://img.shields.io/badge/google%20gemini-8E75B2?style=for-the-badge&logo=google%20gemini&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)
![Redis](https://img.shields.io/badge/redis-%23DD0031.svg?style=for-the-badge&logo=redis&logoColor=white)
![Filament](https://img.shields.io/badge/filament-%23FDAE4B.svg?style=for-the-badge&logo=filament&logoColor=black&logoSize=auto)

---

## Fitur Utama

- **AI Chatbot** - Konsultasi kesehatan dengan AI berbasis Gemini + RAG dengan Pinecone Vector DB
- **Katalog Obat** - Database obat lengkap dengan info harga & interaksi
- **Health Dashboard** - Tracking kesehatan fisik & mental
- **Doctor Monitoring** - Dokter bisa memantau pasien yang terhubung
- **Alert System** - Notifikasi otomatis untuk kata-kata berbahaya
- **Artikel Kesehatan** - Konten edukasi kesehatan

---

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL 8.0 / MariaDB 10.6
- Gemini API Key
- Pinecone API Key (untuk RAG)

---

## Quick Start

### Cara 1: Manual (Laragon/XAMPP)

```bash
# 1. Clone repo
git clone https://github.com/Satyasy/tpac-doc-dot.git
cd tpac-doc-dot

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Buat database 'docdot' di phpMyAdmin/MySQL

# 5. Edit .env - sesuaikan database & API keys
DB_DATABASE=docdot
DB_USERNAME=root
DB_PASSWORD=
GEMINI_API_KEY=your_key
PINECONE_API_KEY=your_key
PINECONE_HOST=your_host

# 6. Migrate & seed database
php artisan migrate --seed

# 7. Build frontend
npm run build

# 8. Jalankan server
php artisan serve
```

Buka http://localhost:8000

---

### Cara 2: Pake Docker

```bash
# 1. Clone repo
git clone https://github.com/Satyasy/tpac-doc-dot.git
cd tpac-doc-dot

# 2. Copy env
cp .env.example .env

# 3. Edit .env - isi API keys

# 4. Jalankan docker
docker-compose up -d

# 5. Setup database
docker-compose exec app php artisan migrate --seed
```

Buka http://localhost:8000

---

## Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@docdot.com | password |
| Dokter | doctor@docdot.com | password |
| User | user@docdot.com | password |

Admin Panel: http://localhost:8000/admin

---

## Tech Stack

**Backend:**
- Laravel 12
- Filament 4 (Admin Panel)
- Spatie Permission (Role Management)
- Gemini AI (LLM)
- Pinecone (Vector Database)
- Redis (Cache & Sessions)

**Frontend:**
- Vue 3 + TypeScript
- Inertia.js
- Tailwind CSS 4
- Iconify

---

## 🔴 Redis Cache

Aplikasi ini menggunakan **Redis** untuk caching dan sessions agar performa lebih cepat.

### Fitur yang di-cache:
| Data | TTL | Keterangan |
|------|-----|------------|
| Kategori Obat | 10 menit | Jarang berubah |
| Related Drugs | 5 menit | Per kategori |
| Featured Article | 5 menit | Halaman utama |
| Popular Articles | 5 menit | Halaman utama |
| Kategori Artikel | 10 menit | Jarang berubah |
| Daftar Dokter | 1 menit | Role-based |

### Cache Commands:
```bash
# Lihat statistik cache
php artisan cache:stats

# Clear semua cache
php artisan cache:stats --clear

# Laravel cache commands
php artisan cache:clear
php artisan config:clear
php artisan optimize:clear
```

### Jika tidak pakai Redis:
Edit `.env` - ubah ke database:
```env
SESSION_DRIVER=database
CACHE_STORE=database
```

---

## Environment Variables

| Variable | Deskripsi |
|----------|-----------|
| `DB_DATABASE` | Nama database |
| `GEMINI_API_KEY` | API key Gemini AI |
| `PINECONE_API_KEY` | API key Pinecone | 
| `PINECONE_HOST` | Host Pinecone index | 
| `REDIS_HOST` | Redis server (default: 127.0.0.1) |
| `CACHE_STORE` | Cache driver (redis/database) |
| `SESSION_DRIVER` | Session driver (redis/database) |
| `MAIL_*` | Config email untuk OTP | 

---

## Commands

```bash
# Development
composer run dev

# Production
npm run build 

# Database
php artisan migrate
php artisan migrate:fresh --seed

# Cache
php artisan cache:stats          # Lihat statistik
php artisan cache:stats --clear  # Clear all cache
php artisan optimize:clear       # Clear all Laravel cache
```

---

## Docker Services

| Service | Port | Deskripsi |
|---------|------|-----------|
| app | 8000 | Laravel + PHP |
| mysql | 3307 | Database MySQL 8 |
| redis | 6379 | Cache & Sessions |
| phpmyadmin | 8080 | Database GUI |
| redisinsight | 5540 | Redis GUI |

---

## What For?

- Satyasy - Traspac Competition 2025

---

