<?php

namespace Fintech\Transaction\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderQueueRequest extends FormRequest
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
        $uniqueRule = 'unique:' . config('fintech.transaction.order_queue_model', \Fintech\Transaction\Models\OrderQueue::class) . ',user_id';

        return [
            'user_id' => ['integer', 'required', 'min:1', $uniqueRule],
            'order_id' => ['integer', 'required', 'min:1'],
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
