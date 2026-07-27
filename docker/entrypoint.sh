#!/bin/sh
set -e

cd /var/www/html

echo "🔧 Starting MegaSorpresa Backend (Cloud Run)..."

# 1. Renderizar la config de Nginx al puerto que asigna Cloud Run ($PORT, 8080
#    por defecto). Solo se sustituye ${PORT}; las variables propias de Nginx
#    ($uri, $realpath_root, ...) se preservan.
: "${PORT:=8080}"
export PORT
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# 2. Garantizar directorios escribibles (Cloud Run gen2 monta un FS escribible
#    en memoria; sesiones/cache/colas van a MySQL, no al disco).
mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 3. APP_KEY DEBE llegar por entorno (Secret Manager). No se genera al vuelo:
#    una clave nueva en cada cold start invalidaría sesiones y datos cifrados.
if [ -z "$APP_KEY" ]; then
    echo "⚠️  WARNING: APP_KEY no está definida. Provéela vía Secret Manager." >&2
fi

# 4. Optimización de producción. En Cloud Run las variables de entorno solo
#    existen en runtime (no en build), así que las cachés se construyen aquí.
echo "⚙️  Cacheando config / rutas / vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Las migraciones deben ejecutarse como un Job de Cloud Run puntual, NO en
#    cada arranque de instancia (evita carreras y cold starts lentos).
#    Opt-in explícito solo para casos controlados:
#        gcloud run jobs create migrate-back ... --command php --args artisan,migrate,--force
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "🗄️  Ejecutando migraciones (RUN_MIGRATIONS=true)..."
    php artisan migrate --force
fi

# 6. Arrancar PHP-FPM + Nginx bajo Supervisor.
echo "🚀 Sirviendo en el puerto ${PORT}..."
exec supervisord -c /etc/supervisord.conf
