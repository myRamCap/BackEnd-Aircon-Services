<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCenterResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference_number' => $this->reference_number,
            'name' => $this->name,
            'category' => $this->category,
            'country' => $this->country,
            'house_number' => $this->house_number,
            'barangay' => $this->barangay,
            'municipality' => $this->municipality,
            'province' => $this->province,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'facility' => $this->facility,
            'corporate_manager_id' => $this->corporate_manager_id,
            'image' => $this->image,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
