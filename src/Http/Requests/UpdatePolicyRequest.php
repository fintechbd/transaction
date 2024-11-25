<?php

namespace Fintech\Transaction\Http\Requests;

use Fintech\Core\Enums\Auth\RiskProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePolicyRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        if ($this->method() === 'PATCH') {
            return [
                'enabled' => ['nullable', 'boolean'],
                'policy_data' => ['required', 'array'],
            ];
        }

        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'code' => ['required', 'string', 'min:1', 'max:255', Rule::unique('policies', 'code')->ignore($this->route('policy'))],
            'enabled' => ['required', 'boolean'],
            'risk' => ['required', 'string', Rule::in(RiskProfile::values())],
            'priority' => ['required', 'string', Rule::in(RiskProfile::values())],
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
