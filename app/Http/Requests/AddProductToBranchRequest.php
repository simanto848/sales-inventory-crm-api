<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProductToBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin' || $this->user()?->role === 'manager';
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'initial_quantity' => 'sometimes|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'initial_quantity.min' => 'Initial quantity cannot be negative.',
        ];
    }
}