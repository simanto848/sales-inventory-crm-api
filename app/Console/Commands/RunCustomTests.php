<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Branch;
use App\Models\Product;
use App\Models\BranchProduct;

#[Signature('app:run-custom-tests {phase=all}')]
#[Description('Run custom programmatic api integration tests')]
class RunCustomTests extends Command
{
    private int $testsRun = 0;
    private int $testsPassed = 0;
    private int $testsFailed = 0;
    private $kernel;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phase = $this->argument('phase');
        $this->info("Starting custom integration tests for phase: {$phase}");

        // Bootstrap the HTTP kernel
        $this->kernel = app(\Illuminate\Contracts\Http\Kernel::class);

        // Reset database
        $this->info("Refreshing database and seeding...");
        Artisan::call('migrate:fresh');
        Artisan::call('db:seed');

        if ($phase === '1' || $phase === 'all') {
            $this->runPhase1();
        }

        $this->line("");
        $this->info("Test Run Summary:");
        $this->line("Total Tests Run: {$this->testsRun}");
        $this->line("Passed: " . ($this->testsPassed > 0 ? "\033[32m{$this->testsPassed}\033[0m" : "0"));
        $this->line("Failed: " . ($this->testsFailed > 0 ? "\033[31m{$this->testsFailed}\033[0m" : "0"));

