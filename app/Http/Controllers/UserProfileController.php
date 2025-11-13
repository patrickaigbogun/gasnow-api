<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserProfileResource;

class UserProfileController extends Controller
{
    public function show(Request $request)
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['message' => 'Missing token'], 400);
        }

        $personalToken = PersonalAccessToken::findToken($token);
        if (! $personalToken) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $user = $personalToken->tokenable;

        $user->load('profile');

        $userData = $user->only(['id', 'username', 'email', 'active']);
        $profileResource = $user->profile ? ProfileResource::make($user->profile) : null;

        return UserProfileResource::make([
            'user' => $userData,
            'profile' => $profileResource,
        ])->response();
    }
}
