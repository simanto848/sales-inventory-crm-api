<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeRepositoryInterface
{
    public function allEmployees(array $columns = ['*']): Collection;
    public function paginateEmployees(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;
    public function findEmployeeById(int $id, array $columns = ['*']): ?User;
    public function createEmployee(array $data): User;
    public function updateEmployee(User $employee, array $data): User;
    public function deleteEmployee(User $employee): bool;
    public function getTopPerformers(int $limit = 10): Collection;
    public function getEmployeeKpi(int $employeeId): int;
}