<?php

namespace App\Repositories\Contracts;

use App\Models\BranchProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BranchStockRepositoryInterface
{
    public function getBranchInventory(int $branchId): Collection;
    public function getBranchInventoryPaginated(int $branchId, int $perPage = 15): LengthAwarePaginator;
    public function getStock(int $branchId, int $productId): ?BranchProduct;
    public function updateStock(int $branchId, int $productId, int $quantity): BranchProduct;
    public function adjustStock(int $branchId, int $productId, int $adjustment): BranchProduct;
    public function getLowStockProducts(int $branchId, int $threshold = 10): Collection;
    public function getAllInventoryPaginated(int $perPage = 15): LengthAwarePaginator;
}