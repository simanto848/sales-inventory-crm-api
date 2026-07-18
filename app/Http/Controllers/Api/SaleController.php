<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Sale;
use App\Services\SaleService;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class SaleController extends Controller
{
    use ApiResponser;

    public function __construct(
        protected SaleService $saleService
    ) {}

    public function listSales(): JsonResponse
    {
        return $this->success(
            $this->saleService->getAllSales((int) request()->get('per_page', 15)),
            'Sales retrieved successfully'
        );
    }

    public function createSale(StoreSaleRequest $request): JsonResponse
    {
        return $this->success(
            $this->saleService->createSale($request->validated()),
            'Sale created successfully',
            201
        );
    }

    public function getSaleDetails(Sale $sale): JsonResponse
    {
        return $this->success($sale, 'Sale retrieved successfully');
    }

    public function deleteSale(Sale $sale): JsonResponse
    {
        $this->saleService->deleteSale($sale);
        return $this->success(null, 'Sale deleted successfully');
    }

    public function getSalesByCustomer(int $customerId): JsonResponse
    {
        return $this->success(
            $this->saleService->getSalesByCustomer($customerId, (int) request()->get('per_page', 15)),
            'Customer sales retrieved successfully'
        );
    }

    public function getSalesByBranch(int $branchId): JsonResponse
    {
        return $this->success(
            $this->saleService->getSalesByBranch($branchId, (int) request()->get('per_page', 15)),
            'Branch sales retrieved successfully'
        );
    }
}