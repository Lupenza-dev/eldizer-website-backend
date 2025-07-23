<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $media = $this->getFirstMedia('sliders');
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'title_slider'     =>$this->extractBetweenMarkers($this->title,"#"),
            'title_slider_sub' =>$this->extractBetweenMarkers($this->title,"@"),
            'subtitle' => $this->subtitle,
            'button_text' => $this->button_text,
            'button_url' => $this->button_url,
            'button_url' => $this->button_url,
            'badge' => $this->badge,
            'badge_1' =>$this->extractBetweenMarkers($this->badge,"#"),
            'badge_2' =>$this->extractBetweenMarkers($this->badge,"@"),
            'features' => $this->features,
            'all_features' => explode(',',$this->features),
            'image_url' => $this->getFirstMediaUrl('images'),
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
