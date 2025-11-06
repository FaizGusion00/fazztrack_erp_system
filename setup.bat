@echo off
echo 🚀 Setting up Fazztrack ERP System...

REM Check if Docker is running
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker is not running. Please start Docker first.
    pause
    exit /b 1
)

REM Stop any existing containers
echo 🛑 Stopping existing containers...
docker-compose down

REM Remove any existing volumes to start fresh
echo 🗑️  Cleaning up existing volumes...
docker volume prune -f

REM Build and start containers
echo 🔨 Building and starting containers...
docker-compose up -d --build

REM Wait for database to be ready
echo ⏳ Waiting for database to be ready...
timeout /t 30 /nobreak >nul

REM Generate application key
echo 🔑 Generating application key...
docker-compose exec app php artisan key:generate

REM Run migrations
echo 📊 Running database migrations...
docker-compose exec app php artisan migrate --force

REM Set proper permissions
echo 🔐 Setting proper permissions...
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

REM Clear caches
echo 🧹 Clearing caches...
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

echo ✅ Setup complete!
echo.
echo 🌐 Application URLs:
echo    Main App: http://localhost:8000
echo    phpMyAdmin: http://localhost:8080
echo.
echo 👤 Demo Credentials:
echo    SuperAdmin: superadmin / admin123
echo    Admin: admin / approver123
echo    Sales Manager: sales / sales123
echo    Designer: designer / designer123
echo    Production Staff: print / print123, press / press123, cut / cut123, sew / sew123, qc / qc123, packing / packing123
echo.
echo 📊 Database Info:
echo    Host: localhost
echo    Port: 3307
echo    Database: fazztrack
echo    Username: fazztrack
echo    Password: secret
echo.
echo 🎉 Your Fazztrack ERP system is now running!
pause
