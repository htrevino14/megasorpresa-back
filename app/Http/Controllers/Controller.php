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
 */
abstract class Controller
{
    //
}
