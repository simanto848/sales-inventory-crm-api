<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\User;
use App\Services\EmployeeService;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    use ApiResponser;

    public function __construct(
        protected EmployeeService $employeeService
    ) {}

    public function listEmployees(): JsonResponse
    {
        return $this->success(
            $this->employeeService->getAllEmployees((int) request()->get('per_page', 15)),
            'Employees retrieved successfully'
        );
    }

    public function createEmployee(StoreEmployeeRequest $request): JsonResponse
    {
        return $this->success(
            $this->employeeService->createEmployee($request->validated()),
            'Employee created successfully',
            201
        );
    }

    public function getEmployeeDetails(User $employee): JsonResponse
    {
        return $this->success(
            $this->employeeService->getEmployeeDetails($employee),
            'Employee retrieved successfully'
        );
    }

    public function updateEmployee(UpdateEmployeeRequest $request, User $employee): JsonResponse
    {
        return $this->success(
            $this->employeeService->updateEmployee($employee, $request->validated()),
            'Employee updated successfully'
        );
    }

    public function deleteEmployee(User $employee): JsonResponse
    {
        $this->employeeService->deleteEmployee($employee);
        return $this->success(null, 'Employee deleted successfully');
    }

    public function getTopPerformers(): JsonResponse
    {
        return $this->success(
            $this->employeeService->getTopPerformers((int) request()->get('limit', 10)),
            'Top performing employees retrieved successfully'
        );
    }

    public function getEmployeeKpi(int $employeeId): JsonResponse
    {
        return $this->success(
            ['kpi_score' => $this->employeeService->getEmployeeKpi($employeeId)],
            'Employee KPI retrieved successfully'
        );
    }
}