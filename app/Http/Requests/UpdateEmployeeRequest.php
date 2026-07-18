<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id ?? $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $employeeId,
            'password' => 'sometimes|string|min:8|confirmed',
            'kpi_score' => 'sometimes|integer|min:0',
        ];
    }
}