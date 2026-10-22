<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontendAllergenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // The name is the visual identifier of the legend (e.g. "01" or the
            // leaf icon), while the description carries the localized label.
            'name' => $this->name,
            'icon' => $this->icon,
            'color' => $this->color,
            'description' => $this->getTranslations('description'),
        ];
    }
}