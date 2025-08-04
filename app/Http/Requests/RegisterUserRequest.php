<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'emp_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'emp_dob' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
        ];
    }
}