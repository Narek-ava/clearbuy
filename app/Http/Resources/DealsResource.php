<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DealsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'content' => [
                'total_pages' => (int) $this['total_pages'],
                'total_posts' => (int) $this['total_posts'],
                'current_page' => (int) $this['current_page'],
                'posts_per_page' => (int) $this['posts_per_page'],
                'posts' => DealResource::collectionWithParams($this['posts'], false)
            ],            
            'hot' => DealResource::collectionWithParams($this['hot'], true)
        ];
    }
}
