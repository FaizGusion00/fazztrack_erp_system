#!/bin/bash

echo "🚀 Starting Fazztrack T-Shirt Printing App Setup..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

echo "📦 Starting Docker containers..."
docker-compose up -d

echo "⏳ Waiting for containers to be ready..."
sleep 10

echo "🔧 Installing Laravel dependencies..."
docker-compose exec app composer install

echo "📝 Setting up environment..."
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate

echo "🗄️ Running database migrations..."
docker-compose exec app php artisan migrate

echo "📁 Creating storage link..."
docker-compose exec app php artisan storage:link

echo "🌱 Seeding database with sample data..."
docker-compose exec app php artisan db:seed

echo "✅ Setup complete!"
echo ""
echo "🌐 Access the application at: http://localhost:8000"
echo ""
echo "👤 Default login credentials:"
echo "   SuperAdmin: admin/admin123"
echo "   Sales Manager: sales/sales123"
echo "   Designer: designer/designer123"
echo "   Print Staff: print/print123"
echo ""
echo "📊 Database: localhost:3306 (fazztrack/secret)"
echo "🔴 Redis: localhost:6379"
echo ""
echo "📚 For more information, see README.md" 