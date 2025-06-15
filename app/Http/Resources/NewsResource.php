<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $media = $this->getFirstMedia('news');
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $media ? $media->getUrl() : null,
            'image_thumb' => $media ? $media->getUrl('thumb') : null,
            'image_medium' => $media ? $media->getUrl('medium') : null,
            'image_large' => $media ? $media->getUrl('large') : null,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'created_by' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
        ];
    }
}
