<?php

namespace App\Http\Controllers\Api;

use App\DTOs\AddressDTO;
use App\DTOs\ReminderDTO;
use App\DTOs\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\StoreReminderRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\ReminderResource;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Registrar un nuevo usuario",
     *     description="Crea una nueva cuenta de usuario y retorna un token de autenticación. El usuario queda automáticamente autenticado tras el registro.",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe", maxLength=255),
     *             @OA\Property(property="email", type="string", format="email", example="newuser@example.com", maxLength=255),
     *             @OA\Property(property="password", type="string", format="password", example="password123", minLength=8, description="Contraseña (mínimo 8 caracteres)"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123", description="Confirmación de contraseña"),
     *             @OA\Property(property="first_name", type="string", example="John", maxLength=255, description="Nombre (opcional)"),
     *             @OA\Property(property="last_name", type="string", example="Doe", maxLength=255, description="Apellido (opcional)"),
     *             @OA\Property(property="phone", type="string", example="+1234567890", maxLength=20, description="Teléfono (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario registrado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string", example="1|abcdef123456..."),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos de validación inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $userDTO = UserDTO::fromRequest($request);
        $user = $this->userService->register($userDTO);

        // Generate token for the new user
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/user/profile",
     *     summary="Actualizar perfil de usuario",
     *     description="Actualiza la información del perfil del usuario autenticado. Todos los campos son opcionales.",
     *     tags={"Perfil de Usuario"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe Updated", maxLength=255),
     *             @OA\Property(property="email", type="string", format="email", example="newemail@example.com", maxLength=255, description="Email (debe ser único)"),
     *             @OA\Property(property="first_name", type="string", example="John", maxLength=255),
     *             @OA\Property(property="last_name", type="string", example="Doe", maxLength=255),
     *             @OA\Property(property="phone", type="string", example="+1234567890", maxLength=20)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Perfil actualizado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos de validación inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->userService->updateProfile(
            $request->user(),
            $request->validated()
        );

        return new UserResource($user);
    }

    /**
     * @OA\Get(
     *     path="/api/user/addresses",
     *     summary="Listar direcciones del usuario",
     *     description="Obtiene todas las direcciones guardadas del usuario autenticado.",
     *     tags={"Perfil de Usuario"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de direcciones obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="street", type="string", example="Av. Principal 123"),
     *                     @OA\Property(property="ext_number", type="string", example="123A"),
     *                     @OA\Property(property="neighborhood", type="string", example="Centro"),
     *                     @OA\Property(property="city_id", type="integer", example=1),
     *                     @OA\Property(property="zip_code", type="string", example="12345"),
     *                     @OA\Property(property="references", type="string", example="Casa blanca con portón negro")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     )
     * )
     */
    public function addresses()
    {
        $addresses = $this->userService->getUserAddresses(auth()->id());

        return AddressResource::collection($addresses);
    }

    /**
     * @OA\Post(
     *     path="/api/user/addresses",
     *     summary="Agregar una nueva dirección",
     *     description="Crea una nueva dirección de entrega para el usuario autenticado.",
     *     tags={"Perfil de Usuario"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"street","city_id"},
     *             @OA\Property(property="street", type="string", example="Av. Principal 123", maxLength=255),
     *             @OA\Property(property="ext_number", type="string", example="123A", maxLength=50, description="Número exterior (opcional)"),
     *             @OA\Property(property="neighborhood", type="string", example="Centro", maxLength=255, description="Colonia (opcional)"),
     *             @OA\Property(property="city_id", type="integer", example=1, description="ID de la ciudad"),
     *             @OA\Property(property="zip_code", type="string", example="12345", maxLength=10, description="Código postal (opcional)"),
     *             @OA\Property(property="references", type="string", example="Casa blanca con portón negro", maxLength=500, description="Referencias adicionales (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dirección creada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="street", type="string", example="Av. Principal 123"),
     *                 @OA\Property(property="ext_number", type="string", example="123A"),
     *                 @OA\Property(property="city_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos de validación inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function storeAddress(StoreAddressRequest $request)
    {
        $addressDTO = AddressDTO::fromRequest($request);
        $address = $this->userService->addAddress($addressDTO);

        return (new AddressResource($address))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/user/reminders",
     *     summary="Listar recordatorios del usuario",
     *     description="Obtiene todos los recordatorios de eventos especiales del usuario autenticado.",
     *     tags={"Perfil de Usuario"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de recordatorios obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="event_name", type="string", example="Cumpleaños de María"),
     *                     @OA\Property(property="date", type="string", format="date", example="2026-06-15"),
     *                     @OA\Property(property="notify_days_before", type="integer", example=7)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     )
     * )
     */
    public function reminders()
    {
        $reminders = $this->userService->getUserReminders(auth()->id());

        return ReminderResource::collection($reminders);
    }

    /**
     * @OA\Post(
     *     path="/api/user/reminders",
     *     summary="Crear un nuevo recordatorio",
     *     description="Crea un nuevo recordatorio de evento especial para el usuario autenticado. Útil para recordar cumpleaños u otros eventos importantes.",
     *     tags={"Perfil de Usuario"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"event_name","date"},
     *             @OA\Property(property="event_name", type="string", example="Cumpleaños de María", maxLength=255, description="Nombre del evento"),
     *             @OA\Property(property="date", type="string", format="date", example="2026-06-15", description="Fecha del evento (debe ser posterior a hoy)"),
     *             @OA\Property(property="notify_days_before", type="integer", example=7, minimum=1, maximum=365, description="Días antes del evento para recibir notificación (opcional, por defecto 7)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Recordatorio creado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="event_name", type="string", example="Cumpleaños de María"),
     *                 @OA\Property(property="date", type="string", format="date", example="2026-06-15")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos de validación inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function storeReminder(StoreReminderRequest $request)
    {
        $reminderDTO = ReminderDTO::fromRequest($request);
        $reminder = $this->userService->createReminder($reminderDTO);

        return (new ReminderResource($reminder))
            ->response()
            ->setStatusCode(201);
    }
}
