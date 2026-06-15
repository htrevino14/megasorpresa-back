<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_name' => 'required|string|max:255',
            'phone_code'     => ['required', 'string', 'max:6', 'regex:/^\+[0-9]{1,5}$/'],
            'phone'          => 'required|string|max:20',
            'address_search' => 'nullable|string|max:255',
            'street'         => 'required|string|max:255',
            'ext_number'     => 'required|string|max:50',
            'neighborhood'   => 'required|string|max:255',
            'dwelling_type'  => [
                'required',
                Rule::in([
                    'casa',
                    'hotel',
                    'restaurante',
                    'escuela',
                    'oficina',
                    'hospital',
                    'teatro',
                    'plaza comercial',
                    'departamento',
                    'otro',
                ]),
            ],
            'zip_code' => 'required|string|max:10',
            'state_id' => 'required|integer|exists:states,id',
            'city_id'  => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(
                    fn ($query) => $query->where('state_id', $this->integer('state_id'))
                ),
            ],
            'references' => 'nullable|string|max:1000',
        ];
    }
}
