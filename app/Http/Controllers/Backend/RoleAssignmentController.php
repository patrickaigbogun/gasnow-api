<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Bouncer;

class RoleAssignmentController extends Controller
{
    public function assignRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        Bouncer::assign($validated['role'])->to($user);

        return response()->json([
            'message' => 'Role assigned',
            'roles' => $user->getRoles(),
        ]);
    }

    public function removeRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        Bouncer::retract($validated['role'])->from($user);

        return response()->json([
            'message' => 'Role removed',
            'roles' => $user->getRoles(),
        ]);
    }
}
