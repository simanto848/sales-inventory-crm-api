<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function all(array $columns = ['*']): Collection;
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;
    public function findById(int $id, array $columns = ['*']): ?Customer;
    public function findByEmail(string $email, array $columns = ['*']): ?Customer;
    public function create(array $data): Customer;
    public function update(Customer $customer, array $data): Customer;
    public function delete(Customer $customer): bool;
    public function getInactiveCustomers(int $days = 90): Collection;
    public function getAssignedToEmployee(int $employeeId): Collection;
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;
}