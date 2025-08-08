<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'emp_name' => $this->emp_name,
            'email' => $this->email,
            'role' => $this->role->role_name, // Include role name
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
