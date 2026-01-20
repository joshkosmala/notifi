<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // User fields
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // Organisation fields
            'organisation_name' => ['required', 'string', 'max:255'],
            'organisation_email' => ['nullable', 'string', 'email', 'max:255'],
            'organisation_phone' => ['nullable', 'string', 'max:50'],
            'organisation_address' => ['nullable', 'string', 'max:500'],
            'organisation_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'organisation_name.required' => 'Please enter your organisation name.',
            'organisation_url.url' => 'Please enter a valid URL (e.g., https://example.com).',
        ];
    }
}
