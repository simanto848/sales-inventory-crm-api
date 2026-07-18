<?php

namespace App\Repositories\Contracts;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SaleRepositoryInterface
{
    public function all(array $columns = ['*']): Collection;
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;
    public function findById(int $id, array $columns = ['*']): ?Sale;
    public function findByInvoiceNumber(string $invoiceNumber): ?Sale;
    public function create(array $data): Sale;
    public function update(Sale $sale, array $data): Sale;
    public function delete(Sale $sale): bool;
    public function getByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator;
    public function getByBranch(int $branchId, int $perPage = 15): LengthAwarePaginator;
    public function getByDateRange(\Carbon\Carbon $start, \Carbon\Carbon $end, int $perPage = 15): LengthAwarePaginator;
}