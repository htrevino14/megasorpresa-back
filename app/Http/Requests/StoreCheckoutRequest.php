<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * Valida estrictamente el payload del checkout enviado por el frontend.
 *
 * El payload se agrupa por la tabla destino:
 *  - orders:         payment_method, subtotal, total
 *  - order_details:  delivery_date, delivery_slot_id, recipient_phone,
 *                    card_message, signature
 *  - user_addresses: street, ext_number, neighborhood, dwelling_type,
 *                    zip_code, state_id, city_id, references
 *
 * `user_id` y `created_at` los resuelve el backend automáticamente.
 */
class StoreCheckoutRequest extends FormRequest
{
    /**
     * Tipos de domicilio permitidos para user_addresses.dwelling_type.
     */
    private const DWELLING_TYPES = [
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
    ];

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // ── orders ──────────────────────────────────────────────────
            'payment_method' => ['required', 'string', 'max:255'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],

            // ── order_details ───────────────────────────────────────────
            'delivery_date' => ['required', 'date', 'after:today'],
            'delivery_slot_id' => ['required', 'integer', 'exists:delivery_slots,id'],
            'recipient_phone' => ['required', 'string', 'max:20'],
            'card_message' => ['nullable', 'string', 'max:500'],
            'signature' => ['nullable', 'string', 'max:100'],

            // ── user_addresses ──────────────────────────────────────────
            'street' => ['required', 'string', 'max:255'],
            'ext_number' => ['required', 'string', 'max:20'],
            'neighborhood' => ['required', 'string', 'max:255'],
            'dwelling_type' => ['required', 'string', Rule::in(self::DWELLING_TYPES)],
            'zip_code' => ['required', 'string', 'max:10'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'references' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            // orders
            'payment_method.required' => 'El método de pago es obligatorio.',
            'subtotal.required' => 'El subtotal es obligatorio.',
            'subtotal.numeric' => 'El subtotal debe ser un valor numérico.',
            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un valor numérico.',

            // order_details
            'delivery_date.required' => 'La fecha de envío es obligatoria.',
            'delivery_date.after' => 'La fecha de envío debe ser posterior a hoy.',
            'delivery_slot_id.required' => 'Debes seleccionar un horario de entrega.',
            'delivery_slot_id.exists' => 'El horario de entrega seleccionado no es válido.',
            'recipient_phone.required' => 'El teléfono del destinatario es obligatorio.',

            // user_addresses
            'street.required' => 'La calle es obligatoria.',
            'ext_number.required' => 'El número exterior es obligatorio.',
            'neighborhood.required' => 'La colonia es obligatoria.',
            'dwelling_type.required' => 'El tipo de domicilio es obligatorio.',
            'dwelling_type.in' => 'El tipo de domicilio seleccionado no es válido.',
            'zip_code.required' => 'El código postal es obligatorio.',
            'state_id.required' => 'El estado es obligatorio.',
            'state_id.exists' => 'El estado seleccionado no es válido.',
            'city_id.required' => 'La ciudad es obligatoria.',
            'city_id.exists' => 'La ciudad seleccionada no es válida.',
        ];
    }

    /**
     * Respuesta JSON estandarizada cuando la validación falla.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Los datos enviados no son válidos.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