        return $this->testsFailed > 0 ? 1 : 0;
    }

    private function it(string $description, callable $callback)
    {
        $this->testsRun++;
        $this->output->write("Running test: {$description} ... ");
        try {
            $callback();
            $this->testsPassed++;
            $this->output->writeln("\033[32mPASSED\033[0m");
        } catch (\Throwable $e) {
            $this->testsFailed++;
            $this->output->writeln("\033[31mFAILED\033[0m");
            $this->error("Error: " . $e->getMessage());
            $this->line($e->getTraceAsString());
            $this->line("");
        }
    }

    private function assertEqual($actual, $expected, string $message = '')
    {
        if ($actual !== $expected) {
            throw new \Exception("Assertion failed: expected " . var_export($expected, true) . ", got " . var_export($actual, true) . ". {$message}");
        }
    }

    private function assertTrue($condition, string $message = '')
    {
        if (!$condition) {
            throw new \Exception("Assertion failed: condition is not true. {$message}");
        }
    }

    private function assertStatus($response, int $status)
    {
        $this->assertEqual($response->getStatusCode(), $status, "Response status is not {$status}. Body: " . $response->getContent());
    }

    private function dispatch(string $method, string $uri, array $params = [], ?string $token = null)
    {
        // Reset cached authentication state and request singleton for process isolation
        \Illuminate\Support\Facades\Auth::forgetGuards();
        app()->forgetInstance('request');

        $server = [];
        if ($token) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        }

        // Spoof PUT/PATCH/DELETE methods inside POST request to bypass PHP stream reader blocks in sandbox
        if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE'])) {
            $params['_method'] = strtoupper($method);
            $method = 'POST';
        }

        $request = Request::create($uri, $method, $params, [], [], $server);
        $response = $this->kernel->handle($request);
        $this->kernel->terminate($request, $response);
        return $response;
    }

    private function runPhase1()
    {
        $this->comment("\n--- Phase 1: Authentication, Products & Branch stock ---");

        $this->it('logs in successfully with correct credentials', function () {
            $response = $this->dispatch('POST', '/api/auth/login', [
                'email' => 'admin@example.com',
                'password' => 'password',
            ]);
            $this->assertStatus($response, 200);
            $data = json_decode($response->getContent(), true);
            $this->assertTrue(isset($data['access_token']), 'Token not present');
        });

        $this->it('fails login with incorrect credentials', function () {
            $response = $this->dispatch('POST', '/api/auth/login', [
                'email' => 'admin@example.com',
                'password' => 'wrong-password',
            ]);
            $this->assertStatus($response, 422);
        });

        $this->it('allows authenticated profile view and logout', function () {
            // Login to get token
            $loginRes = $this->dispatch('POST', '/api/auth/login', [
                'email' => 'employee@example.com',
                'password' => 'password',
            ]);
            $token = json_decode($loginRes->getContent(), true)['access_token'];

            // Get Profile
            $meRes = $this->dispatch('GET', '/api/auth/me', [], $token);
            $this->assertStatus($meRes, 200);
            $meData = json_decode($meRes->getContent(), true);
            $this->assertEqual($meData['email'], 'employee@example.com');
            $this->assertEqual($meData['role'], 'employee');

            // Logout
            $logoutRes = $this->dispatch('POST', '/api/auth/logout', [], $token);
            $this->assertStatus($logoutRes, 200);
        });

        $this->it('allows admins to manage branches but restricts employees', function () {
            $this->info("1. Log in admin");
            // Admin token
            $adminLogin = $this->dispatch('POST', '/api/auth/login', [
                'email' => 'admin@example.com',
                'password' => 'password',
            ]);
            $adminToken = json_decode($adminLogin->getContent(), true)['access_token'];

            $this->info("2. Log in employee");
            // Employee token
            $empLogin = $this->dispatch('POST', '/api/auth/login', [
                'email' => 'employee@example.com',
                'password' => 'password',
            ]);
            $empToken = json_decode($empLogin->getContent(), true)['access_token'];

            $this->info("3. Admin creates branch");
            // Admin creates branch
            $createRes = $this->dispatch('POST', '/api/branches', [
                'name' => 'Sylhet Branch',
                'location' => 'Sylhet',
            ], $adminToken);
            $this->assertStatus($createRes, 201);
            $branch = json_decode($createRes->getContent(), true);
            $branchId = $branch['id'];

            $this->info("4. Employee tries to update");
            // Employee tries to update and gets 403
            $empUpdateRes = $this->dispatch('PUT', "/api/branches/{$branchId}", [
                'name' => 'Sylhet Updated',
            ], $empToken);
            $this->assertStatus($empUpdateRes, 403);

            $this->info("5. Admin updates branch");
            // Admin updates branch
            $adminUpdateRes = $this->dispatch('PUT', "/api/branches/{$branchId}", [
                'name' => 'Sylhet Updated Name',
            ], $adminToken);
            $this->assertStatus($adminUpdateRes, 200);
            $this->assertEqual(json_decode($adminUpdateRes->getContent(), true)['name'], 'Sylhet Updated Name');

            $this->info("6. Employee tries to delete");
            // Employee tries to delete and gets 403
            $empDeleteRes = $this->dispatch('DELETE', "/api/branches/{$branchId}", [], $empToken);
            $this->assertStatus($empDeleteRes, 403);

            $this->info("7. Admin deletes branch");
            // Admin deletes branch
            $adminDeleteRes = $this->dispatch('DELETE', "/api/branches/{$branchId}", [], $adminToken);
            $this->assertStatus($adminDeleteRes, 200);
        });

        $this->it('allows admins to manage products but restricts employees', function () {
            // Admin token
            $adminLogin = $this->dispatch('POST', '/api/auth/login', [
                'email' => 'admin@example.com',
                'password' => 'password',
            ]);
            $adminToken = json_decode($adminLogin->getContent(), true)['access_token'];

            // Employee token
            $empLogin = $this->dispatch('POST', '/api/auth/login', [
                'email' => 'employee@example.com',
                'password' => 'password',
            ]);
            $empToken = json_decode($empLogin->getContent(), true)['access_token'];

            // Admin creates product
            $createRes = $this->dispatch('POST', '/api/products', [
                'name' => 'Tablet (iPad Pro)',
                'sku' => 'IPAD-PRO',
                'price' => 799.99,
            ], $adminToken);
            $this->assertStatus($createRes, 201);
            $product = json_decode($createRes->getContent(), true);
            $productId = $product['id'];

            // Employee tries to update and gets 403
            $empUpdateRes = $this->dispatch('PUT', "/api/products/{$productId}", [
                'name' => 'iPad Pro Gen 2',
            ], $empToken);
            $this->assertStatus($empUpdateRes, 403);

            // Admin updates product
            $adminUpdateRes = $this->dispatch('PUT', "/api/products/{$productId}", [
                'name' => 'iPad Pro Gen 2',
            ], $adminToken);
            $this->assertStatus($adminUpdateRes, 200);
            $this->assertEqual(json_decode($adminUpdateRes->getContent(), true)['name'], 'iPad Pro Gen 2');

            // Employee tries to delete and gets 403
            $empDeleteRes = $this->dispatch('DELETE', "/api/products/{$productId}", [], $empToken);
            $this->assertStatus($empDeleteRes, 403);

            // Admin deletes product
            $adminDeleteRes = $this->dispatch('DELETE', "/api/products/{$productId}", [], $adminToken);
            $this->assertStatus($adminDeleteRes, 200);
        });

        $this->it('allows updating and viewing branch stock', function () {
            // Employee token
            $empLogin = $this->dispatch('POST', '/api/auth/login', [
                'email' => 'employee@example.com',
                'password' => 'password',
            ]);
            $empToken = json_decode($empLogin->getContent(), true)['access_token'];

            // Get first branch and product
            $branch = Branch::first();
            $product = Product::first();

            // Update Stock to 50
            $updateRes = $this->dispatch('POST', "/api/branches/{$branch->id}/inventory", [
                'product_id' => $product->id,
                'stock_quantity' => 50,
            ], $empToken);
            $this->assertStatus($updateRes, 200);

            // Verify in database
            $dbRecord = BranchProduct::where('branch_id', $branch->id)
                ->where('product_id', $product->id)
                ->first();
            $this->assertEqual($dbRecord->stock_quantity, 50);

            // Retrieve inventory list
            $listRes = $this->dispatch('GET', "/api/branches/{$branch->id}/inventory", [], $empToken);
            $this->assertStatus($listRes, 200);
            $listData = json_decode($listRes->getContent(), true);
            $found = false;
            foreach ($listData as $item) {
                if ($item['product_id'] == $product->id) {
                    $this->assertEqual($item['stock_quantity'], 50);
                    $found = true;
                }
            }
            $this->assertTrue($found, 'Product stock not found in index response');
        });
    }
}
