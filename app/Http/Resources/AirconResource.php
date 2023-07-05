<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AirconResource extends JsonResource
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
            'client_mobile_number' => $this->contact_number,
            'aircon_name' => $this->aircon_name,
            'aircon_type' => $this->aircon_type,
            'make' => $this->make,
            'model' => $this->model,
            'horse_power' => $this->horse_power,
            'serial_number' => $this->serial_number,
            'image' => $this->image,
            'notes' => $this->notes,
            'created_at' => $this->created_at === null ? '' : $this->created_at->format('Y-m-d H:i:s'),

            //'date' => $startDate === null ? '' : $startDate->format('d/m/Y'),
        ];
    }
}
