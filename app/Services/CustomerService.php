<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReEngagementMail;

class CustomerService
{
    public function __construct(
        protected CustomerRepositoryInterface $customerRepository
    ) {}

    public function getAllCustomers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->customerRepository->paginate($perPage);
    }

    public function getCustomerById(int $id): ?Customer
    {
        return $this->customerRepository->findById($id);
    }

    public function getCustomerByEmail(string $email): ?Customer
    {
        return $this->customerRepository->findByEmail($email);
    }

    public function createCustomer(array $data): Customer
    {
        if ($this->customerRepository->findByEmail($data['email'])) {
            throw new \InvalidArgumentException('Customer with this email already exists.');
        }

        return $this->customerRepository->create($data);
    }

    public function updateCustomer(Customer $customer, array $data): Customer
    {
        if (isset($data['email']) && $data['email'] !== $customer->email) {
            if ($this->customerRepository->findByEmail($data['email'])) {
                throw new \InvalidArgumentException('Customer with this email already exists.');
            }
        }

        return $this->customerRepository->update($customer, $data);
    }

    public function deleteCustomer(Customer $customer): bool
    {
        return $this->customerRepository->delete($customer);
    }

    public function getInactiveCustomers(int $days = 90): Collection
    {
        return $this->customerRepository->getInactiveCustomers($days);
    }

    public function assignToEmployee(Customer $customer, int $employeeId): Customer
    {
        $employee = User::find($employeeId);
        if (!$employee || $employee->role !== 'employee') {
            throw new \InvalidArgumentException('Invalid employee selected.');
        }

        return $this->customerRepository->update($customer, ['assigned_employee_id' => $employeeId]);
    }

    public function unassignEmployee(Customer $customer): Customer
    {
        return $this->customerRepository->update($customer, ['assigned_employee_id' => null]);
    }

    public function getAssignedCustomers(int $employeeId): Collection
    {
        return $this->customerRepository->getAssignedToEmployee($employeeId);
    }

    public function searchCustomers(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->customerRepository->search($query, $perPage);
    }

    public function sendReEngagementNotification(int $customerId, string $message, string $channel = 'email'): bool
    {
        $customer = $this->getCustomerById($customerId);
        if (!$customer) {
            throw new \InvalidArgumentException('Customer not found.');
        }

        if ($channel === 'email') {
            Mail::to($customer->email)->send(new ReEngagementMail($message));
        }

        // We also log the action for audit trail / SMS simulation
        \Log::info("Re-engagement {$channel} sent to customer {$customer->email}", [
            'customer_id' => $customerId,
            'message' => $message,
            'channel' => $channel,
        ]);

        return true;
    }

    public function getSalesByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return Sale::query()
            ->where('customer_id', $customerId)
            ->with(['branch', 'employee', 'items.product'])
            ->latest()
            ->paginate($perPage);
    }
}