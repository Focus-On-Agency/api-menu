<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllergenResource extends JsonResource
{
    protected bool $hiddenInfo = false;

    /**
     * Build a resource instance while explicitly controlling the visibility of
     * the internal identifiers. The default JsonResource constructor must stay
     * untouched so Laravel collections can instantiate this resource safely.
     */
    public static function withHiddenInfo(mixed $resource, bool $hiddenInfo = false): self
    {
        $instance = new self($resource);
        $instance->hiddenInfo = $hiddenInfo;

        return $instance;
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
            'description' => $this->getTranslations('description'),
        ];
    }
}
