<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Bouncer;

class BackendUserController extends Controller
{
    public function createUser(Request $request, Staff $staff): JsonResponse
    {
        if ($staff->user) {
            return response()->json(['message' => 'Staff already has a user account'], 400);
        }

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:150|unique:users,username',
            'role' => 'required|string|exists:roles,name',
        ]);

        $tempPassword = Hash::make('Password123!');

        $user = User::create([
            'staff_id' => $staff->id,
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $tempPassword,
        ]);

        Bouncer::assign($validated['role'])->to($user);

        return response()->json([
            'message' => 'Backend user created',
            'user' => $user,
            'role' => $validated['role'],
        ], 201);
    }
}
