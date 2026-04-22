# Shopping Cart API Documentation

## Overview

The Shopping Cart system allows both guest users and authenticated users to manage their shopping cart. The cart persists using session IDs for guests and user IDs for authenticated users.

## Key Features

- **Guest Cart Support**: Guests can add products to their cart using session-based persistence
- **User Cart Support**: Authenticated users have persistent carts tied to their account
- **Cart Merging**: When a guest user logs in, their guest cart is automatically merged with their user cart
- **Stock Validation**: Prevents adding more items than available in stock
- **Delivery Information**: Supports shipping details including zip code, city, and scheduled delivery date
- **Atomic Operations**: All cart operations use database transactions for data consistency

## Endpoints

### 1. Get Current Cart
**GET** `/api/cart`

Returns the current cart for the user (guest or authenticated).

**Response:**
```json
{
  "data": {
    "id": 1,
    "session_id": "abc123...",
    "shipping_zip_code": "12345",
    "shipping_city": {
      "id": 1,
      "name": "Ciudad de México"
    },
    "scheduled_delivery_date": "2026-05-15",
    "items": [
      {
        "id": 1,
        "product": {
          "id": 10,
          "name": "LEGO Star Wars Set",
          "base_price": 499.99
        },
        "quantity": 2,
        "price_at_addition": 499.99,
        "subtotal": 999.98
      }
    ],
    "subtotal": 999.98,
    "total_items": 2,
    "created_at": "2026-04-22T10:30:00.000000Z",
    "updated_at": "2026-04-22T10:35:00.000000Z"
  }
}
```

---

### 2. Add Product to Cart
**POST** `/api/cart/add`

Adds a product to the cart. If the product already exists, increments the quantity.

**Request Body:**
```json
{
  "product_id": 10,
  "quantity": 2
}
```

**Validations:**
- `product_id`: required, integer, must exist in products table
- `quantity`: required, integer, minimum 1

**Success Response (200):**
```json
{
  "message": "Product added to cart successfully",
  "data": {
    "id": 1,
    "items": [...],
    "subtotal": 999.98,
    "total_items": 2
  }
}
```

**Error Response (422):**
```json
{
  "message": "Failed to add product to cart",
  "error": "Insufficient stock for product: LEGO Star Wars Set. Available: 5"
}
```

**Business Rules:**
- Validates product exists and is active
- Checks stock availability
- If product already in cart, increments quantity
- If new product, adds as new cart item
- Stores current product price at time of addition

---

### 3. Update Item Quantity
**PATCH** `/api/cart/update-quantity`

Updates the quantity of a specific product in the cart.

**Request Body:**
```json
{
  "product_id": 10,
  "quantity": 5
}
```

**Validations:**
- `product_id`: required, integer, must exist in products table
- `quantity`: required, integer, minimum 1

**Success Response (200):**
```json
{
  "message": "Cart item quantity updated successfully",
  "data": {
    "id": 1,
    "items": [...],
    "subtotal": 2499.95,
    "total_items": 5
  }
}
```

**Error Response (422):**
```json
{
  "message": "Failed to update cart item quantity",
  "error": "Insufficient stock for product"
}
```

---

### 4. Remove Product from Cart
**DELETE** `/api/cart/remove/{productId}`

Removes a specific product from the cart completely.

**Parameters:**
- `productId` (path): ID of the product to remove

**Success Response (200):**
```json
{
  "message": "Product removed from cart successfully",
  "data": {
    "id": 1,
    "items": [],
    "subtotal": 0,
    "total_items": 0
  }
}
```

---

### 5. Update Delivery Details
**POST** `/api/cart/details`

Updates shipping and delivery information for the cart.

**Request Body:**
```json
{
  "shipping_zip_code": "12345",
  "shipping_city_id": 1,
  "scheduled_delivery_date": "2026-05-15"
}
```

**Validations:**
- `shipping_zip_code`: nullable, string, max 10 characters
- `shipping_city_id`: nullable, integer, must exist in cities table
- `scheduled_delivery_date`: nullable, date, must be after today

**Success Response (200):**
```json
{
  "message": "Cart details updated successfully",
  "data": {
    "id": 1,
    "shipping_zip_code": "12345",
    "shipping_city": {
      "id": 1,
      "name": "Ciudad de México"
    },
    "scheduled_delivery_date": "2026-05-15",
    "items": [...]
  }
}
```

**Error Response (422):**
```json
{
  "message": "Failed to update cart details",
  "error": "Delivery date must be in the future"
}
```

