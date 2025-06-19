<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExampleReadResource extends JsonResource
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
            'name' => $this->name,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_time' => $this->created_time,
            'updated_time' => $this->updated_time,
            'status' => $this->status,
            'nik' => $this->nik,
            'dob' => $this->dob,
            'citizen' => $this->citizen,
            'hobbies' => json_decode($this->hobbies),
            'married_status' => $this->married_status,
            'profile_picture' => json_decode($this->profile_picture),
            'supporting_document' => json_decode($this->supporting_document),
            'phone' => $this->phone,
            'taxpayer_number' => $this->taxpayer_number,
            'age' => $this->age,
        ];
    }
}
