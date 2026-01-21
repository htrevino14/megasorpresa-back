<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'street' => 'required|string|max:255',
            'ext_number' => 'nullable|string|max:50',
            'neighborhood' => 'nullable|string|max:255',
            'city_id' => 'required|integer|exists:cities,id',
            'zip_code' => 'nullable|string|max:10',
            'references' => 'nullable|string|max:500',
        ];
    }
}
