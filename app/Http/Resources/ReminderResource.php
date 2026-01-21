<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReminderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_name' => $this->event_name,
            'date' => $this->date->format('Y-m-d'),
            'notify_days_before' => $this->notify_days_before,
            'notification_date' => $this->notification_date->format('Y-m-d'),
        ];
    }
}
