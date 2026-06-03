<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

/**
 * Datos validados del proceso de checkout (payload plano).
 *
 * Inmutable; se construye desde el `StoreCheckoutRequest` ya validado.
 * Los campos se agrupan lógicamente por su tabla destino:
 *  - orders:         payment_method, subtotal, total
 *  - order_details:  delivery_date, delivery_slot_id, recipient_phone,
 *                    card_message, signature
 *  - user_addresses: street, ext_number, neighborhood, dwelling_type,
 *                    zip_code, state_id, city_id, references
 */
readonly class CheckoutDTO
{
    public function __construct(
        public int $user_id,
        // orders
        public string $payment_method,
        public float $subtotal,
        public float $total,
        // order_details
        public string $delivery_date,
        public int $delivery_slot_id,
        public string $recipient_phone,
        public ?string $card_message,
        public ?string $signature,
        // user_addresses
        public string $street,
        public string $ext_number,
        public string $neighborhood,
        public string $dwelling_type,
        public string $zip_code,
        public int $state_id,
        public int $city_id,
        public ?string $references,
        // opcional: nombre del destinatario (no validado, lo usa order_details)
        public ?string $recipient_name,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            user_id: (int) $request->user()->id,
            // orders
            payment_method: (string) $request->input('payment_method'),
            subtotal: (float) $request->input('subtotal'),
            total: (float) $request->input('total'),
            // order_details
            delivery_date: (string) $request->input('delivery_date'),
            delivery_slot_id: (int) $request->input('delivery_slot_id'),
            recipient_phone: (string) $request->input('recipient_phone'),
            card_message: self::nullableString($request->input('card_message')),
            signature: self::nullableString($request->input('signature')),
            // user_addresses
            street: (string) $request->input('street'),
            ext_number: (string) $request->input('ext_number'),
            neighborhood: (string) $request->input('neighborhood'),
            dwelling_type: (string) $request->input('dwelling_type'),
            zip_code: (string) $request->input('zip_code'),
            state_id: (int) $request->input('state_id'),
            city_id: (int) $request->input('city_id'),
            references: self::nullableString($request->input('references')),
            recipient_name: self::nullableString($request->input('recipient_name')),
        );
    }

    /**
     * Compone el mensaje completo de la tarjeta (mensaje + firma).
     */
    public function composedCardMessage(): ?string
    {
        $parts = array_filter([$this->card_message, $this->signature]);

        return empty($parts) ? null : implode("\n\n— ", $parts);
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
