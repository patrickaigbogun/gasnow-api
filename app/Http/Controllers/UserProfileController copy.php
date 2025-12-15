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
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user->load('profile');

        $userData = $user->only(['id', 'username', 'email', 'active']);
        $profileResource = $user->profile ? ProfileResource::make($user->profile) : null;

        return UserProfileResource::make([
            'user' => $userData,
            'profile' => $profileResource,
        ])->response();
    }
}
