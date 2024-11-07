<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'breed' => 'required',
            'age' => 'required',
            'size' => 'required',
            'pedigree' => 'nullable',
            'bio' => 'nullable',
            'description' => 'nullable'
        ];
    }

    public function messages(): array 
    {
        return [
            'name.required' => 'Este campo é obrigatório.',
            'breed.required' => 'Este campo é obrigatório.',
            'age.required' => 'Este campo é obrigatório.',
            'size.required' => 'Este campo é obrigatório.'
        ];
    }
}
