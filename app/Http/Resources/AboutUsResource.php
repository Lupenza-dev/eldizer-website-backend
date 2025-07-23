<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsResource extends JsonResource
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
            'vision' => $this->vision,
            'mission' => $this->mission,
            'content' => $this->content,
            'values' => $this->values,
            'all_values' => explode(',',$this->values),
            'image_url' => $this->getFirstMediaUrl('images'),
            'is_published' => $this->is_published,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->whenLoaded('creator', function () {
                return $this->creator->name;
            }),
        ];
    }
}
