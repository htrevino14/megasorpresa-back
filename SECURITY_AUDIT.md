# Informe de Auditoría — Seguridad, OpenAPI y Arquitectura

> Auditoría integral del repositorio `megasorpresa-back` (Laravel 12 / PHP 8.2+).
> Realizada de forma estática: el entorno de ejecución no dispone de acceso de
> red para `composer install` / `composer update`, por lo que las pruebas se
> validan en CI. El comando `composer audit --locked` sí funciona contra la base
> de advisories de Packagist.

---

## 1. Auditoría de Seguridad y Vulnerabilidades

### 1.1 Dependencias y CVEs

`composer audit --locked` reporta **24 advisories que afectan a 14 paquetes**.
La gran mayoría son dependencias **transitivas** de `laravel/framework`,
`darkaonline/l5-swagger`, Symfony y Guzzle. La única dependencia **directa** que
requiere actualización es `laravel/framework`.

| Paquete | Versión actual (lock) | Versión segura | Severidad máx. | Tipo |
|---------|-----------------------|----------------|----------------|------|
| `laravel/framework` | v12.48.1 | **>= 12.61.1** | **alta** (CRLF injection en regla `email`; Signed URL path confusion) | Directa |
| `guzzlehttp/guzzle` | 7.10.0 | >= 7.12.1 | media | Transitiva |
| `guzzlehttp/psr7` | 2.8.0 | >= 2.12.1 | media (CRLF injection) | Transitiva |
| `symfony/http-foundation` | v7.4.3 | >= 7.4.13 | media (SSRF bypass) | Transitiva |
| `symfony/http-kernel` | v7.4.3 | última 7.4.x | media | Transitiva |
| `symfony/mailer` | v7.4.3 | última 7.4.x | media | Transitiva |
| `symfony/mime` | v7.4.0 | última 7.4.x | media | Transitiva |
| `symfony/routing` | v7.4.3 | última 7.4.x | media | Transitiva |
| `symfony/process` | v7.4.3 | última 7.4.x | media | Transitiva |
| `symfony/yaml` | v7.4.1 | última 7.4.x | media | Transitiva |
| `symfony/polyfill-intl-idn` | v1.33.0 | última 1.x | media | Transitiva |
| `league/commonmark` | 2.8.0 | >= 2.8.2 | media | Transitiva |
| `psy/psysh` | v0.12.18 | >= 0.12.19 | media (LPE vía `.psysh.php`) | Transitiva (dev) |
| `phpunit/phpunit` | 11.5.33 | >= 11.5.50 | alta (deserialización insegura) | Transitiva (dev) |

**Paquete abandonado:** `doctrine/annotations` (2.0.2) — sin reemplazo directo;
llega como dependencia de `l5-swagger`. Vigilar futuras versiones de
`darkaonline/l5-swagger` que lo retiren.

#### Remediación recomendada

La restricción en `composer.json` ya es `"laravel/framework": "^12.0"`, que
**permite** las versiones parcheadas. La corrección consiste en regenerar el
`composer.lock` en un entorno con acceso de red:

```bash
composer update laravel/framework --with-all-dependencies
# o, para arrastrar todas las dependencias transitivas vulnerables:
composer update
```

> No se modifica `composer.lock` en este PR porque el sandbox no puede descargar
> los nuevos artefactos ni recalcular el árbol de dependencias de forma fiable.
> Esta actualización debe ejecutarse y verificarse (`composer audit`,
> `php artisan test`) en CI o en un entorno de desarrollo con red.

### 1.2 Sanitización e Inyección (SQLi / XSS / CSRF)

- **SQL Injection:** ✅ Sin riesgo detectado. No hay SQL crudo en `app/`
  (`DB::raw`, `whereRaw`, `unprepared`, `DB::statement`, etc.). Todas las
  consultas usan Eloquent / Query Builder con binding de parámetros. Las búsquedas
  con `LIKE` (`CatalogService::searchProducts`) usan binding posicional, no
  interpolación directa en SQL.
- **Validación de entrada:** ✅ Los endpoints que reciben datos usan FormRequests
  con reglas explícitas (`exists:`, `in:`, `Password::min(8)->mixedCase()...`).
  Excepción menor: `ReviewController::index()` y `averageRating()` validan
  `product_id` manualmente en el controlador en lugar de un FormRequest
  (ver §3, hallazgo menor).
- **XSS:** ✅ La API responde JSON vía API Resources; no se renderiza HTML con
  entrada de usuario. El riesgo de XSS recae en los clientes (Web/Android/iOS).
- **CSRF:** ✅ Para clientes SPA se usa Sanctum con cookies + `sanctum/csrf-cookie`;
  los clientes móviles usan Bearer tokens (no susceptibles a CSRF).

### 1.3 Secretos hardcodeados

