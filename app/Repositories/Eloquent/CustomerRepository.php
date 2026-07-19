<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function all(array $columns = ['*']): Collection
    {
        return Customer::query()->with('assignedEmployee')->get($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return Customer::query()->with('assignedEmployee')->paginate($perPage, $columns);
    }

    public function findById(int $id, array $columns = ['*']): ?Customer
    {
        return Customer::query()->with('assignedEmployee')->find($id, $columns);
    }

    public function findByEmail(string $email, array $columns = ['*']): ?Customer
    {
        return Customer::query()->with('assignedEmployee')->where('email', $email)->first($columns);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer->fresh('assignedEmployee');
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }

    public function getInactiveCustomers(int $days = 90): Collection
    {
        $cutoffDate = \Carbon\Carbon::now()->subDays($days);
        return Customer::query()
            ->where(function ($query) use ($cutoffDate) {
                $query->where('last_purchase_date', '<', $cutoffDate)
                      ->orWhereNull('last_purchase_date');
            })
            ->with('assignedEmployee')
            ->get();
    }

    public function getAssignedToEmployee(int $employeeId): Collection
    {
        return Customer::query()->where('assigned_employee_id', $employeeId)
            ->with('assignedEmployee')
            ->get();
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Customer::query()->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%");
        })->with('assignedEmployee')->paginate($perPage);
    }
}