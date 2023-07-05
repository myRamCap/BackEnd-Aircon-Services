<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'reference_number' => $this->reference_number,
            'client_name' => $this->first_name . " " . $this->last_name,
            'aircon_id' => $this->aircon_id,
            'aircon_name' => $this->aircon_name,
            'services_id' => $this->services_id,
            'service' => $this->service,
            'service_center_id' => $this->service_center_id,
            'service_center' => $this->service_center,
            'contact_number' => $this->contact_number,
            'status' => $this->status,
            'booking_date' => $this->booking_date,
            'time' => $this->time,
            'estimated_time_desc' => $this->estimated_time_desc,
            'notes' => $this->notes,
            'tech_id' => $this->tech_id,
            'updated_by' => $this->fn ? $this->fn . " " . $this->ln : null,
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
