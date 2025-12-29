#!/bin/bash

echo "========================================"
echo "   DocDot - Setup Script (Linux/Mac)"
echo "========================================"
echo ""

# Check composer
if ! command -v composer &> /dev/null; then
    echo "[ERROR] Composer belum terinstall!"
    echo "Install: https://getcomposer.org/download/"
    exit 1
fi

# Check npm
if ! command -v npm &> /dev/null; then
    echo "[ERROR] Node.js/NPM belum terinstall!"
    echo "Install: https://nodejs.org/"
    exit 1
fi

echo "[1/6] Install PHP dependencies..."
composer install

echo ""
echo "[2/6] Install Node dependencies..."
npm install

echo ""
echo "[3/6] Setup environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo ".env file created!"
else
    echo ".env already exists, skipping..."
fi

echo ""
echo "[4/6] Generate application key..."
php artisan key:generate

echo ""
echo "[5/6] Build frontend assets..."
npm run build

echo ""
echo "[6/6] Clear cache..."
php artisan optimize:clear

echo ""
echo "========================================"
echo "   Setup selesai!"
echo "========================================"
echo ""
echo "Langkah selanjutnya:"
echo "1. Buat database 'docdot' di MySQL"
echo "2. Edit file .env (isi API keys: GEMINI, PINECONE)"
echo "3. Jalankan: php artisan migrate --seed"
echo "4. Jalankan: php artisan serve"
echo ""
echo "=== Redis (Opsional tapi Recommended) ==="
echo "Untuk performa lebih baik, install Redis:"
echo "  Ubuntu: sudo apt install redis-server"
echo "  Mac: brew install redis"
echo "  Docker: docker run -d -p 6379:6379 redis:7-alpine"
echo ""
echo "Jika tidak pakai Redis, edit .env:"
echo "  SESSION_DRIVER=database"
echo "  CACHE_STORE=database"
echo ""
echo "Buka http://localhost:8000"
