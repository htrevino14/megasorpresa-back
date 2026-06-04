<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $first_name,
        public string $last_name,
        public string $gender,
        public string $phone,
        public string $password,
        public int $loyalty_points = 0,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $firstName = trim((string) $request->input('first_name'));
        $lastName = trim((string) $request->input('last_name'));
        $phoneCode = trim((string) $request->input('phone_code'));
        $phone = trim((string) $request->input('phone'));

        return new self(
            name: trim("{$firstName} {$lastName}"),
            email: (string) $request->input('email'),
            first_name: $firstName,
            last_name: $lastName,
            gender: (string) $request->input('gender'),
            phone: $phoneCode . $phone,
            password: (string) $request->input('password'),
            loyalty_points: (int) $request->input('loyalty_points', 0),
        );
    }
}
