<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\BranchProduct;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class SaleService
{
    public function __construct(
        protected SaleRepositoryInterface $saleRepository
    ) {}

    public function getAllSales(int $perPage = 15): LengthAwarePaginator
    {
        return $this->saleRepository->paginate($perPage);
    }

    public function getSaleById(int $id): ?Sale
    {
        return $this->saleRepository->findById($id);
    }

    public function getSaleByInvoiceNumber(string $invoiceNumber): ?Sale
    {
        return $this->saleRepository->findByInvoiceNumber($invoiceNumber);
    }

    public function createSale(array $data): Sale
    {
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = $this->generateInvoiceNumber();
        }

        return DB::transaction(function () use ($data) {
            $itemsData = $data['items'] ?? [];
            unset($data['items']);

            // Validate stock availability for each item
            foreach ($itemsData as $item) {
                $branchProduct = BranchProduct::where('branch_id', $data['branch_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();

                if (!$branchProduct || $branchProduct->stock_quantity < $item['quantity']) {
                    throw new \InvalidArgumentException(
                        "Insufficient stock for product ID {$item['product_id']} at branch ID {$data['branch_id']}."
                    );
                }
            }

            // Create the sale
            $sale = $this->saleRepository->create($data);

            // Create sale items and deduct stock
            $subtotal = 0;
            foreach ($itemsData as $item) {
                $product = Product::findOrFail($item['product_id']);
                $unitPrice = $item['unit_price'] ?? $product->price;
                $quantity = $item['quantity'];
                $discount = $item['discount'] ?? 0;
                $totalPrice = ($unitPrice * $quantity) - $discount;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'branch_id' => $data['branch_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'discount' => $discount,
                ]);

                // Deduct stock
                $branchProduct = BranchProduct::where('branch_id', $data['branch_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();
                $branchProduct->decrement('stock_quantity', $quantity);

                $subtotal += $totalPrice;
            }

            // Update sale totals
            $sale->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal + ($data['tax_amount'] ?? 0) - ($data['discount_amount'] ?? 0),
            ]);

            // Update customer purchase history
            $this->updateCustomerPurchaseHistory($data['customer_id'], $sale);

            // Update employee KPI if customer was assigned
            $this->updateEmployeeKpi($sale);

            // Send Invoice Email to Customer
            try {
                if ($sale->customer && $sale->customer->email) {
                    Mail::to($sale->customer->email)->send(new InvoiceMail($sale));
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send invoice email for sale #{$sale->invoice_number}: " . $e->getMessage());
            }

            return $sale->fresh(['customer', 'branch', 'employee', 'items.product']);
        });
    }

    public function updateSale(Sale $sale, array $data): Sale
    {
        // For simplicity, we only allow updating non-financial fields
        return $this->saleRepository->update($sale, $data);
    }

    public function deleteSale(Sale $sale): bool
    {
        return $this->saleRepository->delete($sale);
    }

    public function getSalesByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->saleRepository->getByCustomer($customerId, $perPage);
    }

    public function getSalesByBranch(int $branchId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->saleRepository->getByBranch($branchId, $perPage);
    }

    public function getSalesByDateRange(\Carbon\Carbon $start, \Carbon\Carbon $end, int $perPage = 15): LengthAwarePaginator
    {
        return $this->saleRepository->getByDateRange($start, $end, $perPage);
    }

    protected function updateCustomerPurchaseHistory(int $customerId, Sale $sale): void
    {
        $customer = Customer::find($customerId);
        if (!$customer) return;

        $customer->update([
            'last_purchase_date' => $sale->created_at->toDateString(),
            'purchase_frequency' => $customer->purchase_frequency + 1,
            'is_active' => true,
        ]);

        // If customer was assigned to an employee and made a purchase, increase KPI
        if ($customer->assigned_employee_id) {
            $employee = User::find($customer->assigned_employee_id);
            if ($employee && $employee->role === 'employee') {
                $employee->increment('kpi_score', 10);
            }
        }
    }

    protected function updateEmployeeKpi(Sale $sale): void
    {
        $customer = $sale->customer;
        if ($customer && $customer->assigned_employee_id) {
            $employee = User::find($customer->assigned_employee_id);
            if ($employee && $employee->role === 'employee') {
                $employee->increment('kpi_score', 10);
            }
        }
    }

    public function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $lastSale = Sale::whereDate('created_at', today())->latest('id')->first();
        $sequence = $lastSale ? (int)substr($lastSale->invoice_number, -4) + 1 : 1;
        return "INV-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}