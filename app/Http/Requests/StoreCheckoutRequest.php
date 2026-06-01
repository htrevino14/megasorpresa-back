<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Valida el payload anidado del checkout enviado por el frontend.
 *
 * Estructura esperada:
 * {
 *   "phone": "+52 81 1234 5678",
 *   "recipient": { "recipient_name", "phone", "street", "ext_number",
 *                  "interior_number", "neighborhood", "zip_code",
 *                  "city_id", "references" },
 *   "schedule":  { "delivery_date", "delivery_slot_id" },
 *   "dedication":{ "message", "signature" },
 *   "payment":   { "payment_method_id", "accepted_terms" }
 * }
 */
class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // ── Paso 1 ─ Teléfono del comprador ─────────────────────────
            'phone' => ['required', 'string', 'max:20'],

            // ── Paso 2 ─ Destinatario y dirección ───────────────────────
            'recipient' => ['required', 'array'],
            'recipient.recipient_name' => ['required', 'string', 'max:255'],
            'recipient.phone' => ['nullable', 'string', 'max:20'],
            'recipient.street' => ['required', 'string', 'max:255'],
            'recipient.ext_number' => ['required', 'string', 'max:20'],
            'recipient.interior_number' => ['nullable', 'string', 'max:20'],
            'recipient.neighborhood' => ['required', 'string', 'max:255'],
            'recipient.zip_code' => ['required', 'string', 'max:10'],
            'recipient.city_id' => ['required', 'integer', 'exists:cities,id'],
            'recipient.references' => ['nullable', 'string', 'max:500'],

            // ── Paso 3 ─ Programación de entrega ────────────────────────
            'schedule' => ['required', 'array'],
            'schedule.delivery_date' => ['required', 'date', 'after:today'],
            'schedule.delivery_slot_id' => ['required', 'integer', 'exists:delivery_slots,id'],

            // ── Paso 4 ─ Dedicatoria (opcional) ─────────────────────────
            'dedication' => ['nullable', 'array'],
            'dedication.message' => ['nullable', 'string', 'max:500'],
            'dedication.signature' => ['nullable', 'string', 'max:100'],

            // ── Paso 5 ─ Pago ───────────────────────────────────────────
            'payment' => ['required', 'array'],
            'payment.payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'payment.accepted_terms' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'El teléfono del comprador es obligatorio.',
            'recipient.recipient_name.required' => 'El nombre del destinatario es obligatorio.',
            'recipient.street.required' => 'La calle es obligatoria.',
            'recipient.ext_number.required' => 'El número exterior es obligatorio.',
            'recipient.neighborhood.required' => 'La colonia es obligatoria.',
            'recipient.zip_code.required' => 'El código postal es obligatorio.',
            'recipient.city_id.required' => 'La ciudad es obligatoria.',
            'recipient.city_id.exists' => 'La ciudad seleccionada no es válida.',
            'schedule.delivery_date.required' => 'La fecha de envío es obligatoria.',
            'schedule.delivery_date.after' => 'La fecha de envío debe ser posterior a hoy.',
            'schedule.delivery_slot_id.required' => 'Debes seleccionar un horario de entrega.',
            'schedule.delivery_slot_id.exists' => 'El horario de entrega seleccionado no es válido.',
            'payment.payment_method_id.required' => 'Debes seleccionar un método de pago.',
            'payment.payment_method_id.exists' => 'El método de pago seleccionado no es válido.',
            'payment.accepted_terms.accepted' => 'Debes aceptar los términos y condiciones.',
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
