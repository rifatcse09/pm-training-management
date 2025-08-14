<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all authorized users to make this request
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2',
            'organization_id' => 'required|exists:organizers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_days' => 'required|integer|min:1',
            'file_link' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Optional file upload
        ];
    }
}
