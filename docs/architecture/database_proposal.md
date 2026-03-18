# Propuesta de Schema de Base de Datos — MegaSorpresa Back

**Versión:** 1.0  
**Fecha:** Marzo 2026  
**Basado en:** Análisis de componentes de `megasorpresa-front` (Nuxt 4 / Vue 3)

---

## Índice

1. [Contexto y Objetivo](#1-contexto-y-objetivo)
2. [Tablas Existentes (Referencia)](#2-tablas-existentes-referencia)
3. [Tablas Nuevas Propuestas — CMS / Landing Page](#3-tablas-nuevas-propuestas--cms--landing-page)
   - 3.1 [announcement_bars](#31-announcement_bars)
   - 3.2 [hero_slides](#32-hero_slides)
   - 3.3 [megamenu_categories](#33-megamenu_categories)
   - 3.4 [megamenu_subcategory_groups](#34-megamenu_subcategory_groups)
   - 3.5 [megamenu_subcategory_items](#35-megamenu_subcategory_items)
   - 3.6 [megamenu_promo_panels](#36-megamenu_promo_panels)
   - 3.7 [category_carousel_items](#37-category_carousel_items)
   - 3.8 [age_groups](#38-age_groups)
   - 3.9 [footer_sections](#39-footer_sections)
   - 3.10 [footer_links](#310-footer_links)
   - 3.11 [social_links](#311-social_links)
   - 3.12 [payment_methods](#312-payment_methods)
   - 3.13 [newsletter_categories](#313-newsletter_categories)
   - 3.14 [regions](#314-regions)
4. [Resumen de Relaciones](#4-resumen-de-relaciones)
5. [Diagrama Entidad-Relación (descripción textual)](#5-diagrama-entidad-relación-descripción-textual)

---

## 1. Contexto y Objetivo

El frontend (`megasorpresa-front`) define su Landing Page a través de cinco componentes principales con **datos estáticos embebidos** en el código:

| Componente | Archivo |
|---|---|
| Barra de anuncio + Header / Megamenu | `TheHeader.vue` |
| Hero Slider | `HeroSection.vue` |
| Carrusel de categorías destacadas | `CategoryCarousel.vue` |
| Selector de edad | `AgeSelector.vue` |
| Footer | `TheFooter.vue` |

El objetivo de este documento es definir las tablas de base de datos (estilo Laravel migrations) que permitirán al **panel de administración** gestionar dinámicamente todo el contenido de la Landing Page sin necesidad de modificar el código frontend.

---

## 2. Tablas Existentes (Referencia)

Las siguientes tablas ya están creadas en el proyecto y **no requieren cambios** para soportar la Landing Page. Se listan como referencia de las relaciones que se establezcan con las nuevas tablas.

| Tabla | Descripción |
|---|---|
| `users` | Usuarios del sistema (clientes y administradores) |
| `categories` | Árbol de categorías de productos (`parent_id` para subcategorías) |
| `products` | Catálogo de productos |
| `product_images` | Imágenes adicionales por producto |
| `product_category` | Pivot N:N entre productos y categorías |
| `product_addons` | Complementos/accesorios de productos |
| `availability_zones` | Zonas de disponibilidad por ciudad/producto |
| `order_statuses` | Estados posibles de un pedido |
| `orders` | Pedidos de clientes |
| `order_items` | Líneas de cada pedido |
| `order_details` | Detalle adicional de pedidos (envío, etc.) |
| `banners` | Banners genéricos (title, image_url, link_to, location, is_active) |
| `coupons` | Cupones de descuento |
| `reviews` | Reseñas de productos |
| `states` | Estados/provincias de México |
| `cities` | Ciudades |
| `delivery_slots` | Franjas horarias de entrega |
| `user_addresses` | Direcciones de usuarios |
| `reminders` | Recordatorios de usuarios |

> **Nota sobre `banners`:** La tabla existente cubre un caso de uso genérico (imagen + link + ubicación). La nueva tabla `hero_slides` proporciona una estructura específica y enriquecida para el slider principal de la Landing Page con soporte para imágenes separadas en versión desktop/mobile y orden de presentación.

---

## 3. Tablas Nuevas Propuestas — CMS / Landing Page

---

### 3.1 `announcement_bars`

**Propósito:** Gestionar la barra de anuncio deslizable (top bar) del header, con soporte de programación por fechas.

**Componente frontend:** `TheHeader.vue` → sección superior (dismissible announcement bar)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `message` | `string` | NOT NULL | Texto del anuncio |
| `link_url` | `string` | nullable | URL de destino al hacer clic |
| `link_label` | `string` | nullable | Texto del enlace (ej. "Ver más") |
| `bg_color` | `string(7)` | default `#0072E3` | Color de fondo en hex |
| `text_color` | `string(7)` | default `#FFFFFF` | Color del texto en hex |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `starts_at` | `timestamp` | nullable | Inicio de programación |
| `ends_at` | `timestamp` | nullable | Fin de programación |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:** Ninguna (tabla independiente).

**Índices:**
```php
$table->index('is_active');
$table->index(['starts_at', 'ends_at']);
```

---

### 3.2 `hero_slides`

**Propósito:** Gestionar las diapositivas del hero slider principal de la Landing Page.

**Componente frontend:** `HeroSection.vue`

**Datos estáticos actuales (3 slides):**
- "Gran valor – 2 x $299" | RC Autos
- "Nuevas llegadas LEGO"
- "Gaming – La mejor selección"

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `title` | `string` | NOT NULL | Título principal del slide |
| `subtitle` | `string` | nullable | Subtítulo o descripción corta |
| `cta_text` | `string` | nullable | Texto del botón CTA |
| `cta_link` | `string` | nullable | URL destino del botón CTA |
| `image_url_desktop` | `string` | NOT NULL | Imagen para escritorio |
| `image_url_mobile` | `string` | nullable | Imagen para móvil (opcional) |
| `alt_text` | `string` | nullable | Texto alternativo para accesibilidad |
| `bg_color` | `string(7)` | nullable | Color de fondo del panel de texto |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden de aparición |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `starts_at` | `timestamp` | nullable | Inicio de programación |
| `ends_at` | `timestamp` | nullable | Fin de programación |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:** Ninguna (tabla independiente).

**Índices:**
```php
$table->index(['is_active', 'sort_order']);
$table->index(['starts_at', 'ends_at']);
```

---

### 3.3 `megamenu_categories`

**Propósito:** Gestionar las categorías de primer nivel del megamenú de navegación.

**Componente frontend:** `TheHeader.vue` → `navCategories` (5 categorías: Juguetes, Bebés, Exterior, Gaming, Educativos)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `name` | `string` | NOT NULL | Nombre visible (ej. "Juguetes") |
| `slug` | `string` | unique, NOT NULL | Slug para URL (ej. "juguetes") |
| `icon` | `string` | nullable | Nombre de icono o clase CSS |
| `category_id_destination` | `foreignId` | nullable, FK → `categories.id` ON DELETE SET NULL | Categoría del catálogo de productos a la que redirige esta entrada del menú |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden de aparición en el menú |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:**
- 1:N con `megamenu_subcategory_groups` (una categoría tiene varios grupos)
- 1:1 con `megamenu_promo_panels` (una categoría tiene un panel promocional)
- N:1 con `categories` vía `category_id_destination` (categoría destino del catálogo)

**Índices:**
```php
$table->index(['is_active', 'sort_order']);
```

---

### 3.4 `megamenu_subcategory_groups`

**Propósito:** Gestionar los grupos de subcategorías dentro de cada categoría del megamenú (ej. en "Juguetes": "Acción", "Creatividad", "Juegos de mesa", "Muñecas").

**Componente frontend:** `TheHeader.vue` → `subcategoryGroups[]`

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `megamenu_category_id` | `foreignId` | FK → `megamenu_categories.id` ON DELETE CASCADE | Categoría padre |
| `title` | `string` | NOT NULL | Título del grupo (ej. "Acción") |
| `category_id_destination` | `foreignId` | nullable, FK → `categories.id` ON DELETE SET NULL | Categoría del catálogo de productos a la que redirige este grupo |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden dentro de la categoría |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:**
- N:1 con `megamenu_categories`
- 1:N con `megamenu_subcategory_items`
- N:1 con `categories` vía `category_id_destination` (categoría destino del catálogo)

**Índices:**
```php
$table->index(['megamenu_category_id', 'sort_order']);
```

---

### 3.5 `megamenu_subcategory_items`

**Propósito:** Gestionar los ítems individuales dentro de cada grupo del megamenú.

**Componente frontend:** `TheHeader.vue` → `subcategoryGroups[].items[]`

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `megamenu_subcategory_group_id` | `foreignId` | FK → `megamenu_subcategory_groups.id` ON DELETE CASCADE | Grupo padre |
| `label` | `string` | NOT NULL | Texto visible del ítem |
| `category_id_destination` | `foreignId` | nullable, FK → `categories.id` ON DELETE SET NULL | Categoría del catálogo de productos a la que redirige este ítem |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden dentro del grupo |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:**
- N:1 con `megamenu_subcategory_groups`
- N:1 con `categories` vía `category_id_destination` (categoría destino del catálogo)

**Índices:**
```php
$table->index(['megamenu_subcategory_group_id', 'sort_order']);
```

---

### 3.6 `megamenu_promo_panels`

**Propósito:** Gestionar el panel promocional (lateral derecho) que aparece en cada categoría del megamenú.

**Componente frontend:** `TheHeader.vue` → `navCategories[].promo` (badge, title, description, emoji, bgColor, linkText)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `megamenu_category_id` | `foreignId` | FK → `megamenu_categories.id` ON DELETE CASCADE, unique | Categoría asociada (1:1) |
| `badge` | `string` | nullable | Texto de etiqueta/badge (ej. "¡Nuevo!") |
| `title` | `string` | NOT NULL | Título del panel promo |
| `description` | `text` | nullable | Descripción del panel |
| `emoji` | `string(10)` | nullable | Emoji decorativo |
| `bg_color` | `string(7)` | nullable | Color de fondo en hex |
| `link_text` | `string` | nullable | Texto del enlace |
| `link_url` | `string` | nullable | URL del enlace |
| `image_url` | `string` | nullable | Imagen opcional del panel |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:**
- 1:1 con `megamenu_categories`

**Índices:**
```php
$table->unique('megamenu_category_id');
```

---

### 3.7 `category_carousel_items`

**Propósito:** Gestionar las tarjetas de categorías destacadas que aparecen en el carrusel horizontal debajo del hero.

**Componente frontend:** `CategoryCarousel.vue` (8 tarjetas con imagen, nombre y color de fondo)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `category_id` | `foreignId` | nullable, FK → `categories.id` ON DELETE SET NULL | Categoría de producto relacionada (opcional) |
| `name` | `string` | NOT NULL | Nombre visible en la tarjeta |
| `slug` | `string` | NOT NULL | Slug para URL de filtrado |
| `image_url` | `string` | NOT NULL | URL de la imagen de la tarjeta |
| `bg_color` | `string(7)` | nullable | Color de fondo en hex (ej. `#D1FAE5`) |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden de aparición |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:**
- N:1 con `categories` (opcional — permite vincular a una categoría real de productos)

**Índices:**
```php
$table->index(['is_active', 'sort_order']);
```

---

### 3.8 `age_groups`

**Propósito:** Gestionar los grupos de edad que se muestran como botones circulares en el selector de edad (AgeSelector).

**Componente frontend:** `AgeSelector.vue` (6 grupos: 0-18M, 18-36M, 3-5A, 6-8A, 9-11A, BIG KIDS)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `label` | `string` | NOT NULL | Rango de edad visible (ej. "0-18", "BIG") |
| `sublabel` | `string` | NOT NULL | Unidad del rango (ej. "MESES", "AÑOS", "KIDS") |
| `slug` | `string` | unique, NOT NULL | Slug para URL de filtrado (ej. "0-18-meses") |
| `bg_color` | `string(7)` | NOT NULL | Color de fondo del botón en hex |
| `text_color` | `string(7)` | default `#FFFFFF` | Color del texto en hex |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden de aparición |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:** Ninguna (tabla independiente).

**Índices:**
```php
$table->index(['is_active', 'sort_order']);
```

---

### 3.9 `footer_sections`

**Propósito:** Gestionar las columnas/secciones de información del footer (ej. "Información al cliente", "Ayuda y FAQ").

**Componente frontend:** `TheFooter.vue` → columnas de información (4 columnas)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `title` | `string` | NOT NULL | Título de la columna |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden de aparición |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:**
- 1:N con `footer_links`

**Índices:**
```php
$table->index(['is_active', 'sort_order']);
```

---

### 3.10 `footer_links`

**Propósito:** Gestionar los enlaces individuales dentro de cada sección del footer.

**Componente frontend:** `TheFooter.vue` → `footerColumns[].links[]` (ej. "Solicitar catálogo", "Tarjetas regalo", etc.)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `footer_section_id` | `foreignId` | FK → `footer_sections.id` ON DELETE CASCADE | Sección padre |
| `label` | `string` | NOT NULL | Texto del enlace |
| `url` | `string` | NOT NULL | URL de destino |
| `icon` | `string` | nullable | Nombre de icono o clase CSS |
| `open_in_new_tab` | `boolean` | default `false` | Abrir en nueva pestaña |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden dentro de la sección |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:**
- N:1 con `footer_sections`

**Índices:**
```php
$table->index(['footer_section_id', 'sort_order']);
```

---

### 3.11 `social_links`

**Propósito:** Gestionar los enlaces a redes sociales mostrados en el footer.

**Componente frontend:** `TheFooter.vue` → `socialLinks[]` (Facebook, Instagram, YouTube, TikTok, X, Snapchat)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `platform` | `string` | NOT NULL | Nombre de la plataforma (ej. "Facebook") |
| `url` | `string` | NOT NULL | URL del perfil |
| `icon_class` | `string` | nullable | Clase CSS del icono (ej. "fab fa-facebook") |
| `icon_svg` | `text` | nullable | SVG inline del icono (alternativa a icon_class) |
| `initial` | `string(5)` | nullable | Inicial de fallback si no hay icono |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden de aparición |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:** Ninguna (tabla independiente).

**Índices:**
```php
$table->index(['is_active', 'sort_order']);
```

---

### 3.12 `payment_methods`

**Propósito:** Gestionar los métodos de pago aceptados que se muestran en el footer.

**Componente frontend:** `TheFooter.vue` → `paymentMethods[]` (Visa, Mastercard, Google Pay, Apple Pay, Amex, PayPal, Klarna)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `name` | `string` | NOT NULL | Nombre del método (ej. "Visa") |
| `logo_url` | `string` | nullable | URL del logotipo |
| `icon_class` | `string` | nullable | Clase CSS del icono alternativo |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden de aparición |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:** Ninguna (tabla independiente).

**Índices:**
```php
$table->index(['is_active', 'sort_order']);
```

---

### 3.13 `newsletter_categories`

**Propósito:** Gestionar las categorías de suscripción disponibles en el formulario de newsletter del footer.

**Componente frontend:** `TheFooter.vue` → checkboxes de newsletter (Juguetes, Bebés, Gaming)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `label` | `string` | NOT NULL | Texto visible del checkbox (ej. "Juguetes") |
| `slug` | `string` | unique, NOT NULL | Slug identificador (ej. "juguetes") |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden de aparición |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:**
- N:N con `users` a través de `newsletter_subscriptions` (tabla pivot)

**Pivot table `newsletter_subscriptions`:**

| Columna | Tipo (Laravel migration) | Restricciones |
|---|---|---|
| `id` | `bigIncrements` | PK |
| `user_id` | `foreignId` | FK → `users.id` ON DELETE CASCADE |
| `newsletter_category_id` | `foreignId` | FK → `newsletter_categories.id` ON DELETE CASCADE |
| `subscribed_at` | `timestamp` | default `now()` |

```php
$table->unique(['user_id', 'newsletter_category_id']);
```

---

### 3.14 `regions`

**Propósito:** Gestionar las regiones/países disponibles en el selector de región del footer.

**Componente frontend:** `TheFooter.vue` → selector de región (MX, US, UK, DE, FR, ES)

| Columna | Tipo (Laravel migration) | Restricciones | Descripción |
|---|---|---|---|
| `id` | `bigIncrements` | PK | Identificador |
| `name` | `string` | NOT NULL | Nombre del país/región (ej. "México") |
| `code` | `string(5)` | unique, NOT NULL | Código ISO (ej. "MX") |
| `flag_emoji` | `string(10)` | nullable | Emoji de bandera (ej. "🇲🇽") |
| `flag_url` | `string` | nullable | URL de imagen de bandera (alternativa) |
| `locale` | `string(10)` | nullable | Locale para i18n (ej. "es-MX") |
| `currency_code` | `string(3)` | nullable | Código de moneda ISO 4217 (ej. "MXN") |
| `sort_order` | `unsignedSmallInteger` | default `0` | Orden de aparición |
| `is_active` | `boolean` | default `true` | Control de visibilidad |
| `is_default` | `boolean` | default `false` | Región por defecto |
| `created_at` | `timestamp` | auto | — |
| `updated_at` | `timestamp` | auto | — |

**Relaciones:** Ninguna (tabla independiente).

**Índices:**
```php
$table->index(['is_active', 'sort_order']);
$table->unique('code');
```

---

## 4. Resumen de Relaciones

| Tabla Padre | Tipo | Tabla Hija | Descripción |
|---|---|---|---|
| `megamenu_categories` | 1:N | `megamenu_subcategory_groups` | Una categoría tiene muchos grupos de subcategorías |
| `megamenu_subcategory_groups` | 1:N | `megamenu_subcategory_items` | Un grupo tiene muchos ítems |
| `megamenu_categories` | 1:1 | `megamenu_promo_panels` | Cada categoría tiene un panel promo |
| `categories` | 1:N | `megamenu_categories` | vía `category_id_destination` — categoría destino para la entrada de menú |
| `categories` | 1:N | `megamenu_subcategory_groups` | vía `category_id_destination` — categoría destino para el grupo |
| `categories` | 1:N | `megamenu_subcategory_items` | vía `category_id_destination` — categoría destino para el ítem |
| `categories` | 1:N | `category_carousel_items` | Una categoría de producto puede estar en el carrusel |
| `footer_sections` | 1:N | `footer_links` | Una sección del footer tiene muchos enlaces |
| `users` | N:N | `newsletter_categories` | vía `newsletter_subscriptions` |

---

## 5. Diagrama Entidad-Relación (descripción textual)

```
┌─────────────────────────────────────┐
│         HEADER / MEGAMENU           │
│                                     │
│  announcement_bars (independiente)  │
│                                     │
│  megamenu_categories                │
│    ├─── megamenu_subcategory_groups │
│    │        └─── megamenu_subcategory_items
│    └─── megamenu_promo_panels (1:1) │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│           HERO SECTION              │
│                                     │
│  hero_slides (independiente)        │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│       CATEGORY CAROUSEL             │
│                                     │
│  category_carousel_items            │
│    └─── categories (FK opcional)    │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│         AGE SELECTOR                │
│                                     │
│  age_groups (independiente)         │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│             FOOTER                  │
│                                     │
│  footer_sections                    │
│    └─── footer_links                │
│                                     │
│  social_links (independiente)       │
│  payment_methods (independiente)    │
│  regions (independiente)            │
│                                     │
│  newsletter_categories              │
│    └─── newsletter_subscriptions    │
│               ├─── users            │
│               └─── newsletter_categories
└─────────────────────────────────────┘
```

---

## Notas de Implementación

1. **Todas las tablas nuevas** deben ser creadas como migrations numeradas de forma secuencial a partir de `2026_03_18_...`.
2. **Soft Deletes:** No se recomienda para tablas de CMS (preferable `is_active`), pero puede añadirse si el administrador requiere papelera de reciclaje.
3. **Caché:** Las respuestas de estas tablas deberían ser cacheadas en Redis dado que son datos que cambian con poca frecuencia (`Cache::remember`).
4. **Seeders:** Crear seeders con los datos estáticos actuales del frontend para facilitar el onboarding inicial (ver datos hardcodeados en cada componente Vue).
5. **APIs REST sugeridas:**
   - `GET /api/cms/hero-slides` — Slides activos ordenados
   - `GET /api/cms/megamenu` — Árbol completo del megamenú
   - `GET /api/cms/category-carousel` — Tarjetas del carrusel
   - `GET /api/cms/age-groups` — Grupos de edad activos
   - `GET /api/cms/footer` — Datos completos del footer (secciones + links + social + pagos + regiones)
   - `GET /api/cms/announcement` — Anuncio activo vigente
