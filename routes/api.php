<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\BranchStockController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductIntegrationController;
use App\Http\Controllers\Api\SaleController;

// Auth Routes (Mixed public and protected)
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', 'profile');
        Route::post('/logout', 'logout');
    });
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Branches & Branch Stock
    Route::prefix('branches')->group(function () {
        Route::controller(BranchController::class)->group(function () {
            Route::get('/', 'listBranches');
            Route::post('/', 'createBranch');
            Route::get('/{branch}', 'getBranchDetails');
            Route::put('/{branch}', 'updateBranch');
            Route::delete('/{branch}', 'deleteBranch');
        });

        Route::prefix('{branch}/inventory')->controller(BranchStockController::class)->group(function () {
            Route::get('/', 'getBranchInventory');
            Route::post('/', 'updateBranchStock');
            Route::post('/adjust', 'adjustBranchStock');
            Route::post('/add-product', 'addProductToBranchInventory');
            Route::get('/low-stock', 'getBranchLowStock');
            Route::get('/{product}', 'getBranchProductStock');
        });
    });

    // Global Inventory
    Route::get('/inventory', [BranchStockController::class, 'getAllInventory']);

    // Products
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('/search', 'searchProducts');
        Route::get('/', 'listProducts');
        Route::post('/', 'createProduct');
        Route::get('/{product}', 'getProductDetails');
        Route::put('/{product}', 'updateProduct');
        Route::delete('/{product}', 'deleteProduct');
    });

    // Customers
    Route::prefix('customers')->controller(CustomerController::class)->group(function () {
        Route::get('/search', 'searchCustomers');
        Route::get('/inactive', 'listInactiveCustomers');
        Route::get('/assigned', 'listAssignedCustomers');
        Route::post('/{customer}/assign-employee', 'assignCustomerToEmployee');
        Route::post('/{customer}/unassign-employee', 'unassignCustomerFromEmployee');
        Route::post('/{customer}/re-engage', 'sendCustomerReEngagement');
        Route::get('/{customer}/purchases', 'getCustomerPurchaseHistory');
        Route::get('/', 'listCustomers');
        Route::post('/', 'createCustomer');
        Route::get('/{customer}', 'getCustomerDetails');
        Route::put('/{customer}', 'updateCustomer');
        Route::delete('/{customer}', 'deleteCustomer');
    });

    // Sales
    Route::prefix('sales')->controller(SaleController::class)->group(function () {
        Route::get('/customer/{customerId}', 'getSalesByCustomer');
        Route::get('/branch/{branchId}', 'getSalesByBranch');
        Route::get('/', 'listSales');
        Route::post('/', 'createSale');
        Route::get('/{sale}', 'getSaleDetails');
        Route::delete('/{sale}', 'deleteSale');
    });

    // Employees
    Route::prefix('employees')->controller(EmployeeController::class)->group(function () {
        Route::get('/top-performers', 'getTopPerformers');
        Route::get('/{employee}/kpi', 'getEmployeeKpi');
        Route::get('/', 'listEmployees');
        Route::post('/', 'createEmployee');
        Route::get('/{employee}', 'getEmployeeDetails');
        Route::put('/{employee}', 'updateEmployee');
        Route::delete('/{employee}', 'deleteEmployee');
    });
});

// Third-party e-commerce integration API (Secured by API Key)
Route::middleware('api.key')->group(function () {
    Route::get('/integration/products', [ProductIntegrationController::class, 'index']);
});