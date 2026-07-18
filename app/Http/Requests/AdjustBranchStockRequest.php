<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustBranchStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin' || $this->user()?->role === 'manager';
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'adjustment' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'adjustment.required' => 'Adjustment quantity is required.',
        ];
    }
}