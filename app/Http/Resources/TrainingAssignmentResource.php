<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingAssignmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'training_name' => $this->training->name,
            'employee_name' => $this->employee->name,
            'designation_name' => $this->designation->name ?? null, // Include designation name
            'working_place' => $this->working_place,
            'organizer_name' => $this->training->organizer->name ?? null, // Include organizer name
            'assigned_date' => $this->assigned_at->toDateString(),
            'assigned_by' => $this->assigned_by,
        ];
    }
}
