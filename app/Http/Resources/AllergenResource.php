<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllergenResource extends JsonResource
{
    protected bool $hiddenInfo;

    public function __construct($resource, bool $hiddenInfo = false)
    {
        parent::__construct($resource);
        $this->hiddenInfo = $hiddenInfo;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->when(!$this->hiddenInfo, $this->id),
            'name' => $this->name,
            'icon' => $this->icon,
            'color' => $this->color,
            'description' => $this->description,
        ];
    }
}
