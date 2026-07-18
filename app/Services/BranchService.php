<?php

namespace App\Services;

use App\Models\Branch;
use App\Repositories\Contracts\BranchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BranchService
{
    public function __construct(
        protected BranchRepositoryInterface $branchRepository
    ) {}

    public function getAllBranches(int $perPage = 15): LengthAwarePaginator
    {
        return $this->branchRepository->paginate($perPage);
    }

    public function getBranchById(int $id): ?Branch
    {
        return $this->branchRepository->findById($id);
    }

    public function createBranch(array $data): Branch
    {
        return $this->branchRepository->create($data);
    }

    public function updateBranch(Branch $branch, array $data): Branch
    {
        return $this->branchRepository->update($branch, $data);
    }

    public function deleteBranch(Branch $branch): bool
    {
        return $this->branchRepository->delete($branch);
    }
}