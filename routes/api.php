<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\BranchStockController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;

// Public Auth routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth info & logout
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Branches
    Route::get('/branches', [BranchController::class, 'listBranches']);
    Route::post('/branches', [BranchController::class, 'createBranch']);
    Route::get('/branches/{branch}', [BranchController::class, 'getBranchDetails']);
    Route::put('/branches/{branch}', [BranchController::class, 'updateBranch']);
    Route::delete('/branches/{branch}', [BranchController::class, 'deleteBranch']);

    // Branch inventory/stock management
    Route::get('/branches/{branch}/inventory', [BranchStockController::class, 'getBranchInventory']);
    Route::post('/branches/{branch}/inventory', [BranchStockController::class, 'updateBranchStock']);
    Route::post('/branches/{branch}/inventory/adjust', [BranchStockController::class, 'adjustBranchStock']);
    Route::post('/branches/{branch}/inventory/add-product', [BranchStockController::class, 'addProductToBranchInventory']);
    Route::get('/branches/{branch}/inventory/low-stock', [BranchStockController::class, 'getBranchLowStock']);
    Route::get('/branches/{branch}/inventory/{product}', [BranchStockController::class, 'getBranchProductStock']);
    Route::get('/inventory', [BranchStockController::class, 'getAllInventory']);

    // Products
    Route::get('/products/search', [ProductController::class, 'searchProducts']);
    Route::get('/products', [ProductController::class, 'listProducts']);
    Route::post('/products', [ProductController::class, 'createProduct']);
    Route::get('/products/{product}', [ProductController::class, 'getProductDetails']);
    Route::put('/products/{product}', [ProductController::class, 'updateProduct']);
    Route::delete('/products/{product}', [ProductController::class, 'deleteProduct']);

    // Customers
    Route::get('/customers/search', [CustomerController::class, 'searchCustomers']);
    Route::get('/customers/inactive', [CustomerController::class, 'listInactiveCustomers']);
    Route::get('/customers/assigned', [CustomerController::class, 'listAssignedCustomers']);
    Route::post('/customers/{customer}/assign-employee', [CustomerController::class, 'assignCustomerToEmployee']);
    Route::post('/customers/{customer}/unassign-employee', [CustomerController::class, 'unassignCustomerFromEmployee']);
    Route::post('/customers/{customer}/re-engage', [CustomerController::class, 'sendCustomerReEngagement']);
    Route::get('/customers/{customer}/purchases', [CustomerController::class, 'getCustomerPurchaseHistory']);
    Route::get('/customers', [CustomerController::class, 'listCustomers']);
    Route::post('/customers', [CustomerController::class, 'createCustomer']);
    Route::get('/customers/{customer}', [CustomerController::class, 'getCustomerDetails']);
    Route::put('/customers/{customer}', [CustomerController::class, 'updateCustomer']);
    Route::delete('/customers/{customer}', [CustomerController::class, 'deleteCustomer']);

    // Sales
    Route::get('/sales/customer/{customerId}', [SaleController::class, 'getSalesByCustomer']);
    Route::get('/sales/branch/{branchId}', [SaleController::class, 'getSalesByBranch']);
    Route::get('/sales', [SaleController::class, 'listSales']);
    Route::post('/sales', [SaleController::class, 'createSale']);
    Route::get('/sales/{sale}', [SaleController::class, 'getSaleDetails']);
    Route::delete('/sales/{sale}', [SaleController::class, 'deleteSale']);

    // Employees
    Route::get('/employees/top-performers', [EmployeeController::class, 'getTopPerformers']);
    Route::get('/employees/{employee}/kpi', [EmployeeController::class, 'getEmployeeKpi']);
    Route::get('/employees', [EmployeeController::class, 'listEmployees']);
    Route::post('/employees', [EmployeeController::class, 'createEmployee']);
    Route::get('/employees/{employee}', [EmployeeController::class, 'getEmployeeDetails']);
    Route::put('/employees/{employee}', [EmployeeController::class, 'updateEmployee']);
    Route::delete('/employees/{employee}', [EmployeeController::class, 'deleteEmployee']);
});