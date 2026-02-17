#!/bin/bash

echo "Setting up Finax Todo..."

cp .env.example .env

sed -i 's/DB_HOST=127.0.0.1/DB_HOST=postgres/' .env
sed -i 's/DB_PASSWORD=/DB_PASSWORD=secret/' .env

docker compose up -d --build

echo "Waiting for containers..."
sleep 5

docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan l5-swagger:generate

echo ""
echo "Done! App running at http://localhost:8000"
echo "Swagger docs at http://localhost:8000/api/documentation"
echo ""
echo "Test credentials:"
echo "  Email: test@gmail.com"
echo "  Password: 12345678"
