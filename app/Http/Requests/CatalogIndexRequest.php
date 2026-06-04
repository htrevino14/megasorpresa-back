<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'city_id' => 'required|integer|exists:cities,id',
            'category' => 'nullable|string|exists:categories,slug',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'city_id.required' => 'El parámetro city_id es obligatorio.',
            'city_id.exists' => 'La ciudad especificada no existe.',
            'category.exists' => 'La categoría especificada no existe.',
        ];
    }
}
