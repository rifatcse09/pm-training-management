<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all authorized users to make this request
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:1,2',
            'organization_id' => 'sometimes|required|exists:organizers,id',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'total_days' => 'sometimes|required|integer|min:1',
            'file_link' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Optional file upload
        ];
    }
}
