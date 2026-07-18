<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmployeeService
{
    public function __construct(
        protected EmployeeRepositoryInterface $employeeRepository
    ) {}

    public function getAllEmployees(int $perPage = 15): LengthAwarePaginator
    {
        return $this->employeeRepository->paginateEmployees($perPage);
    }

    public function getEmployeeById(int $id): ?User
    {
        return $this->employeeRepository->findEmployeeById($id);
    }

    public function createEmployee(array $data): User
    {
        if (User::where('email', $data['email'])->exists()) {
            throw new \InvalidArgumentException('Employee with this email already exists.');
        }

        return $this->employeeRepository->createEmployee($data);
    }

    public function getEmployeeDetails(User $employee): User
    {
        if ($employee->role !== 'employee') {
            throw new NotFoundHttpException('Employee not found.');
        }
        return $employee;
    }

    public function updateEmployee(User $employee, array $data): User
    {
        if ($employee->role !== 'employee') {
            throw new NotFoundHttpException('Employee not found.');
        }

        if (isset($data['email']) && $data['email'] !== $employee->email) {
            if (User::where('email', $data['email'])->exists()) {
                throw new \InvalidArgumentException('Employee with this email already exists.');
            }
        }

        return $this->employeeRepository->updateEmployee($employee, $data);
    }

    public function deleteEmployee(User $employee): bool
    {
        if ($employee->role !== 'employee') {
            throw new NotFoundHttpException('Employee not found.');
        }

        return $this->employeeRepository->deleteEmployee($employee);
    }

    public function getTopPerformers(int $limit = 10): Collection
    {
        return $this->employeeRepository->getTopPerformers($limit);
    }

    public function getEmployeeKpi(int $employeeId): int
    {
        return $this->employeeRepository->getEmployeeKpi($employeeId);
    }
}