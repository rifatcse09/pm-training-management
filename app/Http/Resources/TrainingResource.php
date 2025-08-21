<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'organization_id' => $this->organization_id,
            'organization_name' => $this->organizer_name ?? $this->organization->name ?? null, // Handle organizer_name gracefully
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_days' => $this->total_days,
            'file_name' => $this->file_name,
            'file_link' => $this->file_name ? asset('storage/training/' . $this->file_name) : null,
            'countries' => $this->when($this->type == 2, function () {
                return $this->countries->map(function ($country) {
                    return [
                        'id' => $country->id,
                        'name' => $country->name,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
