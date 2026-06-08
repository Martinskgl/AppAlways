<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id'               => ['required', 'integer', 'exists:customers,id'],

            'items'                     => ['required', 'array', 'min:1'],
            'items.*.product_id'        => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'          => ['required', 'integer', 'min:1'],

            'credit_card.holder_name'   => ['required', 'string', 'max:255'],
            'credit_card.number'        => ['required', 'string', 'size:16'],
            'credit_card.expiry_year' => ['required', 'integer', 'min:' . date('Y')],
            'credit_card.expiry_month' => [
                'required',
                'integer',
                'between:1,12',
                function ($attribute, $value, $fail) {
                    $year = request()->input('credit_card.expiry_year');
                    if ($year == date('Y') && $value < date('n')) {
                        $fail('O cartão está expirado.');
                    }
                },
            ],
            'credit_card.cvv'           => ['required', 'string', 'size:3'],
        ];
    }
}
