<?php

namespace App\Services;

use App\Models\User;
use App\Support\PermissionScopeMapper;

class SessionContext
{
    public static function build(User $user): array
    {
        // Get the roles assigned to the user
        $roles = $user->getRoles();

        // Get all ability names (e.g., "create-purchase", "read-billing")
        $abilities = $user->getAbilities()->pluck('name')->toArray();

        // Map abilities into a structured permission object
        $permissions = PermissionScopeMapper::map($abilities);

        return [
            'user' => $user->only(['id', 'username', 'email']),
            'roles' => $roles,
            'permissions' => $permissions,
        ];
    }
}
