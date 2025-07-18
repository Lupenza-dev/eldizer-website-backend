<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $media = $this->getFirstMedia('team-members');
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'bio' => $this->bio,
            'image_url' => $this->getFirstMediaUrl('images'),
            'social_links' => $this->social_links ? json_decode($this->social_links, true) : [],
            'is_published' => $this->is_published,
            'order' => $this->order,
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
