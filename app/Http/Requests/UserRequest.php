<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|string|between:5,50',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array {
        return [
            'required' => 'Le champ :attribute est obligatoire',
            'between' => 'Le champ :attribute doit contenir entre :min et :max caractères.',
            'max' => 'Le champ :attribute doit contenir au maximum :max caractères.',
            'min' => 'Le champ :attribute doit contenir au minimum :min caractères.',
            'email.unique' => 'L\'adresse mail est déjà utilisée'
        ];
    }

}
