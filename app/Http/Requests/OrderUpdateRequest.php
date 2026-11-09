<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            'status' => ['string', 'max:255'],
            'total' => ['numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'user_id' => ['exists:users,id'],
            'address_id' => ['exists:addresses,id'],
        ];
    }
}
