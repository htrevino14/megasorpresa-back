<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserAddressResource;
use App\Services\UserAddressService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserAddressController extends Controller
{
    public function __construct(
        private UserAddressService $addressService
    ) {}

    /**
     * Lista paginada de las direcciones de envío del usuario autenticado.
     *
     * Query params opcionales:
     *  - `search`   string  Filtra por nombre, teléfono, calle, colonia o CP.
     *  - `per_page` int     Resultados por página (default 6, máx 50).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = max(1, min((int) $request->integer('per_page', 6), 50));
        $search = $request->string('search')->toString() ?: null;

        $addresses = $this->addressService->getUserAddresses(
            userId: (int) auth()->id(),
            perPage: $perPage,
            search: $search,
        );

        return UserAddressResource::collection($addresses);
    }
}
