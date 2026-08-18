<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|unique:admin|max:255',
            'email'    => 'required|email|unique:admin|max:255',
            'password' => 'required|max:50',
            'role'     => 'required',
            'status'   => 'required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'username' => 'Username',
            'email'    => 'Email',
            'password' => 'Password',
            'role'     => 'Role',
            'status'   => 'Status',
        ];
    }
}
