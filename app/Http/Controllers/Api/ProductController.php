<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    use ApiResponser;

    public function __construct(
        protected ProductService $productService
    ) {}

    public function listProducts(): JsonResponse
    {
        return $this->success(
            $this->productService->getAllProducts((int) request()->get('per_page', 15)),
            'Products retrieved successfully'
        );
    }

    public function createProduct(StoreProductRequest $request): JsonResponse
    {
        return $this->success(
            $this->productService->createProduct($request->validated()),
            'Product created successfully',
            201
        );
    }

    public function getProductDetails(Product $product): JsonResponse
    {
        return $this->success($product, 'Product retrieved successfully');
    }

    public function updateProduct(UpdateProductRequest $request, Product $product): JsonResponse
    {
        return $this->success(
            $this->productService->updateProduct($product, $request->validated()),
            'Product updated successfully'
        );
    }

    public function deleteProduct(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);
        return $this->success(null, 'Product deleted successfully');
    }

    public function searchProducts(): JsonResponse
    {
        return $this->success(
            $this->productService->searchProducts(
                request()->get('q', ''),
                (int) request()->get('per_page', 15)
            ),
            'Search results retrieved successfully'
        );
    }
}