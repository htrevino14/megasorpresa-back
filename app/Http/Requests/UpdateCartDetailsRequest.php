<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_zip_code' => 'nullable|string|max:10',
            'shipping_city_id' => 'nullable|integer|exists:cities,id',
            'scheduled_delivery_date' => 'nullable|date|after:today',
        ];
    }
}
