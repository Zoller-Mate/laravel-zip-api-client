<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:255',
            'county_id' => 'required|integer|min:1',
            'postal_code_id' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A város neve kötelező.',
            'name.min' => 'A város neve legalább 2 karakter hosszú kell, hogy legyen.',
            'county_id.required' => 'Válassz ki egy megyét.',
            'postal_code_id.required' => 'Válassz ki egy irányítószámot.',
        ];
    }
}
