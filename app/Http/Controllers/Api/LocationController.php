<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\DeliverySlotResource;
use App\Http\Resources\StateResource;
use App\Services\LocationService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocationController extends Controller
{
    public function __construct(
        private LocationService $locationService
    ) {}

    /**
     * Lista todos los estados disponibles.
     */
    public function states(): AnonymousResourceCollection
    {
        return StateResource::collection(
            $this->locationService->getStates()
        );
    }

    /**
     * Lista las ciudades activas de un estado.
     */
    public function cities(int $stateId): AnonymousResourceCollection
    {
        return CityResource::collection(
            $this->locationService->getCitiesByState($stateId)
        );
    }

    /**
     * Lista las franjas de entrega de una ciudad.
     */
    public function deliverySlots(int $cityId): AnonymousResourceCollection
    {
        return DeliverySlotResource::collection(
            $this->locationService->getDeliverySlotsByCity($cityId)
        );
    }
}