✅ No se encontraron credenciales, tokens ni claves embebidas en `app/`.
La configuración se lee exclusivamente vía `env()` / `config()`. `.env` está en
`.gitignore`. `.env.example` contiene únicamente placeholders.

---

## 2. Documentación OpenAPI

Las anotaciones `@OA\` (zircote/swagger-php vía L5-Swagger) se encuentran en los
controladores de `app/Http/Controllers/Api/` y los esquemas reutilizables en el
controlador base `app/Http/Controllers/Controller.php`.

- ✅ **Cobertura de códigos HTTP:** Los endpoints documentan 200/201 y los
  errores aplicables (401, 403, 404, 422) referenciando esquemas reutilizables
  (`ValidationError`, `UnauthorizedError`, `ForbiddenError`, `NotFoundError`).
  El checkout (`OrderController::checkout`) documenta además 500.
- ✅ **Parámetros y query strings:** Documentados (`city_id`, `category_id`,
  `search`, `per_page`, `limit`, header `X-Cart-Token`, parámetros de ruta).
- ✅ **Sincronización con Resources:** Los esquemas reflejan los campos expuestos
  por los Resources correspondientes.

Las refactorizaciones de §3 **no alteran** la forma de las respuestas JSON, por
lo que las anotaciones existentes siguen siendo válidas.

---

## 3. Desacoplamiento y Arquitectura

Según `ARCHITECTURAL_GUIDELINES.md`, los controladores deben limitarse a validar
(FormRequest), transformar a DTO y delegar en Services; **la lógica de negocio y
las consultas a datos no deben vivir en el controlador**.

### Hallazgos corregidos en este PR

1. **`ProfileController::update()` — lógica de negocio en el controlador.**
   Construía el nombre completo y concatenaba código + número de teléfono, y
   ejecutaba `$user->update()` directamente.
   **Corrección:** la lógica se movió a `UserService::updateProfile()` y el
   controlador ahora inyecta y delega en el servicio (igual que el resto de
   controladores). Esto además unifica el comportamiento con
   `UserController::updateProfile()`, que ya delegaba en el mismo servicio.

2. **`ProductController::show()` — consulta Eloquent directa en el controlador.**
   Ejecutaba `Product::with([...])->findOrFail($id)` ignorando el `CatalogService`
   ya inyectado.
   **Corrección:** se añadió `CatalogService::getProduct()` (mismas relaciones
   `images`, `categories`, `reviews`) y el controlador delega en él. La respuesta
   JSON es idéntica.

### Hallazgo menor (no modificado, registrado para seguimiento)

- **`ReviewController::index()` / `averageRating()`** validan `product_id`
  manualmente devolviendo 422 con mensaje en inglés. Podría extraerse a un
  FormRequest dedicado para alinearse con la convención del proyecto. No se
  modifica en este PR para mantener el alcance mínimo y evitar cambios de
  comportamiento no solicitados.

### Inyección de dependencias

✅ Todos los servicios se inyectan vía constructor (promoción de propiedades);
no se detectaron instanciaciones `new` de servicios ni acoplamiento fuerte a
facades dentro de la capa de negocio.

---

## 4. Consistencia de Datos y Código

- **Codificación de BD:** ✅ `config/database.php` usa `utf8mb4` /
  `utf8mb4_unicode_ci` en las conexiones MySQL/MariaDB. No se detectaron columnas
  ni tablas con `latin1` huérfano en las migraciones de `database/migrations`.
- **PSR-12 / tipado estricto:** Varios archivos nuevos ya declaran
  `declare(strict_types=1)` (p. ej. `CatalogController`, `UserAddressController`,
  `ProfileController`). La convención del proyecto recomienda añadirlo en archivos
  nuevos. El formateo se gestiona con Laravel Pint (`./vendor/bin/pint`).

---

## Resumen de cambios incluidos en este PR

| Archivo | Cambio |
|---------|--------|
| `app/Services/UserService.php` | `updateProfile()` encapsula la construcción de nombre y teléfono. |
| `app/Http/Controllers/Api/ProfileController.php` | Inyecta `UserService` y delega; sin lógica de negocio. |
| `app/Services/CatalogService.php` | Nuevo método `getProduct()`. |
| `app/Http/Controllers/Api/ProductController.php` | `show()` delega en `CatalogService::getProduct()`. |
| `tests/Feature/Api/ProfileTest.php` | Tests del endpoint de perfil. |
| `tests/Feature/Api/ProductTest.php` | Tests del detalle de producto. |
| `SECURITY_AUDIT.md` | Este informe. |

### Acción pendiente fuera de banda

Ejecutar `composer update` en un entorno con acceso de red para regenerar
`composer.lock` y resolver los 24 advisories, y verificar con
`composer audit && php artisan test`.
