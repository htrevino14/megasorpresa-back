<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class AddressDTO
{
    public function __construct(
        public int $user_id,
        public string $recipient_name,
        public string $phone_code,
        public string $phone,
        public string $street,
        public string $ext_number,
        public string $neighborhood,
        public string $dwelling_type,
        public int $city_id,
        public int $state_id,
        public string $zip_code,
        public ?string $references,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            user_id: $request->user()->id,
            recipient_name: (string) $request->input('recipient_name'),
            phone_code: (string) $request->input('phone_code'),
            phone: (string) $request->input('phone'),
            street: (string) $request->input('street'),
            ext_number: (string) $request->input('ext_number'),
            neighborhood: (string) $request->input('neighborhood'),
            dwelling_type: (string) $request->input('dwelling_type'),
            city_id: (int) $request->input('city_id'),
            state_id: (int) $request->input('state_id'),
            zip_code: (string) $request->input('zip_code'),
            references: $request->input('references'),
        );
    }
}
