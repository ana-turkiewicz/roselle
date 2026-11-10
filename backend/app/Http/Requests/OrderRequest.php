<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => ['required', Rule::exists('addresses', 'id')->where('user_id', $this->user()->id)],
            'notes' => ['nullable', 'string'],
        ];
    }
}