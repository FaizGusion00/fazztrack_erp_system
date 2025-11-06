#!/bin/bash

echo "🚀 Setting up Fazztrack ERP System..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Stop any existing containers
echo "🛑 Stopping existing containers..."
docker-compose down

# Remove any existing volumes to start fresh
echo "🗑️  Cleaning up existing volumes..."
docker volume prune -f

# Build and start containers
echo "🔨 Building and starting containers..."
docker-compose up -d --build

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 30

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Run migrations
echo "📊 Running database migrations..."
docker-compose exec app php artisan migrate --force

# Seed database with sample data
echo "🌱 Seeding database with sample data..."
docker-compose exec app php artisan db:seed --force

# Set proper permissions
echo "🔐 Setting proper permissions..."
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

# Clear caches
echo "🧹 Clearing caches..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

echo "✅ Setup complete!"
echo ""
echo "🌐 Application URLs:"
echo "   Main App: http://localhost:8000"
echo "   phpMyAdmin: http://localhost:8080"
echo ""
echo "👤 Demo Credentials:"
echo "   SuperAdmin: superadmin / admin123"
echo "   Admin: admin / approver123"
echo "   Sales Manager: sales / sales123"
echo "   Designer: designer / designer123"
echo "   Production Staff: print / print123, press / press123, cut / cut123, sew / sew123, qc / qc123, packing / packing123"
echo ""
echo "📊 Database Info:"
echo "   Host: localhost"
echo "   Port: 3307"
echo "   Database: fazztrack"
echo "   Username: fazztrack"
echo "   Password: secret"
echo ""
echo "🎉 Your Fazztrack ERP system is now running!"