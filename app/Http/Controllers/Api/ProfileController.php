<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Retorna la información del usuario autenticado para la vista "Mi Perfil".
     */
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Actualiza la información de perfil del usuario autenticado.
     */
    public function update(UpdateProfileRequest $request): UserResource
    {
        $user = $this->userService->updateProfile(
            $request->user(),
            $request->validated(),
        );

        return new UserResource($user);
    }
}
