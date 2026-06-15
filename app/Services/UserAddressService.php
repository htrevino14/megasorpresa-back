<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserAddress;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserAddressService
{
    /**
     * Elimina una dirección asegurándose de que pertenezca al usuario.
     *
     * firstOrFail() hace que Laravel devuelva 404 si no existe o no pertenece
     * al usuario autenticado.
     */
    public function deleteAddress(int $addressId, int $userId): void
    {
        DB::transaction(function () use ($addressId, $userId) {
            $address = UserAddress::where('id', $addressId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $address->delete();
        });
    }

    /**
     * Crea una direccion para el usuario autenticado.
     *
     * Guarda el telefono concatenando lada + numero en el campo `phone`.
     */
    public function createForUser(int $userId, array $data): UserAddress
    {
        return DB::transaction(function () use ($userId, $data) {
            $address = UserAddress::create([
                'user_id' => $userId,
                'recipient_name' => $data['recipient_name'],
                'phone' => trim((string) $data['phone_code'] . (string) $data['phone']),
                'street' => $data['street'],
                'ext_number' => $data['ext_number'],
                'neighborhood' => $data['neighborhood'],
                'dwelling_type' => $data['dwelling_type'],
                'city_id' => $data['city_id'],
                'state_id' => $data['state_id'],
                'zip_code' => $data['zip_code'],
                'references' => $data['references'] ?? null,
            ]);

            return $address->load(['city', 'state']);
        });
    }

    /**
     * Actualiza una dirección existente verificando que pertenezca al usuario.
     *
     * Lanza ModelNotFoundException si la dirección no existe o pertenece a otro usuario,
     * lo que Laravel convierte automáticamente en 404.
     */
    public function updateAddress(int $addressId, int $userId, array $data): UserAddress
    {
        $address = UserAddress::where('id', $addressId)
            ->where('user_id', $userId)
            ->firstOrFail();

        return DB::transaction(function () use ($address, $data) {
            $address->update([
                'recipient_name' => $data['recipient_name'],
                'phone'          => trim((string) $data['phone_code'] . (string) $data['phone']),
                'street'         => $data['street'],
                'ext_number'     => $data['ext_number'],
                'neighborhood'   => $data['neighborhood'],
                'dwelling_type'  => $data['dwelling_type'],
                'city_id'        => $data['city_id'],
                'state_id'       => $data['state_id'],
                'zip_code'       => $data['zip_code'],
                'references'     => $data['references'] ?? null,
            ]);

            return $address->load(['city', 'state']);
        });
    }

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
