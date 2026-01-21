# MegaSorpresa API Endpoints

## Authentication

### Login
- **POST** `/api/auth/login`
- Body: `{ "email", "password", "device_name" (optional) }`
- Response: `{ "token", "user" }`

### Register
- **POST** `/api/auth/register`
- Body: `{ "name", "email", "password", "password_confirmation", "first_name", "last_name", "phone" }`
- Response: `{ "token", "user" }`

### Logout (Protected)
- **POST** `/api/auth/logout`
- Headers: `Authorization: Bearer {token}`
- Response: `{ "message": "Token revoked successfully" }`

### Get Current User (Protected)
- **GET** `/api/user`
- Headers: `Authorization: Bearer {token}`
- Response: User object

## Catalog (Public)

### List Products
- **GET** `/api/catalog/products`
- Query params: `city_id`, `category_id`, `search`, `per_page`
- Response: Paginated products

### Get Featured Products
- **GET** `/api/catalog/products/featured`
- Query params: `city_id`, `limit`
- Response: Array of featured products

### Get Product Details
- **GET** `/api/catalog/products/{id}`
- Response: Product details with images, categories, reviews

### List Categories
- **GET** `/api/catalog/categories`
- Response: Array of categories with children

### Get Category with Products
- **GET** `/api/catalog/categories/{id}`
- Query params: `city_id`, `per_page`
- Response: `{ "category", "products" }`

## Orders (Protected)

### List User Orders
- **GET** `/api/orders`
- Headers: `Authorization: Bearer {token}`
- Response: Paginated orders

### Create Order
- **POST** `/api/orders`
- Headers: `Authorization: Bearer {token}`
- Body: `{ "items": [{"product_id", "quantity"}], "recipient_name", "recipient_phone", "delivery_date", "delivery_slot_id", "card_message", "coupon_code", "payment_method" }`
- Response: Created order

### Get Order Details
- **GET** `/api/orders/{id}`
- Headers: `Authorization: Bearer {token}`
- Response: Order details

## User Management (Protected)

### Update Profile
- **PUT** `/api/user/profile`
- Headers: `Authorization: Bearer {token}`
- Body: `{ "name", "email", "first_name", "last_name", "phone" }`
- Response: Updated user

### List Addresses
- **GET** `/api/user/addresses`
- Headers: `Authorization: Bearer {token}`
- Response: Array of addresses

### Create Address
- **POST** `/api/user/addresses`
- Headers: `Authorization: Bearer {token}`
- Body: `{ "street", "ext_number", "neighborhood", "city_id", "zip_code", "references" }`
- Response: Created address

### List Reminders
- **GET** `/api/user/reminders`
- Headers: `Authorization: Bearer {token}`
- Response: Array of reminders

### Create Reminder
- **POST** `/api/user/reminders`
- Headers: `Authorization: Bearer {token}`
- Body: `{ "event_name", "date", "notify_days_before" }`
- Response: Created reminder

## Reviews

### List Product Reviews (Public)
- **GET** `/api/reviews`
- Query params: `product_id` (required)
- Response: Paginated reviews

### Get Average Rating (Public)
- **GET** `/api/reviews/average`
- Query params: `product_id` (required)
- Response: `{ "product_id", "average_rating" }`

### Create Review (Protected)
- **POST** `/api/reviews`
- Headers: `Authorization: Bearer {token}`
- Body: `{ "product_id", "rating", "comment" }`
- Response: Created review (pending approval)

## Coupons (Protected)

### Validate Coupon
- **POST** `/api/coupons/validate`
- Headers: `Authorization: Bearer {token}`
- Body: `{ "code", "subtotal" }`
- Response: `{ "valid", "message", "discount", "coupon" }`

## Key Features

### Business Logic Highlights

1. **City-Based Product Availability**: Products are filtered by availability zones (cities)
2. **Atomic Order Creation**: Orders use DB transactions with automatic stock updates
3. **Coupon Validation**: Validates expiry, minimum purchase, and calculates discounts
4. **Stock Management**: Automatic stock deduction on order creation
5. **Review Moderation**: Reviews require approval before being public
6. **Shipping Cost Calculation**: Based on delivery slot selection

### Architecture

- **Models**: Eloquent with relationships and scopes
- **DTOs**: Readonly PHP 8.2+ properties for type safety
- **Form Requests**: Comprehensive validation with `exists` rules
- **Services**: Business logic with transaction support
- **Resources**: Standardized JSON responses
- **Controllers**: Thin controllers delegating to services
