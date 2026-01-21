# GitHub Copilot Instructions - MegaSorpresa Backend

## Project Context

**MegaSorpresa** is a toy e-commerce backend API built with Laravel, serving three clients: Web (SPA), Android, and iOS. The platform enables customers to purchase and ship toys with city-specific availability, delivery scheduling, and comprehensive order management.

---

## General Standards

### PHP Version and Type Safety
- **ALWAYS** use PHP 8.2+ features
- **ALWAYS** declare strict types at the top of every PHP file:
  ```php
  <?php
  
  declare(strict_types=1);
  
  namespace App\...;
  ```
- **ALWAYS** use type hints for parameters, return types, and properties
- **ALWAYS** use readonly properties in DTOs for immutability
- Use named arguments when calling functions with multiple parameters for clarity

### Code Style
- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting: `./vendor/bin/sail pint`
- Use descriptive variable and method names in English
- Add PHPDoc comments only when adding value beyond type hints
- Keep methods focused and single-purpose (max ~50 lines)

### Error Handling
- Throw specific exceptions in Services
- Let Laravel handle exception rendering in production
- Use Form Requests for validation errors (422 responses)
- Return appropriate HTTP status codes:
  - 200: Success
  - 201: Created
  - 204: No Content
  - 400: Bad Request
  - 401: Unauthorized
  - 403: Forbidden
  - 404: Not Found
  - 422: Validation Error
  - 500: Server Error

---

## Layer Responsibilities

### Controllers (`app/Http/Controllers/Api/`)
**Purpose**: HTTP request/response handling ONLY

**MUST DO**:
- Accept FormRequest for validation
- Create DTO from validated request
- Call Service method with DTO
- Transform Service response to Resource
- Return JSON response

**MUST NOT DO**:
- ❌ NO business logic in controllers
- ❌ NO direct database queries
- ❌ NO calculations or data manipulation
- ❌ NO if/else business rules

**Example**:
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\OrderDTO;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function store(StoreOrderRequest $request): OrderResource
    {
        $dto = OrderDTO::fromRequest($request);
        $order = $this->orderService->createOrder($dto);
        
        return OrderResource::make($order);
    }
}
```

### Services (`app/Services/`)
**Purpose**: Business logic and orchestration

**MUST DO**:
- Contain ALL business logic
- Use dependency injection for other services/repositories
- Work with DTOs for input
- Return Models or Collections
- **ALWAYS** use `DB::transaction()` for critical operations:
  - Order creation (checkout)
  - Payment processing
  - Inventory updates
  - Any multi-table operations that must be atomic

**MUST NOT DO**:
- ❌ NO HTTP-specific code (Request, Response)
- ❌ NO direct return of Resources

**Example**:
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\OrderDTO;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(OrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {
            // Calculate totals
            $subtotal = $this->calculateSubtotal($dto->items);
            
            // Create order
            $order = Order::create([
                'user_id' => $dto->user_id,
                'total_amount' => $subtotal,
                // ...
            ]);
            
            // Create order items and update stock
            foreach ($dto->items as $item) {
                $order->items()->create($item);
                // Update stock...
            }
            
            return $order;
        });
    }
}
```

### DTOs (`app/DTOs/`)
**Purpose**: Immutable data transfer between layers

**MUST DO**:
- Use readonly class keyword (PHP 8.2+)
- Use readonly properties
- Provide static factory method `fromRequest()`
- Use typed properties

**Example**:
```php
<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class OrderDTO
{
    public function __construct(
        public int $user_id,
        public array $items,
        public ?string $coupon_code,
        public string $recipient_name,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            user_id: $request->user()->id,
            items: $request->input('items', []),
            coupon_code: $request->input('coupon_code'),
            recipient_name: $request->input('recipient_name'),
        );
    }
}
```

### Form Requests (`app/Http/Requests/`)
**Purpose**: Input validation

**MUST DO**:
- **ALWAYS** create a FormRequest for ANY endpoint that accepts data
- Use `exists:table,column` for foreign key validation
- Use descriptive validation messages
- Validate nested arrays properly (e.g., `items.*.product_id`)
- Return `authorize()` as `true` unless implementing authorization logic

