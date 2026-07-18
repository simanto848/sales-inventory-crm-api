<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Requests\AssignEmployeeRequest;
use App\Http\Requests\SendReEngagementRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    use ApiResponser;

    public function __construct(
        protected CustomerService $customerService
    ) {}

    public function listCustomers(): JsonResponse
    {
        return $this->success(
            $this->customerService->getAllCustomers((int) request()->get('per_page', 15)),
            'Customers retrieved successfully'
        );
    }

    public function createCustomer(StoreCustomerRequest $request): JsonResponse
    {
        return $this->success(
            $this->customerService->createCustomer($request->validated()),
            'Customer created successfully',
            201
        );
    }

    public function getCustomerDetails(Customer $customer): JsonResponse
    {
        return $this->success($customer, 'Customer retrieved successfully');
    }

    public function updateCustomer(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        return $this->success(
            $this->customerService->updateCustomer($customer, $request->validated()),
            'Customer updated successfully'
        );
    }

    public function deleteCustomer(Customer $customer): JsonResponse
    {
        $this->customerService->deleteCustomer($customer);
        return $this->success(null, 'Customer deleted successfully');
    }

    public function listInactiveCustomers(): JsonResponse
    {
        return $this->success(
            $this->customerService->getInactiveCustomers((int) request()->get('days', 90)),
            'Inactive customers retrieved successfully'
        );
    }

    public function assignCustomerToEmployee(AssignEmployeeRequest $request, Customer $customer): JsonResponse
    {
        return $this->success(
            $this->customerService->assignToEmployee($customer, $request->employee_id),
            'Customer assigned to employee successfully'
        );
    }

    public function unassignCustomerFromEmployee(Customer $customer): JsonResponse
    {
        return $this->success(
            $this->customerService->unassignEmployee($customer),
            'Customer unassigned from employee successfully'
        );
    }

    public function listAssignedCustomers(Request $request): JsonResponse
    {
        $employeeId = $request->get('employee_id');
        if (!$employeeId) {
            throw new \InvalidArgumentException('Employee ID is required.');
        }

        return $this->success(
            $this->customerService->getAssignedCustomers((int) $employeeId),
            'Assigned customers retrieved successfully'
        );
    }

    public function sendCustomerReEngagement(SendReEngagementRequest $request, Customer $customer): JsonResponse
    {
        $this->customerService->sendReEngagementNotification(
            $customer->id,
            $request->message,
            $request->get('channel', 'email')
        );
        return $this->success(null, 'Re-engagement notification sent successfully.');
    }

    public function searchCustomers(): JsonResponse
    {
        return $this->success(
            $this->customerService->searchCustomers(
                request()->get('q', ''),
                (int) request()->get('per_page', 15)
            ),
            'Search results retrieved successfully'
        );
    }

    public function getCustomerPurchaseHistory(Customer $customer): JsonResponse
    {
        return $this->success(
            $this->customerService->getSalesByCustomer($customer->id, (int) request()->get('per_page', 15)),
            'Customer purchase history retrieved successfully'
        );
    }
}