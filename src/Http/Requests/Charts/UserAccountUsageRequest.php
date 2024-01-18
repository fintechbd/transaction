<?php

namespace Fintech\Transaction\Http\Requests\Charts;

use Illuminate\Foundation\Http\FormRequest;

class UserAccountUsageRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'min:1'],
            'type' => ['nullable', 'string', 'in:receive,transfer'],
            'duration' => ['nullable', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'duration' => $this->input('duration', 1),
        ]);
    }
}
