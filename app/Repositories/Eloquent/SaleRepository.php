<?php

namespace App\Repositories\Eloquent;

use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SaleRepository implements SaleRepositoryInterface
{
    public function all(array $columns = ['*']): Collection
    {
        return Sale::query()->with(['customer', 'branch', 'employee', 'items.product'])->get($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return Sale::query()->with(['customer', 'branch', 'employee', 'items.product'])->paginate($perPage, $columns);
    }

    public function findById(int $id, array $columns = ['*']): ?Sale
    {
        return Sale::query()->with(['customer', 'branch', 'employee', 'items.product'])->find($id, $columns);
    }

    public function findByInvoiceNumber(string $invoiceNumber): ?Sale
    {
        return Sale::query()->with(['customer', 'branch', 'employee', 'items.product'])
            ->where('invoice_number', $invoiceNumber)
            ->first();
    }

    public function create(array $data): Sale
    {
        return Sale::create($data);
    }

    public function update(Sale $sale, array $data): Sale
    {
        $sale->update($data);
        return $sale->fresh(['customer', 'branch', 'employee', 'items.product']);
    }

    public function delete(Sale $sale): bool
    {
        return $sale->delete();
    }

    public function getByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return Sale::query()->where('customer_id', $customerId)
            ->with(['customer', 'branch', 'employee', 'items.product'])
            ->paginate($perPage);
    }

    public function getByBranch(int $branchId, int $perPage = 15): LengthAwarePaginator
    {
        return Sale::query()->where('branch_id', $branchId)
            ->with(['customer', 'branch', 'employee', 'items.product'])
            ->paginate($perPage);
    }

    public function getByDateRange(Carbon $start, Carbon $end, int $perPage = 15): LengthAwarePaginator
    {
        return Sale::query()->whereBetween('created_at', [$start, $end])
            ->with(['customer', 'branch', 'employee', 'items.product'])
            ->paginate($perPage);
    }
}