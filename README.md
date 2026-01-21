# 🎁 MegaSorpresa Backend API

Backend API para el e-commerce de juguetes **MegaSorpresa**. Este proyecto sirve como API central para tres clientes: Web (SPA), Android e iOS.

<p align="center">
<a href="https://github.com/htrevino14/megasorpresa-back/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 📋 Tabla de Contenidos

- [Stack Tecnológico](#stack-tecnológico)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Uso con Docker/Sail](#uso-con-dockersail)
- [Autenticación](#autenticación)
- [Documentación API](#documentación-api)
- [Testing](#testing)
- [Arquitectura](#arquitectura)

## 🛠️ Stack Tecnológico

- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Base de Datos**: MySQL 8.4
- **Cache**: Redis
- **Autenticación**: Laravel Sanctum
- **Testing**: Pest PHP
- **Documentación API**: L5-Swagger (OpenAPI)
- **Desarrollo**: Laravel Sail (Docker)

## 📦 Requisitos

### Desarrollo Local (sin Docker)

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Redis
- Node.js >= 18.x

### Desarrollo con Docker (Recomendado)

- Docker Desktop
- Docker Compose

## 🚀 Instalación

### Opción 1: Con Laravel Sail (Docker) - Recomendado

1. **Clonar el repositorio**

```bash
git clone https://github.com/htrevino14/megasorpresa-back.git
cd megasorpresa-back
```

2. **Instalar dependencias de PHP** (en la primera vez, sin Sail)

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

3. **Configurar el entorno**

```bash
cp .env.example .env
```

4. **Editar el archivo `.env`** para usar Docker services:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=megasorpresa
DB_USERNAME=sail
DB_PASSWORD=password

CACHE_STORE=redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

5. **Iniciar los servicios con Sail**

```bash
./vendor/bin/sail up -d
```

6. **Generar la clave de aplicación**

```bash
./vendor/bin/sail artisan key:generate
```

7. **Ejecutar migraciones**

```bash
./vendor/bin/sail artisan migrate
```

8. **Instalar dependencias de Node.js**

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### Opción 2: Instalación Local (sin Docker)

1. **Clonar el repositorio**

```bash
git clone https://github.com/htrevino14/megasorpresa-back.git
cd megasorpresa-back
```

2. **Instalar dependencias**

```bash
composer install
npm install
```

3. **Configurar el entorno**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar la base de datos** en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=megasorpresa
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

5. **Crear la base de datos**

```bash
mysql -u root -p
CREATE DATABASE megasorpresa;
```

6. **Ejecutar migraciones**

```bash
php artisan migrate
```

7. **Compilar assets**

```bash
npm run build
```

## ⚙️ Configuración

### Redis

**Importante**: Este proyecto requiere Redis para funcionar correctamente. Redis se utiliza para:

- Cache de catálogo de productos
- Gestión de sesiones
- Colas de trabajos
- Rate limiting

#### Con Docker/Sail

Redis ya está configurado y se inicia automáticamente con:

```bash
./vendor/bin/sail up -d
```

#### Sin Docker

Asegúrate de tener Redis instalado y ejecutándose:

**macOS (con Homebrew)**:
```bash
brew install redis
brew services start redis
```

**Ubuntu/Debian**:
```bash
sudo apt-get install redis-server
sudo systemctl start redis
```

**Windows**:
- Descargar desde [Redis for Windows](https://github.com/microsoftarchive/redis/releases)

### Laravel Sanctum

Sanctum ya está instalado y configurado. Para publicar la configuración (opcional):

```bash
./vendor/bin/sail artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

## 🐳 Uso con Docker/Sail

### Comandos Útiles

```bash
# Iniciar servicios
./vendor/bin/sail up -d

# Detener servicios
./vendor/bin/sail down

# Ver logs
./vendor/bin/sail logs

# Ejecutar comandos Artisan
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan tinker

# Ejecutar tests
./vendor/bin/sail test
./vendor/bin/sail pest

# Acceder al contenedor
./vendor/bin/sail shell

# Comandos de Composer
./vendor/bin/sail composer install
./vendor/bin/sail composer update

# Comandos de NPM
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
./vendor/bin/sail npm run build
```

### Servicios Disponibles

Cuando ejecutas `sail up`, los siguientes servicios están disponibles:

- **Aplicación Laravel**: http://localhost
- **MySQL**: localhost:3306
- **Redis**: localhost:6379
- **Mailpit** (email testing): http://localhost:8025
- **Meilisearch** (búsqueda): http://localhost:7700

### Alias de Sail (Opcional)

Para no escribir `./vendor/bin/sail` cada vez:

```bash
# En tu ~/.bashrc o ~/.zshrc
alias sail='./vendor/bin/sail'

# Luego puedes usar:
sail up -d
sail artisan migrate
sail test
```

## 🔐 Autenticación

Este proyecto utiliza **Laravel Sanctum** para autenticación con dos estrategias:

### Para Clientes Móviles (Android/iOS)

Los clientes móviles usan **Bearer Tokens**:

**Login**:
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

**Respuesta**:
```json
{
  "token": "1|abc123xyz...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  }
}
```

**Uso del token**:
```http
GET /api/products
Authorization: Bearer 1|abc123xyz...
```

### Para Cliente Web (SPA)

El cliente web usa **Session-based authentication** con cookies.

Ver documentación completa en [ARCHITECTURAL_GUIDELINES.md](./ARCHITECTURAL_GUIDELINES.md#autenticación)

## 📚 Documentación API

La documentación de la API está generada automáticamente con **Swagger/OpenAPI**.

### Acceder a la Documentación

1. **Generar la documentación**:

```bash
./vendor/bin/sail artisan l5-swagger:generate
```

2. **Acceder a Swagger UI**:

```
http://localhost/api/documentation
```

### Actualizar Documentación

Cada vez que agregues o modifiques endpoints, actualiza la documentación:

```bash
./vendor/bin/sail artisan l5-swagger:generate
```

## 🧪 Testing

Este proyecto utiliza **Pest PHP** para testing.

### Ejecutar Tests

```bash
# Todos los tests
./vendor/bin/sail pest

# Tests específicos
./vendor/bin/sail pest tests/Feature/Api/AuthTest.php

# Con cobertura
./vendor/bin/sail pest --coverage

# Tests en modo watch (re-ejecuta al cambiar archivos)
./vendor/bin/sail pest --watch

# Tests con PHPUnit (alternativa)
./vendor/bin/sail test
```

### Estructura de Tests

```
tests/
├── Feature/          # Tests de integración (API, flujos completos)
│   └── Api/
│       └── AuthTest.php
├── Unit/             # Tests unitarios (lógica aislada)
│   └── ExampleTest.php
└── Pest.php          # Configuración de Pest
```

### Ejemplo de Test

Ver ejemplo completo en [tests/Feature/Api/AuthTest.php](./tests/Feature/Api/AuthTest.php)

## 🏛️ Arquitectura

Este proyecto sigue el patrón **"Laravel Way Pro"** con clara separación de responsabilidades:

```
app/
├── DTOs/                    # Data Transfer Objects
├── Services/                # Lógica de negocio
├── Repositories/            # Abstracción de datos
├── Traits/                  # Comportamiento reutilizable
├── Http/
│   ├── Controllers/Api/     # Controladores API
│   ├── Requests/            # Form Requests
│   └── Resources/           # API Resources
└── Models/                  # Eloquent Models
```

**Documentación completa**: Ver [ARCHITECTURAL_GUIDELINES.md](./ARCHITECTURAL_GUIDELINES.md)

### Flujo de una Petición

```
Cliente → Route → Controller → DTO → Service → Repository → Model → DB
                                 ↓
                              Events & Listeners (async)
```

## 🔧 Scripts Útiles

```bash
# Setup inicial completo
composer run setup

# Modo desarrollo (servidor + queue + logs + vite)
composer run dev

# Ejecutar tests
composer run test

# Linter de código
./vendor/bin/sail pint
```

## 📖 Documentación Adicional

- [Guía de Arquitectura](./ARCHITECTURAL_GUIDELINES.md) - Patrones y convenciones del proyecto
- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Sanctum](https://laravel.com/docs/sanctum)
- [Documentación de Pest](https://pestphp.com)
- [Documentación de Sail](https://laravel.com/docs/sail)

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### Convenciones de Código

- Seguir [PSR-12](https://www.php-fig.org/psr/psr-12/) para código PHP
- Usar Laravel Pint para formatear: `./vendor/bin/sail pint`
- Escribir tests para nuevas funcionalidades
- Documentar endpoints con anotaciones OpenAPI

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

## 🙏 Agradecimientos

- [Laravel](https://laravel.com)
- [Pest PHP](https://pestphp.com)
- [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)

---

**Desarrollado con ❤️ para MegaSorpresa**
