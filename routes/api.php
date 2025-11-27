<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Setup\CylinderSizeController;
use App\Http\Controllers\Setup\PurchaseKgController;
use App\Http\Controllers\Setup\DeliveryTimeController;
use App\Http\Controllers\Setup\ComplaintCategoryController;
use App\Http\Controllers\Setup\DepartmentController;
use App\Http\Controllers\Setup\DesignationController;
use App\Http\Controllers\Setup\GenderController;
use App\Http\Controllers\Setup\StatusController;
use App\Http\Controllers\Setup\PaymentTypeController;
use App\Http\Controllers\Backend\StaffController;
use App\Http\Controllers\Backend\BackendUserController;
use App\Http\Controllers\Backend\RoleAssignmentController;
use App\Http\Controllers\AdminRegistrationController;
use App\Http\Controllers\PurchaseLookupController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;



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

    // Setup-admin endpoints under /api/setup/{entity}
    Route::prefix('setup')->group(function () {
        Route::get('/cylinder-sizes', [CylinderSizeController::class, 'index'])
            ->middleware('role_or_perm:customer,read-setup-system');
        Route::post('/cylinder-sizes', [CylinderSizeController::class, 'store'])
            ->middleware('can:create-setup-system');
        Route::put('/cylinder-sizes/{cylinder_size}', [CylinderSizeController::class, 'update'])
            ->middleware('can:update-setup-system');
        Route::delete('/cylinder-sizes/{cylinder_size}', [CylinderSizeController::class, 'destroy'])
            ->middleware('can:delete-setup-system');

        Route::get('/purchase-kgs', [PurchaseKgController::class, 'index'])
            ->middleware('role_or_perm:customer,read-setup-system');
        Route::post('/purchase-kgs', [PurchaseKgController::class, 'store'])
            ->middleware('can:create-setup-system');
        Route::put('/purchase-kgs/{purchase_kg}', [PurchaseKgController::class, 'update'])
            ->middleware('can:update-setup-system');
        Route::delete('/purchase-kgs/{purchase_kg}', [PurchaseKgController::class, 'destroy'])
            ->middleware('can:delete-setup-system');

        Route::get('/delivery-times', [DeliveryTimeController::class, 'index'])
            ->middleware('role_or_perm:customer,read-setup-system');
        Route::post('/delivery-times', [DeliveryTimeController::class, 'store'])
            ->middleware('can:create-setup-system');
        Route::put('/delivery-times/{delivery_time}', [DeliveryTimeController::class, 'update'])
            ->middleware('can:update-setup-system');
        Route::delete('/delivery-times/{delivery_time}', [DeliveryTimeController::class, 'destroy'])
            ->middleware('can:delete-setup-system');

        Route::get('/complaint-categories', [ComplaintCategoryController::class, 'index'])
            ->middleware('can:read-setup-system');
        Route::post('/complaint-categories', [ComplaintCategoryController::class, 'store'])
            ->middleware('can:create-setup-system');
        Route::put('/complaint-categories/{complaint_category}', [ComplaintCategoryController::class, 'update'])
            ->middleware('can:update-setup-system');
        Route::delete('/complaint-categories/{complaint_category}', [ComplaintCategoryController::class, 'destroy'])
            ->middleware('can:delete-setup-system');

        Route::get('/departments', [DepartmentController::class, 'index'])
            ->middleware('can:read-setup-system');
        Route::post('/departments', [DepartmentController::class, 'store'])
            ->middleware('can:create-setup-system');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])
            ->middleware('can:update-setup-system');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])
            ->middleware('can:delete-setup-system');

        Route::get('/designations', [DesignationController::class, 'index'])
            ->middleware('can:read-setup-system');
        Route::post('/designations', [DesignationController::class, 'store'])
            ->middleware('can:create-setup-system');
        Route::put('/designations/{designation}', [DesignationController::class, 'update'])
            ->middleware('can:update-setup-system');
        Route::delete('/designations/{designation}', [DesignationController::class, 'destroy'])
            ->middleware('can:delete-setup-system');

        Route::get('/payment-types', [PaymentTypeController::class, 'index'])
            ->middleware('can:read-setup-system');
        Route::post('/payment-types', [PaymentTypeController::class, 'store'])
            ->middleware('can:create-setup-system');
        Route::put('/payment-types/{payment_type}', [PaymentTypeController::class, 'update'])
            ->middleware('can:update-setup-system');
        Route::delete('/payment-types/{payment_type}', [PaymentTypeController::class, 'destroy'])
            ->middleware('can:delete-setup-system');

        Route::get('/genders', [GenderController::class, 'index'])
            ->middleware('can:read-setup-system');
        Route::post('/genders', [GenderController::class, 'store'])
            ->middleware('can:create-setup-system');
        Route::put('/genders/{gender}', [GenderController::class, 'update'])
            ->middleware('can:update-setup-system');
        Route::delete('/genders/{gender}', [GenderController::class, 'destroy'])
            ->middleware('can:delete-setup-system');

        Route::get('/statuses', [StatusController::class, 'index'])
            ->middleware('can:read-setup-system');
        Route::post('/statuses', [StatusController::class, 'store'])
            ->middleware('can:create-setup-system');
        Route::put('/statuses/{status}', [StatusController::class, 'update'])
            ->middleware('can:update-setup-system');
        Route::delete('/statuses/{status}', [StatusController::class, 'destroy'])
            ->middleware('can:delete-setup-system');
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
