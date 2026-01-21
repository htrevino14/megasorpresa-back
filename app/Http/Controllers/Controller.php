<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="MegaSorpresa API",
 *     version="1.0.0",
 *     description="API Backend para el e-commerce de juguetes MegaSorpresa. Sirve a clientes Web (SPA), Android e iOS.",
 *     @OA\Contact(
 *         email="api@megasorpresa.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/",
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token",
 *     description="Token de autenticación de Laravel Sanctum para clientes móviles"
 * )
 * 
 * @OA\Tag(
 *     name="Autenticación",
 *     description="Endpoints de autenticación para clientes móviles y web"
 * )
 * 
 * @OA\Tag(
 *     name="Catálogo",
 *     description="Endpoints para gestión de productos y categorías"
 * )
 * 
 * @OA\Tag(
 *     name="Órdenes",
 *     description="Endpoints para gestión de órdenes de compra (Checkout)"
 * )
 * 
 * @OA\Tag(
 *     name="Perfil de Usuario",
 *     description="Endpoints para gestión del perfil, direcciones y recordatorios"
 * )
 * 
 * @OA\Tag(
 *     name="Reseñas",
 *     description="Endpoints para gestión de reseñas de productos"
 * )
 * 
 * @OA\Tag(
 *     name="Cupones",
 *     description="Endpoints para validación de cupones de descuento"
 * )
 * 
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Modelo de producto",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Muñeca Barbie"),
 *     @OA\Property(property="slug", type="string", example="muneca-barbie"),
 *     @OA\Property(property="sku", type="string", example="TOY-001"),
 *     @OA\Property(property="base_price", type="number", format="float", example=29.99),
 *     @OA\Property(property="description", type="string", example="Muñeca Barbie original con accesorios"),
 *     @OA\Property(property="stock_quantity", type="integer", example=50),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="primary_image", type="string", example="https://example.com/images/product.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     description="Modelo de categoría",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Muñecas"),
 *     @OA\Property(property="slug", type="string", example="munecas"),
 *     @OA\Property(property="description", type="string", example="Muñecas y accesorios"),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 * 
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order",
 *     description="Modelo de orden",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="tracking_number", type="string", example="MS-0000000001"),
 *     @OA\Property(
 *         property="status",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="pending")
 *     ),
 *     @OA\Property(property="total_amount", type="number", format="float", example=99.99),
 *     @OA\Property(property="shipping_cost", type="number", format="float", example=5.99),
 *     @OA\Property(property="payment_method", type="string", example="card"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="Modelo de usuario",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="loyalty_points", type="integer", example=100),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     title="Validation Error",
 *     description="Error de validación (422)",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\AdditionalProperties(
 *             type="array",
 *             @OA\Items(type="string")
 *         ),
 *         example={"email": {"The email field is required."}}
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="UnauthorizedError",
 *     type="object",
 *     title="Unauthorized Error",
 *     description="Error de autenticación (401)",
 *     @OA\Property(property="message", type="string", example="Unauthenticated.")
 * )
 * 
 * @OA\Schema(
 *     schema="NotFoundError",
 *     type="object",
 *     title="Not Found Error",
 *     description="Recurso no encontrado (404)",
 *     @OA\Property(property="message", type="string", example="Resource not found.")
 * )
 * 
 * @OA\Schema(
 *     schema="ForbiddenError",
 *     type="object",
 *     title="Forbidden Error",
 *     description="Acceso prohibido (403)",
 *     @OA\Property(property="message", type="string", example="Unauthorized access.")
 * )
 */
abstract class Controller
{
    //
}
