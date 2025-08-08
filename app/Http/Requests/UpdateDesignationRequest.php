<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDesignationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:designations,name,' . $this->designation->id,
            'grade' => 'required|string|max:255', // Added grade field
            'class' => 'required|string|max:255', // Added class field
        ];
    }
}
