<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateWebsiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'user_id' => 'required|integer|exists:users,id'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Website name is required.',
            'name.max' => 'Website name cannot exceed 255 characters.',
            'url.required' => 'Website URL is required.',
            'url.url' => 'Please provide a valid URL.',
            'url.max' => 'Website URL cannot exceed 255 characters.',
            'user_id.required' => 'User ID is required.',
            'user_id.integer' => 'User ID must be a valid integer.',
            'user_id.exists' => 'The specified user does not exist.',
        ];
    }
}
