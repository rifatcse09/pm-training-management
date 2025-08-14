<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'designation_id' => $this->designation_id, // Include designation ID
            'designation_name' => $this->designation->name ?? null, // Include designation name
            'grade' => $this->designation->grade ?? null,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'working_place' => $this->working_place,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
