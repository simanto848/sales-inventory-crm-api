<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchStockController;
use App\Http\Controllers\ProductController;

// Public Auth routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth info & logout
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Branches CRUD
    Route::apiResource('branches', BranchController::class);

    // Products CRUD
    Route::apiResource('products', ProductController::class);

    // Branch specific stock management
    Route::get('/branches/{branch}/inventory', [BranchStockController::class, 'index']);
    Route::post('/branches/{branch}/inventory', [BranchStockController::class, 'update']);
});
