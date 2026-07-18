<?php

use App\Models\Branch;
use App\Models\BranchProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'login-test@example.com',
        'password' => bcrypt('password123'),
        'role' => 'employee',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'login-test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'user' => ['id', 'name', 'email', 'role', 'kpi_score']
        ]);
});

test('user cannot login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'login-test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'login-test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('authenticated user can view profile and logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/auth/me');
    $response->assertStatus(200)
        ->assertJsonPath('email', $user->email);

    $logoutResponse = $this->actingAs($user, 'sanctum')->postJson('/api/auth/logout');
    $logoutResponse->assertStatus(200)
        ->assertJson(['message' => 'Successfully logged out.']);
});

test('admin can manage branches', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Create
    $response = $this->actingAs($admin, 'sanctum')->postJson('/api/branches', [
        'name' => 'Test Branch',
        'location' => 'Dhaka',
    ]);
    $response->assertStatus(201)
        ->assertJsonPath('name', 'Test Branch');

    $branchId = $response->json('id');

    // List
    $listResponse = $this->actingAs($admin, 'sanctum')->getJson('/api/branches');
    $listResponse->assertStatus(200)
        ->assertJsonFragment(['name' => 'Test Branch']);

    // Show
    $showResponse = $this->actingAs($admin, 'sanctum')->getJson("/api/branches/{$branchId}");
    $showResponse->assertStatus(200)
        ->assertJsonPath('name', 'Test Branch');

    // Update
    $updateResponse = $this->actingAs($admin, 'sanctum')->putJson("/api/branches/{$branchId}", [
        'name' => 'Updated Branch Name',
    ]);
    $updateResponse->assertStatus(200)
        ->assertJsonPath('name', 'Updated Branch Name');

    // Delete
    $deleteResponse = $this->actingAs($admin, 'sanctum')->deleteJson("/api/branches/{$branchId}");
    $deleteResponse->assertStatus(200);

    $this->assertDatabaseMissing('branches', ['id' => $branchId]);
});

test('employee cannot manage branches', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $branch = Branch::create(['name' => 'Pre-existing Branch']);

    $this->actingAs($employee, 'sanctum')
        ->postJson('/api/branches', ['name' => 'New Branch'])
        ->assertStatus(403);

    $this->actingAs($employee, 'sanctum')
        ->putJson("/api/branches/{$branch->id}", ['name' => 'New Name'])
        ->assertStatus(403);

    $this->actingAs($employee, 'sanctum')
        ->deleteJson("/api/branches/{$branch->id}")
        ->assertStatus(403);
});

test('admin can manage products', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Create
    $response = $this->actingAs($admin, 'sanctum')->postJson('/api/products', [
        'name' => 'Test Product',
        'sku' => 'TEST-SKU-1',
        'price' => 99.99,
    ]);
    $response->assertStatus(201)
        ->assertJsonPath('sku', 'TEST-SKU-1');

    $productId = $response->json('id');

    // List
    $listResponse = $this->actingAs($admin, 'sanctum')->getJson('/api/products');
    $listResponse->assertStatus(200)
        ->assertJsonFragment(['sku' => 'TEST-SKU-1']);

    // Show
    $showResponse = $this->actingAs($admin, 'sanctum')->getJson("/api/products/{$productId}");
    $showResponse->assertStatus(200)
        ->assertJsonPath('sku', 'TEST-SKU-1');

    // Update
    $updateResponse = $this->actingAs($admin, 'sanctum')->putJson("/api/products/{$productId}", [
        'name' => 'Updated Product Name',
        'price' => 120.00,
    ]);
    $updateResponse->assertStatus(200)
        ->assertJsonPath('name', 'Updated Product Name')
        ->assertJsonPath('price', '120.00');

    // Delete
    $deleteResponse = $this->actingAs($admin, 'sanctum')->deleteJson("/api/products/{$productId}");
    $deleteResponse->assertStatus(200);

    $this->assertDatabaseMissing('products', ['id' => $productId]);
});

test('employee cannot manage products', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $product = Product::create(['name' => 'Pre-existing Product', 'sku' => 'PRE-SKU-123', 'price' => 50]);

    $this->actingAs($employee, 'sanctum')
        ->postJson('/api/products', ['name' => 'New Product', 'sku' => 'NEW-SKU-456', 'price' => 10])
        ->assertStatus(403);

    $this->actingAs($employee, 'sanctum')
        ->putJson("/api/products/{$product->id}", ['name' => 'New Name'])
        ->assertStatus(403);

    $this->actingAs($employee, 'sanctum')
        ->deleteJson("/api/products/{$product->id}")
        ->assertStatus(403);
});

test('user can get and update branch inventory', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $branch = Branch::create(['name' => 'Dhaka Office']);
    $product = Product::create(['name' => 'Dell XPS', 'sku' => 'DELL-1', 'price' => 1000]);

    // Update Stock
    $response = $this->actingAs($employee, 'sanctum')->postJson("/api/branches/{$branch->id}/inventory", [
        'product_id' => $product->id,
        'stock_quantity' => 20,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('stock.stock_quantity', 20);

    $this->assertDatabaseHas('branch_products', [
        'branch_id' => $branch->id,
        'product_id' => $product->id,
        'stock_quantity' => 20,
    ]);

    // Get Stock
    $getResponse = $this->actingAs($employee, 'sanctum')->getJson("/api/branches/{$branch->id}/inventory");
    $getResponse->assertStatus(200)
        ->assertJsonFragment(['stock_quantity' => 20]);
});
