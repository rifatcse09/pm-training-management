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
            'employee_ids.*' => ['exists:employees,id'], // Validate each employee ID
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_days' => 'required|integer|min:1',
            'file_link' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Validate file upload
        ];
    }
}
