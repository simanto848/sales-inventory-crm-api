<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class ProductIntegrationController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of products formatted for e-commerce integration.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = Product::with('branches')->get()->map(function (Product $product) {
            return [
                'sku' => $product->sku,
                'product_name' => $product->name,
                'price' => $product->price,
                'available_stock' => $product->branches->sum('pivot.stock_quantity'),
            ];
        });

        return $this->success($products, 'Products retrieved successfully');
    }
}
