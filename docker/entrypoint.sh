#!/bin/sh
set -e

cd /var/www/html

echo "🔧 Initializing MegaSorpresa Backend..."

# ---------------------------------------------------------------------------
# 1. Create .env from .env.example if it doesn't exist
# ---------------------------------------------------------------------------
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Created .env from .env.example"
fi

# ---------------------------------------------------------------------------
# 2. Generate APP_KEY if not already set (via env var or .env file)
# ---------------------------------------------------------------------------
if [ -z "${APP_KEY}" ]; then
    if ! grep -q "^APP_KEY=.\+" .env 2>/dev/null; then
        php artisan key:generate --force
        echo "✅ Generated APP_KEY"
    fi
fi

# ---------------------------------------------------------------------------
# 3. Wait for the MySQL database to be ready
# ---------------------------------------------------------------------------
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
        echo "❌ ERROR: Could not connect to database after multiple retries."
        exit 1
    fi
    echo "   Database not ready, retrying in 2s... (${RETRIES} attempts left)"
    sleep 2
    RETRIES=$((RETRIES - 1))
done

echo "✅ Database connection established"

# ---------------------------------------------------------------------------
# 4. Run database migrations
# ---------------------------------------------------------------------------
php artisan migrate --force
echo "✅ Migrations completed"

# ---------------------------------------------------------------------------
# 5. Start PHP-FPM + Nginx via Supervisor
# ---------------------------------------------------------------------------
echo "🚀 Starting application on port 80..."
exec supervisord -c /etc/supervisord.conf
