<?php

namespace App\Services;

use App\Models\BranchProduct;
use App\Models\Product;
use App\Repositories\Contracts\BranchStockRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BranchStockService
{
    public function __construct(
        protected BranchStockRepositoryInterface $branchStockRepository
    ) {}

    public function getBranchInventory(int $branchId): Collection
    {
        return $this->branchStockRepository->getBranchInventory($branchId);
    }

    public function getBranchInventoryPaginated(int $branchId, int $perPage = 15)
    {
        return $this->branchStockRepository->getBranchInventoryPaginated($branchId, $perPage);
    }

    public function getStock(int $branchId, int $productId): ?BranchProduct
    {
        return $this->branchStockRepository->getStock($branchId, $productId);
    }

    public function updateStock(int $branchId, int $productId, int $quantity): BranchProduct
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Stock quantity cannot be negative.');
        }

        return $this->branchStockRepository->updateStock($branchId, $productId, $quantity);
    }

    public function adjustStock(int $branchId, int $productId, int $adjustment): BranchProduct
    {
        $stock = $this->branchStockRepository->getStock($branchId, $productId);
        if (!$stock) {
            throw new \InvalidArgumentException('Product not found in this branch inventory.');
        }

        $newQuantity = $stock->stock_quantity + $adjustment;
        if ($newQuantity < 0) {
            throw new \InvalidArgumentException('Insufficient stock for adjustment.');
        }

        return $this->branchStockRepository->adjustStock($branchId, $productId, $adjustment);
    }

    public function getLowStockProducts(int $branchId, int $threshold = 10): Collection
    {
        return $this->branchStockRepository->getLowStockProducts($branchId, $threshold);
    }

    public function addProductToBranch(int $branchId, int $productId, int $initialQuantity = 0): BranchProduct
    {
        if ($initialQuantity < 0) {
            throw new \InvalidArgumentException('Initial quantity cannot be negative.');
        }

        // Check if already exists
        $existing = $this->branchStockRepository->getStock($branchId, $productId);
        if ($existing) {
            throw new \InvalidArgumentException('Product already exists in this branch.');
        }

        return $this->branchStockRepository->updateStock($branchId, $productId, $initialQuantity);
    }

    public function getStockDetails(int $branchId, int $productId): BranchProduct
    {
        $stock = $this->branchStockRepository->getStock($branchId, $productId);
        if (!$stock) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Product not found in this branch inventory.');
        }
        return $stock->load('product');
    }

    public function getAllInventoryPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->branchStockRepository->getAllInventoryPaginated($perPage);
    }
}