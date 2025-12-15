<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Setup\LookupController;
use App\Http\Controllers\Backend\StaffController;
use App\Http\Controllers\Backend\BackendUserController;
use App\Http\Controllers\Backend\RoleAssignmentController;
use App\Http\Controllers\AdminRegistrationController;
use App\Http\Controllers\PurchaseLookupController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Services\LookupService;



Route::get('/ping', function () {
    return response()->json(['status' => 'ok', 'message' => 'pong'], 200);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserProfileController::class, 'show']);

    // Purchase page combined lookups
    Route::get('/purchase/lookups', [PurchaseLookupController::class, 'index'])
        ->middleware('role_or_perm:customer,read-setup-system');

    // Admin registration (after login)
    Route::prefix('admin')->group(function () {
        Route::get('/roles', [AdminRoleController::class, 'index']);
        Route::get('/abilities', [AdminRoleController::class, 'abilities']);
        Route::post('/roles', [AdminRoleController::class, 'store']);
        Route::put('/roles/{id}', [AdminRoleController::class, 'update']);

        Route::post('/users', [AdminUserController::class, 'store']);
    });

    // Purchases (Bouncer can: middleware)
    Route::post('/purchases', [PurchaseController::class, 'store'])
        ->middleware('can:create-purchase');

    Route::get('/purchases', [PurchaseController::class, 'index'])
        ->middleware('can:read-purchases');

    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])
        ->middleware('can:read-purchases');

    Route::put('/purchases/{purchase}', [PurchaseController::class, 'update'])
        ->middleware('can:update-purchases');

    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])
        ->middleware('can:delete-purchases');

    // Setup-admin endpoints - Generic lookup API under /api/setup/{type}
    Route::prefix('setup')->group(function () {
        // Get all available lookup types
        Route::get('/types', [LookupController::class, 'types'])
            ->middleware('can:read-setup-system');

        // Generic lookup routes with dynamic middleware based on lookup type
        Route::get('/{type}', [LookupController::class, 'index'])
            ->middleware('role_or_perm:customer,read-setup-system')
            ->where('type', implode('|', app(LookupService::class)->getAvailableTypes()));

        Route::get('/{type}/{id}', [LookupController::class, 'show'])
            ->middleware('can:read-setup-system')
            ->where('type', implode('|', app(LookupService::class)->getAvailableTypes()));

        Route::post('/{type}', [LookupController::class, 'store'])
            ->middleware('can:create-setup-system')
            ->where('type', implode('|', app(LookupService::class)->getAvailableTypes()));

        Route::put('/{type}/{id}', [LookupController::class, 'update'])
            ->middleware('can:update-setup-system')
            ->where('type', implode('|', app(LookupService::class)->getAvailableTypes()));

        Route::delete('/{type}/{id}', [LookupController::class, 'destroy'])
            ->middleware('can:delete-setup-system')
            ->where('type', implode('|', app(LookupService::class)->getAvailableTypes()));
    });

    // Setup admin module
    Route::prefix('setup-admin')->group(function () {
            Route::get('/staff', [StaffController::class, 'index'])
            ->middleware('role_or_perm:setup-admin,read-setup-admin');
            Route::post('/staff', [StaffController::class, 'store'])
                ->middleware('role_or_perm:setup-admin,create-setup-admin');
            Route::put('/staff/{staff}', [StaffController::class, 'update'])
                ->middleware('role_or_perm:setup-admin,update-setup-admin');

            Route::post('/staff/{staff}/create-user', [BackendUserController::class, 'createUser'])
                ->middleware('role_or_perm:setup-admin,create-setup-admin');

            Route::post('/user/{user}/assign-role', [RoleAssignmentController::class, 'assignRole'])
                ->middleware('role_or_perm:setup-admin,update-setup-admin');
            Route::post('/user/{user}/remove-role', [RoleAssignmentController::class, 'removeRole'])
                ->middleware('role_or_perm:setup-admin,update-setup-admin');
        });
});

// Admin registration bootstrap (no auth): create first system-admin
Route::post('/admin/register/system-admin', [AdminRegistrationController::class, 'registerSystemAdmin']);
