<?php

namespace App\Repositories\Contracts;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BranchRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Branch;
    public function create(array $data): Branch;
    public function update(Branch $branch, array $data): Branch;
    public function delete(Branch $branch): bool;
}