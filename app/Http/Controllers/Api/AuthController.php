<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const DEFAULT_DEVICE_NAME = 'api';

    /**
     * Register a user and issue a Sanctum token.
     *
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate($this->registerRules());
        $user = $this->createUser($validated);

        event(new Registered($user));

        return $this->tokenResponse($user, $this->deviceName($validated), 201);
    }

    /**
     * Authenticate a user and issue a Sanctum token.
     *
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate($this->loginRules());
        $user = $this->findUserForCredentials($validated);

        return $this->tokenResponse($user, $this->deviceName($validated));
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function registerRules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], $this->deviceNameRules());
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function loginRules(): array
    {
        return array_merge([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], $this->deviceNameRules());
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function deviceNameRules(): array
    {
        return [
            'device_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @param  array{name: string, email: string, password: string}  $validated
     */
    private function createUser(array $validated): User
    {
        return User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
    }

    /**
     * @param  array{email: string, password: string}  $validated
     *
     * @throws ValidationException
     */
    private function findUserForCredentials(array $validated): User
    {
        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        return $user;
    }

    private function tokenResponse(User $user, string $deviceName, int $status = 200): JsonResponse
    {
        return response()->json([
            'user' => $user,
            'token' => $user->createToken($deviceName)->plainTextToken,
            'token_type' => 'Bearer',
        ], $status);
    }

    /**
     * @param  array{device_name?: string|null}  $validated
     */
    private function deviceName(array $validated): string
    {
        return $validated['device_name'] ?? self::DEFAULT_DEVICE_NAME;
    }
}
