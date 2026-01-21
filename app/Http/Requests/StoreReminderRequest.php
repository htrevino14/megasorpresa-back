<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_name' => 'required|string|max:255',
            'date' => 'required|date|after:now',
            'notify_days_before' => 'sometimes|integer|min:1|max:365',
        ];
    }
}
