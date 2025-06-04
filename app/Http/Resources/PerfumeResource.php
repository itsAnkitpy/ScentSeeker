<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerfumeResource extends JsonResource
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
            'brand' => $this->brand,
            'description' => $this->description,
            'notes' => $this->when($this->notes, function () {
                return is_string($this->notes) ? json_decode($this->notes, true) : $this->notes;
            }, null),
            'image_url' => $this->image_url,
            'concentration' => $this->concentration,
            'gender_affinity' => $this->gender_affinity,
            'launch_year' => $this->launch_year,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
