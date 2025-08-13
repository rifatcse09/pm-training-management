<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'organization_id' => $this->organization_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_days' => $this->total_days,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
