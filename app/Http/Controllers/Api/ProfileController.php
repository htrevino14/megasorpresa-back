<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}