---

### 6. Clear Cart
**DELETE** `/api/cart/clear`

Removes all items from the cart.

**Success Response (200):**
```json
{
  "message": "Cart cleared successfully",
  "data": {
    "id": 1,
    "items": [],
    "subtotal": 0,
    "total_items": 0
  }
}
```

---

## Cart Merging on Login

When a guest user logs in, their guest cart is automatically merged with their user cart:

1. If the user has no existing cart, the guest cart is assigned to them
2. If the user has an existing cart:
   - Items from guest cart are merged into user cart
   - For duplicate products, quantities are combined (respecting stock limits)
   - Guest cart is deleted after merge
3. Session ID is updated to maintain cart continuity

**Example Login Flow:**

```bash
# 1. Guest adds products to cart
POST /api/cart/add
{
  "product_id": 10,
  "quantity": 2
}

# 2. User logs in
POST /api/auth/login
{
  "email": "user@example.com",
  "password": "password123"
}

# 3. Cart is automatically merged
# User's cart now contains both their previous items and guest cart items

# 4. Get merged cart
GET /api/cart
```

---

## Database Schema

### carts table
```sql
id                      BIGINT UNSIGNED PRIMARY KEY
user_id                 BIGINT UNSIGNED NULLABLE (FK to users)
session_id              VARCHAR(255) UNIQUE
shipping_zip_code       VARCHAR(255) NULLABLE
shipping_city_id        BIGINT UNSIGNED NULLABLE (FK to cities)
scheduled_delivery_date DATE NULLABLE
created_at              TIMESTAMP
updated_at              TIMESTAMP

INDEX: session_id, user_id
```

### cart_items table
```sql
id                BIGINT UNSIGNED PRIMARY KEY
cart_id           BIGINT UNSIGNED (FK to carts, CASCADE DELETE)
product_id        BIGINT UNSIGNED (FK to products, CASCADE DELETE)
quantity          INTEGER DEFAULT 1
price_at_addition DECIMAL(10,2)
created_at        TIMESTAMP
updated_at        TIMESTAMP

UNIQUE: (cart_id, product_id)
INDEX: cart_id, product_id
```

---

## Error Handling

All cart endpoints follow consistent error handling:

### Validation Errors (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "product_id": ["The product id field is required."],
    "quantity": ["The quantity must be at least 1."]
  }
}
```

### Business Logic Errors (422)
```json
{
  "message": "Failed to add product to cart",
  "error": "Insufficient stock for product: Product Name"
}
```

### Not Found Errors (404)
```json
{
  "message": "Product not found in cart"
}
```

---

## Testing

The cart system includes comprehensive tests covering:

- Guest cart creation
- Adding products to cart
- Incrementing quantities for existing products
- Stock validation
- Updating item quantities
- Removing items
- Updating delivery details
- Clearing cart
- Session-based cart isolation

Run tests:
```bash
./vendor/bin/sail pest tests/Feature/Api/CartTest.php
```

---

## Best Practices

1. **Always validate stock** before adding/updating cart items
2. **Use transactions** for all cart operations to ensure data consistency
3. **Store price at addition** to protect against price changes between cart and checkout
4. **Merge carts on login** to provide seamless user experience
5. **Clean up abandoned carts** periodically (recommended: carts older than 30 days with no user_id)

---

## Integration with Checkout

When a user proceeds to checkout, use the cart data to create an order:

```php
// 1. Get cart with all items
$cart = $cartService->getOrCreateCart($sessionId, $userId);

// 2. Validate all items are still in stock
foreach ($cart->items as $item) {
    if ($item->product->stock_quantity < $item->quantity) {
        throw new Exception("Product out of stock");
    }
}

// 3. Create order from cart
$orderDTO = OrderDTO::fromCart($cart);
$order = $orderService->createOrder($orderDTO);

// 4. Clear cart after successful order
$cartService->clearCart($cart);
```

---

## Redis Caching (Future Enhancement)

For high-traffic scenarios, consider caching cart data in Redis:

```php
// Cache cart for 1 hour
Cache::put("cart:{$sessionId}", $cart, 3600);

// Retrieve from cache
$cart = Cache::get("cart:{$sessionId}");
```

This reduces database load during heavy browsing sessions.

---

## API Rate Limiting

Cart endpoints are subject to standard rate limiting:
- **Guest users**: 60 requests per minute per IP
- **Authenticated users**: 120 requests per minute

Configure in `config/sanctum.php` and `app/Http/Kernel.php`.
