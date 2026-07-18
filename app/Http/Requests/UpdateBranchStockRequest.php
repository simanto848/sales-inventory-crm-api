<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin' || $this->user()?->role === 'manager';
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'stock_quantity' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.min' => 'Stock quantity cannot be negative.',
        ];
    }
}