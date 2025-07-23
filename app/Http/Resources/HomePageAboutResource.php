<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePageAboutResource extends JsonResource
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
            'values' => $this->values,
            'all_values' => explode(',',$this->values),
            'badge'  => $this->badge,
            'badge_1' =>$this->extractBetweenMarkers($this->badge,"#"),
            'badge_2' =>$this->extractBetweenMarkers($this->badge,"@"),
            'image_url' => $this->getFirstMediaUrl('images'),
            'is_published' => $this->is_published,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->whenLoaded('creator', function () {
                return $this->creator->name;
            }),
        ];
    }

    function extractBetweenMarkers($text, $marker) {
        // Escape the marker for regex (in case it's a special character)
        $escapedMarker = preg_quote($marker, '/');
    
        // Build the regex pattern dynamically
        $pattern = "/{$escapedMarker}(.*?){$escapedMarker}/";
    
        if (preg_match($pattern, $text, $matches)) {
            return $matches[1];  // Return the content inside markers
        }
    
        return null;  // Return null if not found
    }
}
