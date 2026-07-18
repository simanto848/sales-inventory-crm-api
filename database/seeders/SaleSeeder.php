<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchProduct;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::where('is_active', true)->get();
        $branches = Branch::all();
        $employees = User::where('role', 'employee')->get();
        $products = Product::all();

        if ($customers->isEmpty() || $branches->isEmpty() || $employees->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Missing required data for sales seeding.');
            return;
        }

        for ($i = 0; $i < 50; $i++) {
            $customer = $customers->random();
            $branch = $branches->random();
            $employee = $employees->random();
            $saleDate = Carbon::now()->subDays(rand(0, 180));

            // Select 1-5 random products for this sale
            $saleProducts = $products->random(rand(1, 5));
            $items = [];
            $subtotal = 0;

            foreach ($saleProducts as $product) {
                // Check stock availability at branch
                $branchProduct = BranchProduct::where('branch_id', $branch->id)
                    ->where('product_id', $product->id)
                    ->first();

                if (!$branchProduct || $branchProduct->stock_quantity <= 0) {
                    continue;
                }

                $quantity = rand(1, min(3, $branchProduct->stock_quantity));
                $unitPrice = $product->price;
                $discount = rand(0, 1) ? 0 : round($unitPrice * 0.1, 2);
                $totalPrice = ($unitPrice * $quantity) - $discount;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'total_price' => $totalPrice,
                ];

                $subtotal += $totalPrice;

                // Deduct stock
                $branchProduct->decrement('stock_quantity', $quantity);
            }

            if (empty($items)) {
                continue;
            }

            $taxAmount = round($subtotal * 0.08, 2); // 8% tax
            $discountAmount = 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            $paymentStatuses = ['pending', 'paid', 'partial', 'refunded'];
            $paymentMethods = ['cash', 'card', 'bank_transfer', 'other'];

            $sale = Sale::create([
                'invoice_number' => 'INV-' . $saleDate->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'employee_id' => $employee->id,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'notes' => 'Sample sale for testing',
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ]);

            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'branch_id' => $branch->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'discount' => $item['discount'],
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                ]);
            }

            // Update customer purchase history
            $customer->increment('purchase_frequency');
            $customer->update([
                'last_purchase_date' => $saleDate->toDateString(),
                'is_active' => true,
            ]);

            // Update employee KPI if customer was assigned
            if ($customer->assigned_employee_id && $customer->assigned_employee_id === $employee->id) {
                $employee->increment('kpi_score', 10);
            }
        }
    }
}