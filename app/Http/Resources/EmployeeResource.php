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
            'designation' => $this->designation->name ?? null,
            'grade' => $this->designation->grade,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'working_place' => $this->working_place,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
