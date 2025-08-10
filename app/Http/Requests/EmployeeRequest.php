<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'designation_id' => 'required|exists:designations,id',
            'mobile' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'working_place' => 'required|in:1,2,3,4,5,6,7',
        ];
    }
}
