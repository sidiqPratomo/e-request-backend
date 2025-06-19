<?php

use App\Http\Controllers\Api\ApiResourcesController;
use App\Http\Controllers\Api\AuditrailController;
use App\Http\Controllers\Api\EmployeesController;
use App\Http\Controllers\Api\ExamplesController;
use App\Http\Controllers\Api\LeavesController;
use App\Http\Controllers\Api\PrivilegesController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\Storage\FileController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('', function () {
    return phpinfo();
});

Route::prefix('v1')->group(function () {
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::post('signin', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });
});

Route::prefix('v1')->group(function () {
    Route::group([
        'prefix' => 'file',
    ], function () {
        Route::post('upload', [FileController::class, 'upload']);
        Route::get('download', [FileController::class, 'get']);
    });
});

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('logout', [AuthController::class], 'logout');
    });
});

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::prefix('auditrails')->group(function () {
        Route::get('/', [AuditrailController::class, 'index']);
        Route::get('/log/download', [AuditrailController::class, 'download']);
    });

    Route::prefix('examples')->group(function () {
        Route::post('/', [ExamplesController::class, 'create']);
        Route::get('/{id}', [ExamplesController::class, 'read']);
        Route::put('/{id}', [ExamplesController::class, 'update']);
    });

    Route::prefix('privileges')->group(function () {
        Route::get('fetch/format', [PrivilegesController::class, 'fetch']);
    });

    Route::prefix('roles')->group(function () {
        Route::post('/', [RolesController::class, 'create']);
        Route::get('{role}', [RolesController::class, 'read']);
        Route::put('{role}', [RolesController::class, 'update']);
    });

    Route::prefix('users')->group(function () {
        Route::post('/', [UsersController::class, 'create']);
        Route::get('/', [EmployeesController::class, 'index']);
        Route::get('{users}', [UsersController::class, 'read']);
        Route::put('{users}', [UsersController::class, 'update']);
    });

    Route::prefix('employees')->group(function () {
        Route::post('/', [EmployeesController::class, 'create']);
        Route::get('/', [EmployeesController::class, 'index']);
        Route::get('/{id}', [EmployeesController::class, 'read']);
        Route::put('/{id}', [EmployeesController::class, 'update']);
        Route::put('/{id}/delete', [EmployeesController::class, 'softDelete']);
    });

    Route::prefix('leaves')->group(function () {
        Route::post('/', [LeavesController::class, 'create']);
        Route::get('/', [LeavesController::class, 'index']);
        Route::get('/{id}', [LeavesController::class, 'read']);
        Route::put('/{id}', [LeavesController::class, 'update']);
        Route::put('/{id}/delete', [LeavesController::class, 'softDelete']);
    });
});

Route::middleware(['auth:api', 'checkModel'])->prefix('v1')->group(function () {
    Route::get('{collection}', [ApiResourcesController::class, 'index']);
    Route::get('{collection}/null/export', [ApiResourcesController::class, 'export']);
    Route::get('{collection}/{id}', [ApiResourcesController::class, 'read']);
    Route::post('{collection}', [ApiResourcesController::class, 'create']);
    Route::put('{collection}/{id}', [ApiResourcesController::class, 'update']);
    Route::put('{collection}/{id}/delete', [ApiResourcesController::class, 'softDelete']);
    Route::delete('{collection}/{id}/destroy', [ApiResourcesController::class, 'hardDelete']);
    Route::put('{collection}/{id}/restore', [ApiResourcesController::class, 'restore']);
});
