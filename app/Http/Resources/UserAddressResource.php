<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // Destinatario
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,

            // Dirección desglosada
            'street' => $this->street,
            'ext_number' => $this->ext_number,
            'neighborhood' => $this->neighborhood,
            'zip_code' => $this->zip_code,

            // Ciudad y estado (desde relaciones eager-loaded)
            'city_name' => $this->whenLoaded('city', fn () => $this->city?->name),
            'state_name' => $this->whenLoaded('state', fn () => $this->state?->name),

            // Dirección completa formateada (calle + número + colonia + CP)
            'full_address' => $this->buildFullAddress(),
        ];
    }

    /**
     * Construye una línea de dirección legible combinando los campos disponibles.
     */
    protected function buildFullAddress(): string
    {
        $parts = array_filter([
            trim(implode(' ', array_filter([$this->street, $this->ext_number]))),
            $this->neighborhood,
            $this->zip_code ? 'C.P. ' . $this->zip_code : null,
        ]);

        return implode(', ', $parts);
    }
}
