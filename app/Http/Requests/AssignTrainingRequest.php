<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignTrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_id' => ['required', 'exists:trainings,id'], // Validate training_id
            'employee_ids' => ['required', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
        ];
    }
}
