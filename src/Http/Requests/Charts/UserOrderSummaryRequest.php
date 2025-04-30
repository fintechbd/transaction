<?php

namespace Fintech\Transaction\Http\Requests\Charts;

use Fintech\Core\Traits\RestApi\HasPaginateQuery;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserOrderSummaryRequest extends FormRequest
{
    use HasPaginateQuery;

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['integer', 'nullable'],
            'role_id' => ['integer', 'nullable'],
            'status' => ['nullable'],
            'status_not_equal' => ['nullable'],
            'created_at_start_date' => ['date', 'nullable'],
            'created_at_end_date' => ['date', 'nullable'],
            'service_type_parent_slug' => ['string', 'nullable', 'exists:service_types,service_type_slug'],
            'service_type_parent_id' => ['integer', 'nullable'],
            'source_country_id' => ['integer', 'nullable', 'master_currency'],
            'destination_country_id' => ['integer', 'nullable', 'master_currency'],
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
