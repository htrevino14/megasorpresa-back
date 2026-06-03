<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliverySlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'city_id' => $this->city_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'label' => $this->formatLabel(),
            'additional_cost' => $this->additional_cost,
        ];
    }

    /**
     * Build a human friendly time range, e.g. "09:00 – 12:00".
     */
    private function formatLabel(): string
    {
        $start = $this->formatTime($this->start_time);
        $end = $this->formatTime($this->end_time);

        return trim("{$start} – {$end}", ' –');
    }

    private function formatTime(?string $time): string
    {
        if (! $time) {
            return '';
        }

        // Acepta "HH:MM:SS" o "HH:MM" y devuelve "HH:MM".
        return substr($time, 0, 5);
    }
}
