#!/bin/sh
set -e

cd /var/www/html

echo "🔧 Initializing MegaSorpresa Backend..."

# 1. Asegurar permisos de carpetas críticas (A veces los volúmenes de Docker los cambian)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 2. Manejo de .env y APP_KEY
if [ ! -f .env ] && [ -z "$APP_KEY" ]; then
    cp .env.example .env
    echo "✅ Created .env from .env.example"
    php artisan key:generate --force
fi

# 3. Esperar a la base de datos (Tu lógica de PDO es excelente, la mantenemos)
echo "⏳ Waiting for database connection..."
RETRIES=30
until php -r "
    \$host = getenv('DB_HOST') ?: 'mysql';
    \$port = getenv('DB_PORT') ?: '3306';
    \$db   = getenv('DB_DATABASE') ?: 'megasorpresa';
    \$user = getenv('DB_USERNAME') ?: 'megasorpresa';
    \$pass = getenv('DB_PASSWORD') ?: 'secret';
    try {
        new PDO(\"mysql:host=\$host;port=\$port;dbname=\$db\", \$user, \$pass);
        exit(0);
    } catch (PDOException \$e) {
        exit(1);
    }
" 2>/dev/null; do
    if [ "$RETRIES" -le 0 ]; then
        echo "❌ ERROR: Could not connect to database."
        exit 1
    fi
    echo "   Database not ready, retrying... (${RETRIES} left)"
    sleep 2
    RETRIES=$((RETRIES - 1))
done
echo "✅ Database connection established"

# 4. Optimización de Laravel (Añadido)
echo "🧹 Optimizing Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
# En producción, descomenta las siguientes líneas:
# php artisan config:cache
# php artisan route:cache

# 5. Ejecutar migraciones
# --force es obligatorio en producción
php artisan migrate --force
echo "✅ Migrations completed"

# 6. Iniciar Supervisor (PHP-FPM + Nginx + Workers de Redis)
echo "🚀 Starting application via Supervisor..."
exec supervisord -c /etc/supervisord.conf