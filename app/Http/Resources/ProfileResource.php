<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'phone_number' => $this->phone_number,
            'contact_address' => $this->contact_address,
            'billing_address' => $this->billing_address,
            'is_staff' => (bool) $this->is_staff,
            'gender_id' => $this->gender_id,
            'department_id' => $this->department_id,
            'designation_id' => $this->designation_id,
            'status_id' => $this->status_id,
            // Related simple lookups (id + name)
            'gender' => $this->whenLoaded('gender', function () {
                return $this->gender=[
                    'id' => $this->gender->id,
                    'name' => $this->gender->name,
                ];
            }),
            'department' => $this->whenLoaded('department', function () {
                return $this->department=[
                    'id' => $this->department->id,
                    'name' => $this->department->name,
                ];
            }),
            'designation' => $this->whenLoaded('designation', function () {
                return $this->designation=[
                    'id' => $this->designation->id,
                    // Title on the table; expose as title to match DB
                    'name' => $this->designation->name,
                ];
            }),
            'status' => $this->whenLoaded('status', function () {
                return $this->status=[
                    'id' => $this->status->id,
                    'name' => $this->status->name,
                ];
            }),
        ];
    }
}
