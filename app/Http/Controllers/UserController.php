<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserProfileResource;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function showProfile(Request $request)
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

        $user->load(['profile.gender', 'profile.department', 'profile.designation', 'profile.status']);

        $userData = $user->only(['id', 'username', 'email', 'active']);
        $profileResource = $user->profile = ProfileResource::make($user->profile) ;

        return UserProfileResource::make([
            'user' => $userData,
            'profile' => $profileResource,
        ])->response();
    }
}
