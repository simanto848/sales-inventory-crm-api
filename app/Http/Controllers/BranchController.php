<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Branch::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $branch = Branch::create($validated);

        return response()->json($branch, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch): JsonResponse
    {
        return response()->json($branch);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $branch->update($validated);

        return response()->json($branch);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Branch $branch): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $branch->delete();

        return response()->json(['message' => 'Branch deleted successfully.']);
    }
}
