<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchStockController extends Controller
{
    /**
     * Get stock inventory for a branch.
     */
    public function index(Branch $branch): JsonResponse
    {
        $inventory = BranchProduct::with('product')
            ->where('branch_id', $branch->id)
            ->get();

        return response()->json($inventory);
    }

    /**
     * Update stock level for a product at a branch.
     */
    public function update(Request $request, Branch $branch): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $stock = BranchProduct::updateOrCreate(
            [
                'branch_id' => $branch->id,
                'product_id' => $validated['product_id'],
            ],
            [
                'stock_quantity' => $validated['stock_quantity'],
            ]
        );

        return response()->json([
            'message' => 'Stock updated successfully.',
            'stock' => $stock->load('product'),
        ]);
    }
}
