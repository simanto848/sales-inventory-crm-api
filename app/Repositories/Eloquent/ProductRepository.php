<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(): Collection
    {
        return Product::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with('branches')->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return Product::with('branches')->find($id);
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::with('branches')->where('sku', $sku)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh('branches');
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with('branches')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->paginate($perPage);
    }
}