<?php

namespace App\Http\Controllers\Api;

use App\DTOs\AddressDTO;
use App\DTOs\ReminderDTO;
use App\DTOs\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\StoreReminderRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\ReminderResource;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request)
    {
        $userDTO = UserDTO::fromRequest($request);
        $user = $this->userService->register($userDTO);

        // Generate token for the new user
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->userService->updateProfile(
            $request->user(),
            $request->validated()
        );

        return new UserResource($user);
    }

    /**
     * Get authenticated user's addresses.
     */
    public function addresses()
    {
        $addresses = $this->userService->getUserAddresses(auth()->id());

        return AddressResource::collection($addresses);
    }

    /**
     * Store a new address for the authenticated user.
     */
    public function storeAddress(StoreAddressRequest $request)
    {
        $addressDTO = AddressDTO::fromRequest($request);
        $address = $this->userService->addAddress($addressDTO);

        return (new AddressResource($address))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get authenticated user's reminders.
     */
    public function reminders()
    {
        $reminders = $this->userService->getUserReminders(auth()->id());

        return ReminderResource::collection($reminders);
    }

    /**
     * Store a new reminder for the authenticated user.
     */
    public function storeReminder(StoreReminderRequest $request)
    {
        $reminderDTO = ReminderDTO::fromRequest($request);
        $reminder = $this->userService->createReminder($reminderDTO);

        return (new ReminderResource($reminder))
            ->response()
            ->setStatusCode(201);
    }
}
