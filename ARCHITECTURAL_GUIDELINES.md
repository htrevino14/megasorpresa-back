# 🏛️ Guía de Arquitectura - MegaSorpresa Backend

## 📋 Índice

1. [Introducción](#introducción)
2. [Stack Tecnológico](#stack-tecnológico)
3. [Arquitectura del Sistema](#arquitectura-del-sistema)
4. [Flujo de Peticiones](#flujo-de-peticiones)
5. [Estructura de Carpetas](#estructura-de-carpetas)
6. [Patrones y Convenciones](#patrones-y-convenciones)
7. [Autenticación](#autenticación)
8. [Testing](#testing)

---

## Introducción

Este proyecto sigue el patrón **"Laravel Way Pro"**, una evolución del enfoque tradicional de Laravel que enfatiza la separación de responsabilidades y la escalabilidad del código. El backend actúa como API central para tres clientes:

- **Web (SPA)**: Single Page Application
- **Android**: Aplicación nativa móvil
- **iOS**: Aplicación nativa móvil

---

## Stack Tecnológico

### Core Framework
- **Laravel 12.x**: Framework PHP principal
- **PHP 8.2+**: Lenguaje de programación

### Base de Datos y Cache
- **MySQL 8.4**: Motor de base de datos principal
- **Redis**: Sistema de caché para optimización de catálogo y sesiones

### Autenticación
- **Laravel Sanctum**: Gestión de tokens para APIs
  - Tokens Bearer para clientes móviles (Android/iOS)
  - Cookies de sesión para Web SPA

### Testing
- **Pest PHP**: Framework de testing moderno para Laravel
- Suite de pruebas unitarias e integración

### Documentación de API
- **L5-Swagger (OpenAPI/Swagger)**: Generación automática de documentación
- Accesible en: `/api/documentation`

### Desarrollo
- **Laravel Sail**: Entorno de desarrollo Docker
- **Vite**: Build tool para assets
- **Laravel Pint**: Linter de código PHP

---

## Arquitectura del Sistema

La arquitectura sigue el patrón **"Laravel Way Pro"** con una clara separación de responsabilidades:

```
┌─────────────┐
│   Cliente   │ (Web SPA, Android, iOS)
└──────┬──────┘
       │ HTTP/JSON
       ▼
┌─────────────────────────────────────────┐
│          API Layer (Routes)             │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│         Controller Layer                │
│  • Validación de entrada (FormRequest) │
│  • Transformación a DTO                 │
│  • Delegación a Services                │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│          Service Layer                  │
│  • Lógica de negocio                    │
│  • Orquestación de procesos             │
│  • Uso de Repositories y Traits         │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│        Repository Layer                 │
│  • Abstracción de datos                 │
│  • Queries Eloquent                     │
│  • Integración con APIs externas        │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│          Model Layer                    │
│  • Eloquent Models                      │
│  • Relaciones                           │
│  • Accessors/Mutators                   │
└──────────────┬──────────────────────────┘
               │
       ┌───────┴────────┐
       ▼                ▼
┌─────────────┐  ┌─────────────┐
│   MySQL     │  │    Redis    │
└─────────────┘  └─────────────┘
```

---

## Flujo de Peticiones

### Ejemplo: Proceso de Checkout de Pedido

```php
1. Cliente → POST /api/orders
   ↓
2. Route (api.php) → OrderController@store
   ↓
3. Controller:
   • Valida con OrderRequest (FormRequest)
   • Crea OrderDTO desde el request
   • Llama a OrderProcessingService
   ↓
4. Service (OrderProcessingService):
   • Valida stock disponible
   • Verifica cupón de descuento
   • Aplica reglas de negocio (edad mínima, etc.)
   • Llama a OrderRepository para persistir
   ↓
5. Repository (OrderRepository):
   • Crea el registro en MySQL
   • Actualiza inventario
   • Retorna el modelo Order
   ↓
6. Service:
   • Dispara eventos (OrderCreated)
   • Retorna OrderDTO al controller
   ↓
7. Controller:
   • Transforma DTO a JSON Resource
   • Retorna respuesta HTTP
   ↓
8. Events & Listeners (asíncronos):
   • Envía notificación de confirmación
   • Actualiza caché de productos en Redis
   • Registra en analytics
```

---

## Estructura de Carpetas

Toda la lógica de negocio reside dentro de `app/`, organizada de la siguiente manera:

```
app/
├── DTOs/                    # Data Transfer Objects
│   ├── OrderDTO.php
│   └── ProductDTO.php
│
├── Services/                # Lógica de negocio
│   ├── OrderProcessingService.php
│   ├── PaymentService.php
│   └── InventoryService.php
│
├── Repositories/            # Capa de abstracción de datos
│   ├── OrderRepository.php
│   ├── ProductRepository.php
│   └── UserRepository.php
│
├── Traits/                  # Comportamiento reutilizable
│   ├── HasDiscounts.php
│   ├── Uploader.php
│   └── Filterable.php
│
├── Http/
│   ├── Controllers/         # Punto de entrada HTTP
│   │   └── Api/
│   │       ├── OrderController.php
│   │       └── ProductController.php
│   ├── Requests/            # Form Request Validation
│   │   ├── OrderRequest.php
│   │   └── ProductRequest.php
│   └── Resources/           # API JSON Resources
│       ├── OrderResource.php
│       └── ProductResource.php
│
├── Models/                  # Eloquent Models
│   ├── Order.php
│   ├── Product.php
│   └── User.php
│
├── Events/                  # Eventos del sistema
│   └── OrderCreated.php
│
├── Listeners/               # Manejadores de eventos
│   └── SendOrderConfirmation.php
│
└── Providers/               # Service Providers
    └── AppServiceProvider.php
```

### Responsabilidades de Cada Capa

| Carpeta | Responsabilidad | Ejemplo |
|---------|----------------|---------|
| **DTOs/** | Objetos inmutables para transferir datos entre capas. Aseguran tipo seguro. | `OrderDTO` contiene datos validados del pedido |
| **Services/** | Lógica de negocio pura. Orquestan operaciones complejas. | `OrderProcessingService` maneja todo el flujo de checkout |
| **Repositories/** | Abstracción de acceso a datos. Queries Eloquent o APIs externas. | `ProductRepository` obtiene productos con filtros |
| **Traits/** | Comportamiento transversal reutilizable. | `HasDiscounts` agrega lógica de descuentos a modelos |
| **Controllers/** | Manejo HTTP, delegación a Services, respuestas JSON. | `OrderController` valida y delega a Service |
| **Models/** | Representación de entidades de base de datos. Relaciones. | `Order` con relaciones a `OrderItems` y `User` |
| **Events/Listeners/** | Tareas asíncronas post-operación. | Enviar emails, actualizar cache, notificaciones |

---

## Patrones y Convenciones

### 1. DTOs (Data Transfer Objects)

Los DTOs son objetos inmutables que transportan datos entre capas:

```php
namespace App\DTOs;

class OrderDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly array $items,
        public readonly ?string $couponCode,
        public readonly string $shippingAddress,
    ) {}

    public static function fromRequest(OrderRequest $request): self
    {
        return new self(
            userId: auth()->id(),
            items: $request->input('items'),
            couponCode: $request->input('coupon_code'),
            shippingAddress: $request->input('shipping_address'),
        );
    }
}
```

### 2. Services (Business Logic)

Los Services contienen la lógica de negocio:

```php
namespace App\Services;

use App\DTOs\OrderDTO;
use App\Repositories\OrderRepository;
use App\Events\OrderCreated;

class OrderProcessingService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private InventoryService $inventoryService,
    ) {}

    public function process(OrderDTO $dto): Order
    {
        // Validar stock
        $this->inventoryService->validateStock($dto->items);
        
        // Aplicar descuentos
        $total = $this->calculateTotal($dto);
        
        // Crear orden
        $order = $this->orderRepository->create([
            'user_id' => $dto->userId,
            'total' => $total,
            'status' => 'pending',
        ]);
        
        // Disparar evento
        event(new OrderCreated($order));
        
        return $order;
    }
}
```

### 3. Repositories (Data Access)

Los Repositories abstraen el acceso a datos:

```php
namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function findByUser(int $userId): Collection
    {
        return Order::where('user_id', $userId)
            ->with(['items', 'shipping'])
            ->latest()
            ->get();
    }
}
```

### 4. Traits (Shared Behavior)

Los Traits proporcionan comportamiento reutilizable:

```php
namespace App\Traits;

trait HasDiscounts
{
    public function applyDiscount(float $percentage): float
    {
        return $this->price * (1 - $percentage / 100);
    }

    public function hasActiveDiscount(): bool
    {
        return $this->discount_until && 
               $this->discount_until->isFuture();
    }
}
```

---

## Autenticación

### Laravel Sanctum - Configuración Multi-Cliente

El sistema utiliza **Laravel Sanctum** para autenticación, con diferentes estrategias según el cliente:

#### Clientes Móviles (Android/iOS)

Los clientes móviles usan **Bearer Tokens**:

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}

Response:
{
  "token": "1|abc123xyz...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  }
}
```

**Uso del token en peticiones subsecuentes:**

```http
GET /api/products
Authorization: Bearer 1|abc123xyz...
```

#### Cliente Web (SPA)

El cliente web usa **Session-based authentication** con cookies:

```javascript
// Primero: Obtener CSRF token
await axios.get('/sanctum/csrf-cookie');

// Luego: Login
await axios.post('/api/auth/login', {
  email: 'user@example.com',
  password: 'password123'
});

// Las peticiones subsecuentes incluyen automáticamente la cookie
await axios.get('/api/products');
```

#### Configuración en Controllers

```php
// routes/api.php
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'profile']);
    Route::post('/orders', [OrderController::class, 'store']);
});
```

#### Generación de Tokens

```php
namespace App\Http\Controllers\Api;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        
        // Generar token para móviles
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }
}
```

---

## Testing

### Pest PHP - Framework de Testing

El proyecto utiliza **Pest** para testing, proporcionando una sintaxis más expresiva que PHPUnit:

#### Estructura de Tests

```
tests/
├── Feature/              # Tests de integración
│   ├── Api/
│   │   ├── OrderTest.php
│   │   └── ProductTest.php
│   └── Auth/
│       └── LoginTest.php
├── Unit/                 # Tests unitarios
│   ├── Services/
│   │   └── OrderProcessingServiceTest.php
│   └── DTOs/
│       └── OrderDTOTest.php
└── Pest.php             # Configuración global de Pest
```

#### Ejemplo de Test con Pest

```php
<?php

use App\Models\User;
use App\Models\Product;

it('can create an order with valid data', function () {
    // Arrange
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10]);
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ],
            'shipping_address' => '123 Main St'
        ]);
    
    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'total', 'status']
        ]);
    
    expect($user->orders()->count())->toBe(1);
});

it('validates stock before creating order', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 1]);
    
    $response = $this->actingAs($user)
        ->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10]
            ]
        ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors('items');
});
```

#### Ejecutar Tests

```bash
# Todos los tests
./vendor/bin/pest

# Tests específicos
./vendor/bin/pest tests/Feature/Api/OrderTest.php

# Con cobertura
./vendor/bin/pest --coverage

# Modo watch (re-ejecuta al cambiar archivos)
./vendor/bin/pest --watch
```

---

## Convenciones Generales

### Nomenclatura

- **Controllers**: Singular, sufijo `Controller` → `OrderController`
- **Models**: Singular → `Order`, `Product`
- **Migrations**: Snake case → `create_orders_table`
- **Services**: Sufijo `Service` → `OrderProcessingService`
- **Repositories**: Sufijo `Repository` → `OrderRepository`
- **DTOs**: Sufijo `DTO` → `OrderDTO`
- **Events**: Pasado → `OrderCreated`, `PaymentProcessed`
- **Listeners**: Imperativo → `SendOrderConfirmation`

### Respuestas API

Usar API Resources para respuestas consistentes:

```php
// Success
return OrderResource::make($order);

// Collection
return OrderResource::collection($orders);

// Error
return response()->json([
    'message' => 'Resource not found'
], 404);
```

### Manejo de Errores

```php
// En Services, lanzar excepciones específicas
throw new InsufficientStockException($product);

// En Handler, convertir a respuestas JSON
public function render($request, Throwable $exception)
{
    if ($exception instanceof InsufficientStockException) {
        return response()->json([
            'message' => $exception->getMessage()
        ], 400);
    }
}
```

---

## Recursos Adicionales

- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Sanctum](https://laravel.com/docs/sanctum)
- [Documentación de Pest](https://pestphp.com)
- [OpenAPI Specification](https://swagger.io/specification/)
- [README del proyecto](./README.md) - Instrucciones de instalación

---

**Última actualización**: Enero 2026
