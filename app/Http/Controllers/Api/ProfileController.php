<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
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
        $validated = $request->validated();
        $user = $request->user();

        $firstName = trim((string) $validated['first_name']);
        $lastName = trim((string) $validated['last_name']);

        $user->update([
            'name' => trim("{$firstName} {$lastName}"),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'gender' => (string) $validated['gender'],
            'phone' => trim((string) $validated['phone_code']) . trim((string) $validated['phone']),
        ]);

        return new UserResource($user->fresh());
    }
}
