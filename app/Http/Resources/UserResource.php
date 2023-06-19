<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $createdAt = $this->created_at;

        if (is_string($createdAt)) {
            $createdAt = new \DateTime($createdAt);
        }

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'fullname' => $this->first_name . " " . $this->last_name,
            'email' => $this->email,
            'contact_number' => $this->contact_number,
            'role_id' => $this->role_id,
            'role_name' => $this->name,
            'service_center_id' => $this->service_center_id ?? null,
            'service_center' => $this->service_center ?? null,
            'branch_manager' => $this->branch_manager ?? null,
            'allowed_sc' => $this->allowed_sc ?? null,
            'allowed_bm' => $this->allowed_bm ?? null,
            'image' => $this->image,
            'created_at' => $createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
