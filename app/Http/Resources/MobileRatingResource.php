<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileRatingResource extends JsonResource
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
            'client_id' => $this->client_id,
            'service_center_id' => $this->service_center_id,
            'booking_id' => $this->booking_id,
            'tech_ref_id' => $this->tech_ref_id,
            'quality_of_service' => $this->quality_of_service,
            'quick_service' => $this->quick_service,
            'general_exp' => $this->general_exp,
            'comments' => $this->comments
        ];
    }
}
