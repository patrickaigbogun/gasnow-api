<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Support\PermissionScopeMapper;
use Laravel\Sanctum\PersonalAccessToken;
use App\Services\SessionContext;
use Silber\Bouncer\BouncerFacade as Bouncer;

/**
 * Handle token-based authentication for the API.
 *
 * Exposes endpoints to register, login, refresh tokens, fetch the
 * authenticated user, and logout. Uses Laravel Sanctum for issuing
 * personal access tokens and Bouncer for role/ability checks.
 */
class AuthController extends Controller
{
    /**
     * Register a new user and assign a default role.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Get the customer role id
        $customerRole = Bouncer::role()->where('name', 'customer')->first();

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'primary_role_id' => $customerRole ? $customerRole->id : null,
        ]);

        Bouncer::assign('customer')->to($user);

        return response()->json(['user' => $user], 201);
    }

    /**
     * Authenticate a user and issue access and refresh tokens.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // If primary_role_id is not set, set it to the customer role
        if (is_null($user->primary_role_id)) {
            $customerRole = Bouncer::role()->where('name', 'customer')->first();
            if ($customerRole) {
                $user->primary_role_id = $customerRole->id;
                $user->save();
            }
        }

        $accessToken = $user->createToken('access-token', ['*'], now()->addDays(14));
        $refreshToken = $user->createToken('refresh-token', ['*'], now()->addDays(30));

        $context = SessionContext::build($user);

        return response()->json(array_merge($context, [
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
        ]));
    }

    /**
     * Refresh the access token using a valid refresh token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['message' => 'Missing token'], 400);
        }

        $personalToken = PersonalAccessToken::findToken($token);

        if (! $personalToken || $personalToken->name !== 'refresh-token') {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        $user = $personalToken->tokenable;
        $personalToken->delete(); // optional: rotate token each refresh

        $newAccessToken = $user->createToken('access-token', ['*'], now()->addDays(14));
        $newRefreshToken = $user->createToken('refresh-token', ['*'], now()->addDays(30));

        return response()->json([
            'access_token' => $newAccessToken->plainTextToken,
            'refresh_token' => $newRefreshToken->plainTextToken,
        ]);
    }

    /**
     * Return the authenticated user with roles and permissions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $context = SessionContext::build($user);

        $accessToken = $user->createToken('access-token', ['*'], now()->addDays(14));
        $refreshToken = $user->createToken('refresh-token', ['*'], now()->addDays(30));

        return response()->json(array_merge($context, [
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
        ]));
    }

    /**
     * Revoke the current access token (logout).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if ($token) {
            $personalToken = PersonalAccessToken::findToken($token);
            if ($personalToken) {
                $personalToken->delete();
            }
        }

        return response()->json(['message' => 'Logged out successfully']);
    }
}
