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
            'countries'        => ['required_if:type,2','array'],
            'countries.*'      => ['integer','exists:countries,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'countries.required_if' => 'Please select at least one country for type 2 trainings.',
        ];
    }
}
