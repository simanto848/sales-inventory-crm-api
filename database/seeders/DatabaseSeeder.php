<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Users
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'role' => 'employee',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        // Create Branches
        $dhaka = \App\Models\Branch::create([
            'name' => 'Dhaka Office',
            'location' => 'Dhaka, Bangladesh',
        ]);

        $chittagong = \App\Models\Branch::create([
            'name' => 'Chittagong Office',
            'location' => 'Chittagong, Bangladesh',
        ]);

        // Create Products
        $dell = \App\Models\Product::create([
            'name' => 'Laptop (Dell XPS)',
            'sku' => 'DELL-XPS-15',
            'price' => 1499.99,
        ]);

        $iphone = \App\Models\Product::create([
            'name' => 'Smartphone (iPhone 15)',
            'sku' => 'IPHONE-15',
            'price' => 999.99,
        ]);

        $sony = \App\Models\Product::create([
            'name' => 'Headphones (Sony WH-1000XM5)',
            'sku' => 'SONY-XM5',
            'price' => 349.99,
        ]);

        // Create Branch Products Stock
        \App\Models\BranchProduct::create([
            'branch_id' => $dhaka->id,
            'product_id' => $dell->id,
            'stock_quantity' => 10,
        ]);

        \App\Models\BranchProduct::create([
            'branch_id' => $chittagong->id,
            'product_id' => $dell->id,
            'stock_quantity' => 5,
        ]);

        \App\Models\BranchProduct::create([
            'branch_id' => $dhaka->id,
            'product_id' => $iphone->id,
            'stock_quantity' => 20,
        ]);

        \App\Models\BranchProduct::create([
            'branch_id' => $chittagong->id,
            'product_id' => $iphone->id,
            'stock_quantity' => 15,
        ]);

        \App\Models\BranchProduct::create([
            'branch_id' => $dhaka->id,
            'product_id' => $sony->id,
            'stock_quantity' => 30,
        ]);

        \App\Models\BranchProduct::create([
            'branch_id' => $chittagong->id,
            'product_id' => $sony->id,
            'stock_quantity' => 25,
        ]);
    }
}
