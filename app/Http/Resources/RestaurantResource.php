<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
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
            'address' => $this->address,
            'image' => $this->whenLoaded('image', fn () => $this->image ? new ImageResource($this->image) : null),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'menus' => MenuResource::collection($this->whenLoaded('menus')),
        ];
    }
}
