<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCostResource extends JsonResource
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
            'cost' => $this->cost,
            'markup' => $this->markup,
            'notes' => $this->notes,
            'created_at' => $this->created_at === null ? '' : $this->created_at->format('Y-m-d H:i:s'),

            //'date' => $startDate === null ? '' : $startDate->format('d/m/Y'),
        ];
    }
}
