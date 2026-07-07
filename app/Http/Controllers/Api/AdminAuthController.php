<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class AdminAuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/cms/login",
     *     summary="Login de administrador (CMS)",
     *     description="Autentica a un administrador contra la tabla admins. Si las credenciales son válidas y la cuenta está activa, retorna un Bearer Token de Sanctum.",
     *     tags={"CMS - Autenticación"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="admin@megasorpresa.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="device_name", type="string", example="cms-web", description="Nombre del token (opcional)")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="token", type="string", example="1|abcdef123456..."),
     *             @OA\Property(
     *                 property="admin",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Admin User"),
     *                 @OA\Property(property="email", type="string", example="admin@megasorpresa.com"),
     *                 @OA\Property(property="role", type="string", example="editor")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Credenciales inválidas"),
     *     @OA\Response(response=403, description="Cuenta desactivada")
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.',
            ], 401);
        }

        if (! $admin->is_active) {
            return response()->json([
                'message' => 'Esta cuenta de administrador está desactivada.',
            ], 403);
        }

        $tokenName = $request->input('device_name', 'cms-token');
        $token = $admin->createToken($tokenName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => $admin->role,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/cms/logout",
     *     summary="Logout de administrador (CMS)",
     *     description="Revoca el token de acceso actual del administrador autenticado.",
     *     tags={"CMS - Autenticación"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logout exitoso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Sesión cerrada correctamente.")
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/cms/dashboard",
     *     summary="Dashboard del administrador (CMS)",
     *     description="Retorna la información del administrador autenticado. Ruta protegida de ejemplo para el panel de gestión.",
     *     tags={"CMS - Autenticación"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Administrador autenticado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Bienvenido al panel de administración."),
     *             @OA\Property(
     *                 property="admin",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Admin User"),
     *                 @OA\Property(property="email", type="string", example="admin@megasorpresa.com"),
     *                 @OA\Property(property="role", type="string", example="editor")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function dashboard(Request $request): JsonResponse
    {
        $admin = $request->user();

        return response()->json([
            'message' => 'Bienvenido al panel de administración.',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => $admin->role,
            ],
        ]);
    }
}
