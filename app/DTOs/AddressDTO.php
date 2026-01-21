<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class AddressDTO
{
    public function __construct(
        public int $user_id,
        public string $street,
        public ?string $ext_number,
        public ?string $neighborhood,
        public int $city_id,
        public ?string $zip_code,
        public ?string $references,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            user_id: $request->user()->id,
            street: $request->input('street'),
            ext_number: $request->input('ext_number'),
            neighborhood: $request->input('neighborhood'),
            city_id: (int) $request->input('city_id'),
            zip_code: $request->input('zip_code'),
            references: $request->input('references'),
        );
    }
}
