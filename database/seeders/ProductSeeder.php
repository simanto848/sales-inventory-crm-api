<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'iPhone 15 Pro', 'sku' => 'IPH15P-128', 'price' => 999.00],
            ['name' => 'iPhone 15', 'sku' => 'IPH15-128', 'price' => 799.00],
            ['name' => 'MacBook Pro 14"', 'sku' => 'MBP14-M3', 'price' => 1999.00],
            ['name' => 'MacBook Air 13"', 'sku' => 'MBA13-M2', 'price' => 1099.00],
            ['name' => 'iPad Pro 12.9"', 'sku' => 'IPADP12-M2', 'price' => 1099.00],
            ['name' => 'Apple Watch Ultra 2', 'sku' => 'AWU2-49', 'price' => 799.00],
            ['name' => 'AirPods Pro 2', 'sku' => 'APP2-WHT', 'price' => 249.00],
            ['name' => 'Magic Keyboard', 'sku' => 'MK-SPACE', 'price' => 199.00],
            ['name' => 'Magic Mouse', 'sku' => 'MM-BLACK', 'price' => 99.00],
            ['name' => 'USB-C Cable 2m', 'sku' => 'USBC-2M', 'price' => 29.00],
            ['name' => '30W USB-C Adapter', 'sku' => 'ADPT-30W', 'price' => 39.00],
            ['name' => 'MagSafe Charger', 'sku' => 'MSC-WHT', 'price' => 39.00],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }

        // Add stock to branches
        $branches = Branch::all();
        $productsList = Product::all();

        foreach ($branches as $branch) {
            foreach ($productsList as $product) {
                $quantity = fake()->numberBetween(0, 50);
                BranchProduct::firstOrCreate(
                    ['branch_id' => $branch->id, 'product_id' => $product->id],
                    ['stock_quantity' => $quantity]
                );
            }
        }
    }
}