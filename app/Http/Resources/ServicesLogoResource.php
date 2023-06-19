<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicesLogoResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at === null ? '' : $this->created_at->format('Y-m-d H:i:s'),

            //'date' => $startDate === null ? '' : $startDate->format('d/m/Y'),
        ];
    }
}
