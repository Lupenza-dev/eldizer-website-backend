<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $media = $this->getFirstMedia('testimonials');
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'content' => $this->content,
            'image' => $media ? $media->getUrl() : null,
            'image_thumb' => $media ? $media->getUrl('thumb') : null,
            'image_medium' => $media ? $media->getUrl('medium') : null,
            'image_large' => $media ? $media->getUrl('large') : null,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
        ];
    }
}
