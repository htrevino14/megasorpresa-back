<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class ReminderDTO
{
    public function __construct(
        public int $user_id,
        public string $event_name,
        public string $date,
        public int $notify_days_before = 7,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            user_id: $request->user()->id,
            event_name: $request->input('event_name'),
            date: $request->input('date'),
            notify_days_before: (int) $request->input('notify_days_before', 7),
        );
    }
}
