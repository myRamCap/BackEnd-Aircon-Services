<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
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
            'category' => $this->category,
            'client' => json_decode($this->client),
            'datefrom' => $this->datefrom,
            'dateto' => $this->dateto,
            'date_range' => $this->datefrom . " - " . $this->dateto,
            'title' => $this->title,
            'content' => $this->content,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