**Example**:
```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'recipient_name' => 'required|string|max:255',
            'delivery_date' => 'required|date|after:now',
        ];
    }
}
```

### Models (`app/Models/`)
**Purpose**: Database representation and relationships

**MUST DO**:
- Define all relationships (hasMany, belongsTo, belongsToMany)
- Use `$fillable` for mass assignment protection
- Use `$casts` for type casting (dates, booleans, decimals)
- Define query scopes for common queries
- Use soft deletes where appropriate (products, orders)

**Example**:
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'status_id',
        'total_amount',
        'payment_method',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

### Resources (`app/Http/Resources/`)
**Purpose**: JSON response transformation

**MUST DO**:
- Transform model data to API response format
- Use `whenLoaded()` for conditional relationships
- Format dates consistently (ISO 8601)
- Hide sensitive data

**Example**:
```php
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'total_amount' => $this->total_amount,
            'status' => $this->status->name,
            'created_at' => $this->created_at->toISOString(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
```

---

## Database Conventions

### Migration Naming
- Use timestamp prefix: `YYYY_MM_DD_HHMMSS`
- Table names: plural, snake_case (e.g., `create_order_items_table`)
- Pivot tables: alphabetically ordered, singular (e.g., `product_category`)

### Table Structure
- Primary key: `id` (unsigned big integer, auto-increment)
- Foreign keys: `{model}_id` (e.g., `user_id`, `product_id`)
- Timestamps: `created_at`, `updated_at` (use `$table->timestamps()`)
- Soft deletes: `deleted_at` (use `$table->softDeletes()`)
- Boolean fields: prefix with `is_` or `has_` (e.g., `is_active`, `has_discount`)
- Decimal fields: specify precision (e.g., `decimal('price', 10, 2)`)

### Foreign Keys
- **ALWAYS** add foreign key constraints
- Use `constrained()` for automatic reference
- Define `onDelete()` behavior:
  - `cascade`: Delete related records
  - `restrict`: Prevent deletion
  - `set null`: Set to null

**Example**:
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('status_id')->constrained('order_statuses')->onDelete('restrict');
    $table->decimal('total_amount', 10, 2);
    $table->string('tracking_number')->nullable()->unique();
    $table->timestamps();
    $table->softDeletes();
    
    // Indexes for performance
    $table->index('tracking_number');
});
```

---

## API Standards

### Authentication
- Use Laravel Sanctum for API authentication
- Bearer tokens for mobile clients (Android/iOS)
- Session cookies for web SPA
- Protect routes with `auth:sanctum` middleware

**Route Example**:
```php
// Public routes
Route::prefix('catalog')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/user', [UserController::class, 'show']);
});
```

### Response Format
- Use API Resources for consistent responses
- Return collections with `Resource::collection()`
- Use pagination for lists (default 15 items)
- Include relationship data when needed with `load()`

**Success Response**:
```json
{
  "data": {
    "id": 1,
    "name": "Product",
    "price": 99.99
  }
}
```

**Validation Error Response** (handled automatically):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### RESTful Conventions
- GET `/api/resources` - List all
- GET `/api/resources/{id}` - Show one
- POST `/api/resources` - Create
- PUT/PATCH `/api/resources/{id}` - Update
- DELETE `/api/resources/{id}` - Delete

---

## Security Requirements

### Critical Operations with Transactions
**ALWAYS** use `DB::transaction()` for:
1. **Order/Checkout operations**:
   - Creating orders with items
   - Updating inventory
   - Processing payments
   - Applying coupons

2. **Financial operations**:
   - Refunds
   - Credit adjustments
   - Payment processing

3. **Multi-table operations**:
   - Any operation that writes to multiple tables
   - Operations that must be all-or-nothing

### Validation Requirements
- **NEVER** trust user input
- **ALWAYS** use FormRequests for validation
- Validate foreign keys with `exists:table,column`
- Sanitize and validate file uploads
- Check authorization before sensitive operations

### Authorization Patterns
```php
// In Controller
public function show(int $id)
{
    $order = $this->orderService->getOrder($id);
    
    // Verify ownership
    if ($order->user_id !== auth()->id()) {
        abort(403, 'Unauthorized access to order');
    }
    
    return OrderResource::make($order);
}
```

### SQL Injection Prevention
- **ALWAYS** use Eloquent ORM or Query Builder
- **NEVER** use raw SQL with user input
- Use parameter binding if raw queries are necessary

---

## Business-Specific Rules

### Product Catalog
- Products are filtered by city availability through `availability_zones` table
- Use `availableInCity()` scope when querying products
- Cache catalog queries in Redis for performance

### Orders
- Order creation is atomic (use DB::transaction)
- Validate stock before order creation
- Automatically deduct stock on successful order
- Generate unique tracking numbers (format: `MS-XXXXXXXXXX`)
- Create order with OrderStatus 'pending' by default

### Reviews
- Reviews require approval (`is_approved = false` initially)
- Only approved reviews are shown publicly
- Use `approved()` scope when querying reviews

### Coupons
- Validate expiry date before application
- Check minimum purchase requirement
- Support percentage and fixed amount discounts
- Mark as used after successful order

---

## Testing Guidelines

### Test Structure
- Use Pest PHP for testing
- Feature tests in `tests/Feature/Api/`
- Unit tests in `tests/Unit/`
- Use factories for test data

**Example**:
```php
it('can create an order with valid data', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);
    
    $response = $this->actingAs($user)
        ->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ],
            'recipient_name' => 'John Doe',
            'delivery_date' => now()->addDays(2)->toDateString(),
        ]);
    
    $response->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'total_amount']]);
    
    expect($user->orders()->count())->toBe(1);
});
```

---

## Performance Considerations

### Eager Loading
- **ALWAYS** eager load relationships to prevent N+1 queries
- Use `with()` in Services and Repositories

```php
// Good
$orders = Order::with(['items.product', 'user', 'status'])->get();

