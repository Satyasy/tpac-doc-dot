# DocDot - AI Health Consultation Platform

Platform konsultasi kesehatan berbasis AI dengan fitur RAG (Retrieval-Augmented Generation), monitoring dokter-pasien, dan Mood Tracker.

![Laravel](https://img.shields.io/badge/Laravel-12-red?logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3-green?logo=vue.js)
![Tailwind](https://img.shields.io/badge/Tailwind-4-blue?logo=tailwindcss)

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
# DB_DATABASE=docdot
# DB_USERNAME=root
# DB_PASSWORD=
# GEMINI_API_KEY=your_key
# PINECONE_API_KEY=your_key
# PINECONE_HOST=your_host

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

**Frontend:**
- Vue 3 + TypeScript
- Inertia.js
- Tailwind CSS 4
- Iconify

---

## Struktur Penting

```
app/
├── Filament/          # Admin panel resources
├── Http/Controllers/  # API & web controllers
├── Models/            # Eloquent models
├── Services/Rag/      # RAG service (AI)
resources/js/
├── components/        # Vue components
├── Pages/             # Vue pages
```

---

## Environment Variables

| Variable | Deskripsi | Required |
|----------|-----------|----------|
| `DB_DATABASE` | Nama database | ✅ |
| `GEMINI_API_KEY` | API key Gemini AI | ✅ |
| `PINECONE_API_KEY` | API key Pinecone | ✅ |
| `PINECONE_HOST` | Host Pinecone index | ✅ |
| `MAIL_*` | Config email untuk OTP | ⚠️ |

---

## Commands

```bash
# Development
composer run dev

# Production
npm run build         # Build frontend

# Database
php artisan migrate              # Run migrations
php artisan migrate:fresh --seed # Reset & seed

# Cache
php artisan optimize:clear       # Clear all cache
```

---

## What For?

- Satyasy - Traspac Competition 2025

---

## License

MIT License
