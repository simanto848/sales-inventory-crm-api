<?php

namespace App\Repositories\Eloquent;

use App\Models\BranchProduct;
use App\Repositories\Contracts\BranchStockRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BranchStockRepository implements BranchStockRepositoryInterface
{
    public function getBranchInventory(int $branchId): Collection
    {
        return BranchProduct::query()
            ->where('branch_id', $branchId)
            ->with('product')
            ->get();
    }

    public function getBranchInventoryPaginated(int $branchId, int $perPage = 15): LengthAwarePaginator
    {
        return BranchProduct::query()
            ->where('branch_id', $branchId)
            ->with('product')
            ->paginate($perPage);
    }

    public function getStock(int $branchId, int $productId): ?BranchProduct
    {
        return BranchProduct::query()
            ->where('branch_id', $branchId)
            ->where('product_id', $productId)
            ->first();
    }

    public function updateStock(int $branchId, int $productId, int $quantity): BranchProduct
    {
        return BranchProduct::updateOrCreate(
            ['branch_id' => $branchId, 'product_id' => $productId],
            ['stock_quantity' => $quantity]
        );
    }

    public function adjustStock(int $branchId, int $productId, int $adjustment): BranchProduct
    {
        $stock = BranchProduct::where('branch_id', $branchId)
            ->where('product_id', $productId)
            ->firstOrFail();

        $stock->increment('stock_quantity', $adjustment);
        return $stock->fresh('product');
    }

    public function getLowStockProducts(int $branchId, int $threshold = 10): Collection
    {
        return BranchProduct::query()
            ->where('branch_id', $branchId)
            ->where('stock_quantity', '<=', $threshold)
            ->with('product')
            ->get();
    }

    public function getAllInventoryPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return BranchProduct::query()
            ->with(['branch', 'product'])
            ->paginate($perPage);
    }
}