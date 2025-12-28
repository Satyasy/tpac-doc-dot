@echo off
echo ========================================
echo    DocDot - Setup Script (Windows)
echo ========================================
echo.

:: Check if composer is installed
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer belum terinstall!
    echo Download di: https://getcomposer.org/download/
    pause
    exit /b 1
)

:: Check if npm is installed
where npm >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Node.js/NPM belum terinstall!
    echo Download di: https://nodejs.org/
    pause
    exit /b 1
)

echo [1/6] Install PHP dependencies...
call composer install

echo.
echo [2/6] Install Node dependencies...
call npm install

echo.
echo [3/6] Setup environment file...
if not exist .env (
    copy .env.example .env
    echo .env file created!
) else (
    echo .env already exists, skipping...
)

echo.
echo [4/6] Generate application key...
call php artisan key:generate

echo.
echo [5/6] Build frontend assets...
call npm run build

echo.
echo [6/6] Clear cache...
call php artisan optimize:clear

echo.
echo ========================================
echo    Setup selesai!
echo ========================================
echo.
echo Langkah selanjutnya:
echo 1. Buat database 'docdot' di MySQL
echo 2. Edit file .env (isi API keys)
echo 3. Jalankan: php artisan migrate --seed
echo 4. Jalankan: php artisan serve
echo.
echo Buka http://localhost:8000
echo.
pause
