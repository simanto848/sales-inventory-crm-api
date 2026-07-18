<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductToBranchRequest;
use App\Http\Requests\UpdateBranchStockRequest;
use App\Models\Branch;
use App\Models\Product;
use App\Services\BranchStockService;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class BranchStockController extends Controller
{
    use ApiResponser;

    public function __construct(
        protected BranchStockService $branchStockService
    ) {}

    public function getBranchInventory(Branch $branch): JsonResponse
    {
        return $this->success(
            $this->branchStockService->getBranchInventoryPaginated($branch->id, (int) request()->get('per_page', 15)),
            'Branch inventory retrieved successfully'
        );
    }

    public function updateBranchStock(UpdateBranchStockRequest $request, Branch $branch): JsonResponse
    {
        return $this->success(
            $this->branchStockService->updateStock(
                $branch->id,
                $request->product_id,
                $request->stock_quantity
            )->load('product'),
            'Stock updated successfully.'
        );
    }

    public function adjustBranchStock(UpdateBranchStockRequest $request, Branch $branch): JsonResponse
    {
        return $this->success(
            $this->branchStockService->adjustStock(
                $branch->id,
                $request->product_id,
                $request->stock_quantity
            )->load('product'),
            'Stock adjusted successfully.'
        );
    }

    public function getBranchLowStock(Branch $branch): JsonResponse
    {
        return $this->success(
            $this->branchStockService->getLowStockProducts($branch->id, (int) request()->get('threshold', 10)),
            'Low stock products retrieved successfully'
        );
    }

    public function addProductToBranchInventory(AddProductToBranchRequest $request, Branch $branch): JsonResponse
    {
        return $this->success(
            $this->branchStockService->addProductToBranch(
                $branch->id,
                $request->product_id,
                (int) $request->get('initial_quantity', 0)
            )->load('product'),
            'Product added to branch successfully.',
            201
        );
    }

    public function getBranchProductStock(Branch $branch, Product $product): JsonResponse
    {
        return $this->success(
            $this->branchStockService->getStockDetails($branch->id, $product->id),
            'Product stock retrieved successfully'
        );
    }

    public function getAllInventory(): JsonResponse
    {
        return $this->success(
            $this->branchStockService->getAllInventoryPaginated((int) request()->get('per_page', 15)),
            'All branch stock inventory retrieved successfully'
        );
    }
}