<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:50'],
            'body' => ['required', 'string', 'max:280'],
            'link' => ['nullable', 'string', 'max:255', 'regex:/^https?:\/\/[^\s\/$.?#].[^\s]*$/i'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize link - prepend https:// if missing
        if ($this->link) {
            $link = trim($this->link);
            if (! preg_match('/^https?:\/\//i', $link)) {
                $link = 'https://'.$link;
            }
            $this->merge(['link' => $link]);
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.max' => 'The title cannot exceed 50 characters.',
            'body.max' => 'The message cannot exceed 280 characters.',
            'link.regex' => 'Please enter a valid link (e.g., example.com or https://example.com).',
            'scheduled_for.after' => 'The scheduled time must be in the future.',
        ];
    }
}
