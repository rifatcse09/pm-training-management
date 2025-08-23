<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorize the request (adjust as needed)
        return true;
    }

    public function rules(): array
    {
        return [
            'training_id' => 'required|exists:trainings,id',
            'employee_id' => 'required|exists:employees,id', // Single employee ID
        ];
    }
}
