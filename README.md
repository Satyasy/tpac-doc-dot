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

## Quick Start

### Cara 1: Manual (Laragon/XAMPP)

```bash
# 1. Clone repo
git clone https://github.com/Satyasy/tpac-doc-dot.git
cd tpac-doc-dot

# 2. Install dependencies
composer install
npm install

# 3. Copy .env file
cp .env.example .env

# 6. Migrate & seed database
php artisan migrate --seed

# 7. Build frontend
npm run build

# 8. Jalankan server
php artisan serve
```


---

### Cara 2: Pake Docker

```bash
# 1. Clone repo
git clone https://github.com/Satyasy/tpac-doc-dot.git
cd tpac-doc-dot

# 2. Copy env
cp .env.example .env

# 4. Jalankan docker
docker-compose up -d

# 5. Setup database
docker-compose exec app php artisan migrate --seed
```

---

## Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@docdot.com | password |
| Dokter | doctor@docdot.com | password |
| User | user@docdot.com | password |

Admin Panel: http://localhost:8000/admin

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

```

---

## Docker Services

| Service | Port | Deskripsi |
|---------|------|-----------|
| app | 8000 | Laravel + PHP |
| mysql | 3307 | Database MySQL 8 |
| redis | 6379 | Cache & Sessions |

---

## What For?

- Satyasy - Traspac Competition 2025

---