// Bad - N+1 problem
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->user->name; // Separate query per order
}
```

### Caching
- Cache product catalog in Redis
- Cache categories and static data
- Use cache tags for easy invalidation

```php
Cache::remember("products.city.{$cityId}", 3600, function () use ($cityId) {
    return Product::active()->availableInCity($cityId)->get();
});
```

### Database Indexing
- Add indexes to frequently queried columns
- Index foreign keys
- Index columns used in WHERE clauses

---

## Common Patterns

### Service Injection
```php
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CouponService $couponService
    ) {}
}
```

### Query Scopes in Models
```php
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

public function scopeAvailableInCity($query, int $cityId)
{
    return $query->whereHas('availableCities', fn($q) => $q->where('city_id', $cityId));
}
```

### Eloquent Relationships
```php
// One to Many
public function items(): HasMany
{
    return $this->hasMany(OrderItem::class);
}

// Many to Many
public function categories(): BelongsToMany
{
    return $this->belongsToMany(Category::class, 'product_category');
}
```

---

## Documentation

### API Documentation
- Use L5-Swagger for OpenAPI documentation
- Add `@OA\` annotations to controllers
- Generate docs with `php artisan l5-swagger:generate`

### Code Comments
- Document complex business logic
- Explain non-obvious decisions
- Keep comments updated with code changes

---

## Deployment Checklist

Before deploying:
- [ ] Run migrations: `php artisan migrate`
- [ ] Seed essential data (states, cities, order statuses)
- [ ] Configure `.env` for production
- [ ] Enable cache: `php artisan config:cache`
- [ ] Enable route cache: `php artisan route:cache`
- [ ] Set up Redis connection
- [ ] Configure queue workers
- [ ] Set up CORS for frontend
- [ ] Generate API documentation
- [ ] Run tests: `php artisan test`
- [ ] Configure CDN for images
- [ ] Set up SSL certificate

---

## Key Takeaways

1. **Controllers are thin** - only HTTP handling
2. **Services contain business logic** - all calculations and rules
3. **DTOs transfer data** - immutable, type-safe
4. **FormRequests validate input** - mandatory for all endpoints
5. **DB::transaction() for critical operations** - especially checkout
6. **Type everything** - parameters, returns, properties
7. **Test everything** - use Pest for expressive tests
8. **Cache strategically** - especially product catalog
9. **Eager load relationships** - avoid N+1 queries
10. **Follow Laravel conventions** - consistency matters

---

**Version**: 1.0  
**Last Updated**: January 2026  
**Framework**: Laravel 12.x  
**PHP**: 8.2+
