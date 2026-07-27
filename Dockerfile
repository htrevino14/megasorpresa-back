# syntax=docker/dockerfile:1

# ==============================================================================
# MegaSorpresa Backend — Laravel 12 (API) para Cloud Run
#
#   Stage 1 (vendor)  -> dependencias PHP de producción con Composer (cacheable)
#   Stage 2 (runtime) -> PHP-FPM + Nginx + Supervisor, escuchando en $PORT (8080)
#
# Stateless / cloud-native: sin MySQL ni Redis embebidos. Conexiones a Cloud SQL
# y Memorystore se resuelven por variables de entorno. Migraciones/seeders NO se
# ejecutan por instancia (ver entrypoint.sh).
# ==============================================================================

# ------------------------------------------------------------------------------
# Stage 1: Dependencias PHP (Composer, sin dev)
# ------------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

# Scripts y autoloader se ejecutan en runtime, cuando ya está todo el código.
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs

# ------------------------------------------------------------------------------
# Stage 2: Runtime (PHP-FPM + Nginx + Supervisor)
# ------------------------------------------------------------------------------
FROM php:8.2-fpm-alpine AS runtime

# 1. Dependencias de sistema en runtime.
#    - gettext: provee `envsubst` para renderizar el puerto de Nginx desde $PORT.
#    - libs *sin* -dev: solo las bibliotecas compartidas que requieren gd/zip/mbstring.
RUN apk add --no-cache \
    nginx \
    supervisor \
    gettext \
    bash \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    oniguruma

# 2. Extensiones de PHP + PECL redis. Las herramientas de compilación se instalan
#    como paquete virtual temporal y se eliminan para mantener la imagen ligera.
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# 3. Binario de Composer (para el dump-autoload optimizado).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 4. Vendor desde el Stage 1 (aprovecha caché mientras no cambien los lock files).
COPY composer.json composer.lock ./
COPY --from=vendor /app/vendor ./vendor

# 5. Código de la aplicación.
COPY . .

# 6. Autoloader optimizado de producción (+ package discovery vía scripts).
RUN composer dump-autoload --optimize --no-dev

# 7. Directorios escribibles y permisos.
RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
        /var/log/supervisor \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 8. Configuración del contenedor.
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Cloud Run inyecta $PORT (8080 por defecto); lo dejamos explícito.
ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
