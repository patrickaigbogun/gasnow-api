<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $access_token = $user->createToken('api-token', ['*'], now()->addDays(14));
            $refresh_token = $user->createToken('refresh-token', ['*'], now()->addDays(30));

            return response()->json([
                'user' => $user,
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 400);
    }
}
