# MegaSorpresa API Documentation

This directory contains the OpenAPI 3.0 specification for the MegaSorpresa API.

## Files

- **api-docs.json** - OpenAPI specification in JSON format
- **api-docs.yaml** - OpenAPI specification in YAML format  
- **api-spec.yaml** - Copy of api-docs.yaml (as requested in the issue)

## Viewing the Documentation

### Option 1: Swagger UI (Built-in)

The API documentation is available through the built-in Swagger UI interface:

```
http://your-domain/api/documentation
```

### Option 2: Import into Postman

1. Open Postman
2. Go to File > Import
3. Select the `api-docs.json` or `api-docs.yaml` file
4. All endpoints will be imported as a Postman collection

### Option 3: Online Swagger Editor

1. Visit https://editor.swagger.io/
2. Copy the contents of `api-docs.yaml`
3. Paste into the editor to view and test

## Regenerating Documentation

After making changes to controller annotations, regenerate the documentation:

```bash
php artisan l5-swagger:generate
```

Or use the custom command:

```bash
php artisan openapi:generate
```

## API Overview

### Authentication

The API uses Laravel Sanctum with Bearer token authentication:

```
Authorization: Bearer {your-token-here}
```

### Modules Documented

1. **Autenticación** (4 endpoints)
   - Login
   - Logout  
   - Register
   - Get authenticated user

2. **Catálogo** (5 endpoints)
   - List products (with filters)
   - Featured products
   - Product details
   - List categories
   - Category with products

3. **Órdenes** (3 endpoints)
   - List user orders
   - Create order (checkout)
   - Order details

4. **Perfil de Usuario** (6 endpoints)
   - Update profile
   - List/create addresses
   - List/create reminders

5. **Reseñas** (3 endpoints)
   - List product reviews
   - Create review
   - Average rating

6. **Cupones** (1 endpoint)
   - Validate coupon

### Total Endpoints: 22

## Error Responses

All endpoints document standard error responses:

- **401** - Unauthorized (authentication required)
- **403** - Forbidden (insufficient permissions)
- **404** - Not Found
- **422** - Validation Error

## Schemas

Reusable component schemas are defined for:

- Product
- Category
- Order
- User
- ValidationError
- UnauthorizedError
- NotFoundError
- ForbiddenError

## Specification Version

- **OpenAPI**: 3.0.0
- **API Version**: 1.0.0
- **L5-Swagger**: 8.6.5
- **Swagger-PHP**: 4.11.1
