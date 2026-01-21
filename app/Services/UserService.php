<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\DTOs\AddressDTO;
use App\DTOs\ReminderDTO;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Reminder;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Register a new user.
     */
    public function register(UserDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'phone' => $dto->phone,
            'password' => Hash::make($dto->password),
            'loyalty_points' => $dto->loyalty_points,
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    /**
     * Add a new address for user.
     */
    public function addAddress(AddressDTO $dto): UserAddress
    {
        return UserAddress::create([
            'user_id' => $dto->user_id,
            'street' => $dto->street,
            'ext_number' => $dto->ext_number,
            'neighborhood' => $dto->neighborhood,
            'city_id' => $dto->city_id,
            'zip_code' => $dto->zip_code,
            'references' => $dto->references,
        ]);
    }

    /**
     * Get user addresses.
     */
    public function getUserAddresses(int $userId)
    {
        return UserAddress::where('user_id', $userId)
            ->with('city.state')
            ->get();
    }

    /**
     * Create a reminder for user.
     */
    public function createReminder(ReminderDTO $dto): Reminder
    {
        return Reminder::create([
            'user_id' => $dto->user_id,
            'event_name' => $dto->event_name,
            'date' => $dto->date,
            'notify_days_before' => $dto->notify_days_before,
        ]);
    }

    /**
     * Get user reminders.
     */
    public function getUserReminders(int $userId)
    {
        return Reminder::where('user_id', $userId)
            ->orderBy('date')
            ->get();
    }
}
