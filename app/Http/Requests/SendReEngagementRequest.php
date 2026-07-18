<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendReEngagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'required|string',
            'channel' => 'sometimes|in:email,sms',
        ];
    }
}
