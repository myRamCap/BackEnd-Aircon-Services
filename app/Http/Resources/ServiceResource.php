<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'name' => $this->name,
            'details' => $this->details,
            'service_center_id' => $this->service_center_id,
            'service_center' => $this->service_center,
            'image_id' => $this->image_id,
            'image_url' => $this->image_url,
            'created_by' => $this->c_fn . " " . $this->c_ln,
            'updated_by' => $this->u_fn . " " . $this->u_ln,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
