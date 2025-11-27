<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Silber\Bouncer\BouncerFacade as Bouncer;

class AdminRegistrationController extends Controller
{
    public function registerSystemAdmin(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $systemRole = Bouncer::role()->where('name', 'system-admin')->first();

        $systemAdminsExist = $systemRole
            ? $systemRole->users()->exists()
            : false;

        if ($systemAdminsExist) {
            $this->authorize('create-system-admin');
        }

        $user = User::create([
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Bouncer::assign('system-admin')->to($user);

        return response()->json([
            'message' => 'System admin created successfully',
            'data'    => [
                'user' => $user,
            ],
        ], 201);
    }

    public function registerBackendAdmin(Request $request)
    {
        $user = $request->user();

        if (! $user || ! $user->isA('system-admin')) {
            abort(403, 'Only system-admin can create backend admins');
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $newUser = User::create([
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Bouncer::assign('setup-admin')->to($newUser);

        return response()->json([
            'message' => 'Backend admin created successfully',
            'data'    => [
                'user' => $newUser,
            ],
        ], 201);
    }

    public function registerStaffWithRole(Request $request)
    {
        $user = $request->user();

        if (! $user || ! $user->isA('system-admin')) {
            abort(403, 'Only system-admin can create staff users');
        }

        $validated = $request->validate([
            'role'     => ['required', 'string', Rule::in([
                'customer-service',
                'accounts',
                'audit',
            ])],
            'username' => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $role = $validated['role'];

        $roleModel = Bouncer::role()->where('name', $role)->first();

        if (! $roleModel) {
            return response()->json([
                'message' => 'Invalid role',
            ], 400);
        }

        $newUser = User::create([
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Bouncer::assign($role)->to($newUser);

        return response()->json([
            'message' => ucfirst($role) . ' created successfully',
            'data'    => [
                'user' => $newUser,
            ],
        ], 201);
    }
}
