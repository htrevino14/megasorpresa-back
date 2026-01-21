<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $first_name,
        public ?string $last_name,
        public ?string $phone,
        public ?string $password,
        public int $loyalty_points = 0,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            first_name: $request->input('first_name'),
            last_name: $request->input('last_name'),
            phone: $request->input('phone'),
            password: $request->input('password'),
            loyalty_points: (int) $request->input('loyalty_points', 0),
        );
    }
}
