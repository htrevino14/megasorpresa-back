<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($productId),
            ],
            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($productId),
            ],
            'base_price' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string',
            'stock_quantity' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'availability_zones' => 'nullable|array',
            'availability_zones.*' => 'integer|exists:cities,id',
        ];
    }
}
