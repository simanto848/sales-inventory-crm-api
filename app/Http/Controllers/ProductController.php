<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Product::all());
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
            'sku' => 'required|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
