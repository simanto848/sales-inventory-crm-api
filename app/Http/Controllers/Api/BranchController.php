<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use App\Services\BranchService;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    use ApiResponser;

    public function __construct(
        protected BranchService $branchService
    ) {}

    public function listBranches(): JsonResponse
    {
        $user = request()->user();
        if ($user && $user->branch_id) {
            $branches = Branch::where('id', $user->branch_id)->paginate((int) request()->get('per_page', 15));
            return $this->success($branches, 'Branches retrieved successfully');
        }

        return $this->success(
            $this->branchService->getAllBranches((int) request()->get('per_page', 15)),
            'Branches retrieved successfully'
        );
    }

    public function createBranch(StoreBranchRequest $request): JsonResponse
    {
        return $this->success(
            $this->branchService->createBranch($request->validated()),
            'Branch created successfully',
            201
        );
    }

    public function getBranchDetails(Branch $branch): JsonResponse
    {
        return $this->success($branch, 'Branch retrieved successfully');
    }

    public function updateBranch(UpdateBranchRequest $request, Branch $branch): JsonResponse
    {
        return $this->success(
            $this->branchService->updateBranch($branch, $request->validated()),
            'Branch updated successfully'
        );
    }

    public function deleteBranch(Branch $branch): JsonResponse
    {
        $this->branchService->deleteBranch($branch);
        return $this->success(null, 'Branch deleted successfully');
    }
}