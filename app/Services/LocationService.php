<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\City;
use App\Models\DeliverySlot;
use App\Models\State;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{
    /**
     * Obtiene todos los estados ordenados alfabéticamente.
     */
    public function getStates(): Collection
    {
        return State::orderBy('name')->get();
    }

    /**
     * Obtiene las ciudades activas de un estado, ordenadas alfabéticamente.
     */
    public function getCitiesByState(int $stateId): Collection
    {
        return City::where('state_id', $stateId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Obtiene las franjas de entrega disponibles para una ciudad.
     */
    public function getDeliverySlotsByCity(int $cityId): Collection
    {
        return DeliverySlot::where('city_id', $cityId)
            ->orderBy('start_time')
            ->get();
    }
}
