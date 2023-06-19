<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCenterBookingResource extends JsonResource
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
            'client_id' => $this->client_id,
            'client_name' => $this->first_name . " " . $this->last_name,
            'vehicle_id' => $this->vehicle_id,
            'vehicle_name' => $this->vehicle_name,
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
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
