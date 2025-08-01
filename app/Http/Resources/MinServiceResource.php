<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MinServiceResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'icon' => $this->icon,
            'is_published' => $this->is_published,
            'image_url' => $this->getFirstMediaUrl('images'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'created_by' => $this->whenLoaded('creator', function () {
            //     return $this->creator->name;
            // }),
        ];
    }
}
