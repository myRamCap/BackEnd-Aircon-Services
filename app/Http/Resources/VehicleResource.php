<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'client_name' => $this->first_name . " " . $this->last_name,
            'vehicle_name' => $this->vehicle_name,
            'chassis_number' => $this->chassis_number,
            // 'contact_number' => $this->contact_number,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'image' => $this->image,
            'notes' => $this->notes,
            'created_at' => $this->created_at === null ? '' : $this->created_at->format('Y-m-d H:i:s'),

            //'date' => $startDate === null ? '' : $startDate->format('d/m/Y'),
        ];
    }
}
