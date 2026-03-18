<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeNewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_slugs' => 'required|array|min:1',
            'category_slugs.*' => 'required|string|exists:newsletter_categories,slug',
        ];
    }
}
