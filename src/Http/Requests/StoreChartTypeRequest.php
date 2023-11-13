<?php

namespace Fintech\Transaction\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChartTypeRequest extends FormRequest
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
        return [
            'chart_class_id' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'code' => ['required', 'string', 'min:3', 'max:255'],
            'chart_type_data' => ['nullable', 'array'],
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
