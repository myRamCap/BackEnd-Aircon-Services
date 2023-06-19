<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCenterServicesResource extends JsonResource
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
            'service_center_id' => $this->service_center_id,
            'service_id' => $this->service_id,
            'estimated_time' => $this->estimated_time,
            'estimated_time_desc' => $this->estimated_time_desc,
            'price' => $this->price,
            'name' => $this->name,
            'details' => $this->details,
            'image_url' => $this->image_url,
            'price' => $this->price,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
