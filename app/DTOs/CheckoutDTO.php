<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

/**
 * Datos validados del proceso de checkout (todos los pasos del wizard).
 *
 * Inmutable; se construye desde el `StoreCheckoutRequest` ya validado.
 */
readonly class CheckoutDTO
{
    public function __construct(
        public int $user_id,
        public string $buyer_phone,
        public array $recipient,
        public string $delivery_date,
        public int $delivery_slot_id,
        public ?string $card_message,
        public ?string $card_signature,
        public int $payment_method_id,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $recipient = (array) $request->input('recipient', []);
        $dedication = (array) $request->input('dedication', []);

        $message = self::nullableString($dedication['message'] ?? null);
        $signature = self::nullableString($dedication['signature'] ?? null);

        return new self(
            user_id: (int) $request->user()->id,
            buyer_phone: (string) $request->input('phone'),
            recipient: [
                'recipient_name' => (string) ($recipient['recipient_name'] ?? ''),
                'phone' => self::nullableString($recipient['phone'] ?? null),
                'street' => (string) ($recipient['street'] ?? ''),
                'ext_number' => (string) ($recipient['ext_number'] ?? ''),
                'interior_number' => self::nullableString($recipient['interior_number'] ?? null),
                'neighborhood' => (string) ($recipient['neighborhood'] ?? ''),
                'zip_code' => (string) ($recipient['zip_code'] ?? ''),
                'city_id' => (int) ($recipient['city_id'] ?? 0),
                'references' => self::nullableString($recipient['references'] ?? null),
            ],
            delivery_date: (string) $request->input('schedule.delivery_date'),
            delivery_slot_id: (int) $request->input('schedule.delivery_slot_id'),
            card_message: $message,
            card_signature: $signature,
            payment_method_id: (int) $request->input('payment.payment_method_id'),
        );
    }

    /**
     * Compone el mensaje completo de la tarjeta (mensaje + firma).
     */
    public function composedCardMessage(): ?string
    {
        $parts = array_filter([$this->card_message, $this->card_signature]);

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
