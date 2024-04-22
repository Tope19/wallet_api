<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class CreditWalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(['data' => $errors], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'wallet_no' => ['required', 'string', 'exists:wallets,wallet_no'],
            'amount' => ['required', 'numeric', 'min:50.00'],
        ];
    }

    public function messages(): array
    {
        return [
            'wallet_no.exists' => 'Wallet does not exist',
            'amount.min' => 'The minimum amount to credit is 50.00'
        ];
    }
}
