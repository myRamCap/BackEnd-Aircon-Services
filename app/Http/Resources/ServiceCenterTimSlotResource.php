<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCenterTimSlotResource extends JsonResource
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
            'time' => $this->time,
            // 'max_limit' => $this->max_limit,
            'created_at' => $this->created_at,
            'date_created' => $this->created_at ,
        ];
    }
}
