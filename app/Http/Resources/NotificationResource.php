<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            // 'corporate_id' => $this->corporate_id,
            // 'first_name' => $this->first_name,
            // 'last_name' => $this->last_name,
            // 'service_center_id' => $this->service_center_id,
            // 'service_center' => $this->service_center,
            'category' => $this->category,
            'service_center' => json_decode($this->service_center),
            'date_range' => $this->datefrom . " - " . $this->dateto,
            'datefrom' => $this->datefrom,
            'dateto' => $this->dateto,
            'title' => $this->title,
            'content' => $this->content,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at,
        ];
    }
}
