<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'website_id' => 'required|integer|exists:websites,id'
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required.',
            'user_id.integer' => 'User ID must be a valid integer.',
            'user_id.exists' => 'The specified user does not exist.',
            'website_id.required' => 'Website ID is required.',
            'website_id.integer' => 'Website ID must be a valid integer.',
            'website_id.exists' => 'The specified website does not exist.',
        ];
    }
}
