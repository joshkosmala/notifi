<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
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
            'link' => ['nullable', 'url', 'max:255'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->scheduled_for) {
            // Get the organisation's timezone
            $organisation = $this->user()->organisations()->first();
            $timezone = $organisation?->timezone ?? 'Pacific/Auckland';

            // Convert from organisation's timezone to UTC
            $localTime = \Carbon\Carbon::parse($this->scheduled_for, $timezone);
            $this->merge([
                'scheduled_for' => $localTime->utc()->format('Y-m-d H:i:s'),
            ]);
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
            'scheduled_for.after' => 'The scheduled time must be in the future.',
        ];
    }
}
