<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserAddress;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserAddressService
{
    /**
     * Lista paginada de direcciones de un usuario, con búsqueda opcional.
     *
     * La búsqueda aplica sobre: recipient_name, phone, street, neighborhood
     * y zip_code para que el usuario pueda localizar una dirección rápidamente.
     *
     * @param  int         $userId   ID del usuario autenticado.
     * @param  int         $perPage  Direcciones por página (default 6).
     * @param  string|null $search   Cadena de búsqueda libre (opcional).
     */
    public function getUserAddresses(int $userId, int $perPage = 6, ?string $search = null): LengthAwarePaginator
    {
        return UserAddress::where('user_id', $userId)
            ->with(['city', 'state'])
            ->when($search, function ($query, string $term) {
                $like = '%' . $term . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('recipient_name', 'like', $like)
                      ->orWhere('phone', 'like', $like)
                      ->orWhere('street', 'like', $like)
                      ->orWhere('neighborhood', 'like', $like)
                      ->orWhere('zip_code', 'like', $like);
                });
            })
            ->latest()
            ->paginate($perPage);
    }
}
