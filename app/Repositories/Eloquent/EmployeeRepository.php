<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function allEmployees(array $columns = ['*']): Collection
    {
        return User::query()->where('role', 'employee')->get($columns);
    }

    public function paginateEmployees(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return User::query()->where('role', 'employee')->paginate($perPage, $columns);
    }

    public function findEmployeeById(int $id, array $columns = ['*']): ?User
    {
        return User::query()->where('role', 'employee')->find($id, $columns);
    }

    public function createEmployee(array $data): User
    {
        $data['role'] = 'employee';
        return User::create($data);
    }

    public function updateEmployee(User $employee, array $data): User
    {
        $employee->update($data);
        return $employee->fresh();
    }

    public function deleteEmployee(User $employee): bool
    {
        return $employee->delete();
    }

    public function getTopPerformers(int $limit = 10): Collection
    {
        return User::query()
            ->where('role', 'employee')
            ->orderByDesc('kpi_score')
            ->limit($limit)
            ->get();
    }

    public function getEmployeeKpi(int $employeeId): int
    {
        $employee = User::query()->where('role', 'employee')->find($employeeId);
        return $employee?->kpi_score ?? 0;
    }
}