<?php

namespace App\Repositories\Eloquent;

use App\Models\Branch;
use App\Repositories\Contracts\BranchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BranchRepository implements BranchRepositoryInterface
{
    public function all(): Collection
    {
        return Branch::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Branch::paginate($perPage);
    }

    public function findById(int $id): ?Branch
    {
        return Branch::find($id);
    }

    public function create(array $data): Branch
    {
        return Branch::create($data);
    }

    public function update(Branch $branch, array $data): Branch
    {
        $branch->update($data);
        return $branch->fresh();
    }

    public function delete(Branch $branch): bool
    {
        return $branch->delete();
    }
}