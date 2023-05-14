<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BoardOfDirectorController;
use App\Http\Controllers\Api\GeneralAssistantController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'v1'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group([
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/profile', [ProfileController::class, 'get'])->name('profile');
        Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    });

    Route::group([
        'middleware' => ['auth:api', 'role:ga,bod']
    ], function () {

        Route::group([
            'prefix' => 'staff',
            'name' => 'staff.'
        ], function () {
            Route::get('/', [StaffController::class, 'all'])->name('all');
            Route::get('/{uuid}', [StaffController::class, 'get'])->name('get');
            Route::post('/', [StaffController::class, 'store'])->name('store');
            Route::patch('/{uuid}', [StaffController::class, 'update'])->name('update');
            Route::patch('/{uuid}/reset-password', [StaffController::class, 'resetPassword'])->name('reset-password');
            Route::delete('/{uuid}', [StaffController::class, 'delete'])->name('delete');
        });

        Route::group([
            'prefix' => 'ga',
            'name' => 'ga.'
        ], function () {
            Route::get('/', [GeneralAssistantController::class, 'all'])->name('all');
            Route::get('/{uuid}', [GeneralAssistantController::class, 'get'])->name('get');
            Route::post('/', [GeneralAssistantController::class, 'store'])->name('store');
            Route::patch('/{uuid}', [GeneralAssistantController::class, 'update'])->name('update');
            Route::patch('/{uuid}/reset-password', [GeneralAssistantController::class, 'resetPassword'])->name('reset-password');
            Route::delete('/{uuid}', [GeneralAssistantController::class, 'delete'])->name('delete');
        });

        Route::group([
            'prefix' => 'bod',
            'name' => 'bod.'
        ], function () {
            Route::get('/', [BoardOfDirectorController::class, 'all'])->name('all');
            Route::get('/{uuid}', [BoardOfDirectorController::class, 'get'])->name('get');
            Route::post('/', [BoardOfDirectorController::class, 'store'])->name('store');
            Route::patch('/{uuid}', [BoardOfDirectorController::class, 'update'])->name('update');
            Route::patch('/{uuid}/reset-password', [BoardOfDirectorController::class, 'resetPassword'])->name('reset-password');
            Route::delete('/{uuid}', [BoardOfDirectorController::class, 'delete'])->name('delete');
        });

        Route::group([
            'prefix' => 'vehicle',
            'name' => 'vehicle.'
        ], function () {
            Route::get('/', [VehicleController::class, 'all'])->name('all');
            Route::get('/{id}', [VehicleController::class, 'get'])->name('get');
            Route::post('/', [VehicleController::class, 'store'])->name('store');
            Route::patch('/{id}', [VehicleController::class, 'update'])->name('update');
            Route::delete('/{id}', [VehicleController::class, 'delete'])->name('delete');
        });

        Route::group([
            'prefix' => 'order',
            'name' => 'order.'
        ], function () {
            Route::get('/', [OrderController::class, 'all'])->name('all');
            Route::get('/{id}', [OrderController::class, 'get'])->name('get');
            Route::post('/{id}/reject', [OrderController::class, 'rejectOrder'])->name('rejectOrder');
        });
    });

    Route::group([
        'prefix' => 'order',
        'name' => 'name.',
        'middleware' => ['auth:api', 'role:ga']
    ], function () {
        Route::patch('/ga/{id}/approve', [OrderController::class, 'approvedByGa'])->name('ga.approve');
    });

    Route::group([
        'prefix' => 'order',
        'name' => 'name.',
        'middleware' => ['auth:api', 'role:bod']
    ], function () {
        Route::patch('/bod/{id}/approve', [OrderController::class, 'approvedByBod'])->name('ga.approve');
    });

    Route::group([
        'prefix' => 'order',
        'name' => 'name.',
        'middleware' => ['auth:api', 'role:staff,ga']
    ], function () {
        Route::post('/create', [OrderController::class, 'createOrder'])->name('create');
        Route::post('/{id}/update', [OrderController::class, 'updateOrder'])->name('update');
        Route::patch('/{id}/submit', [OrderController::class, 'submitOrder'])->name('submit');
        Route::post('/{id}/done', [OrderController::class, 'completeOrder'])->name('done');
    });

    Route::group([
        'middleware' => ['auth:api', 'role:staff']
    ], function () {
        Route::get('/vehicle-staff', [VehicleController::class, 'available'])->name('vehicle.available');
    });
});
