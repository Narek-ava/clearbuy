<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppResource extends JsonResource
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
            'id' => (int) $this['id'],
            'name' => (string) $this['name'],
            'type' => $this->getTypeAttribute()->name,
            'logo' => $this->getLogoUrl($this->logo) ?? null,
            'images' => $this->getImages(),
            'change_log_url' => (string) $this['change_log_url'],
            'video_url' => (string) $this['video_url'],
            'brand' => $this->brand->name ?? null,
            'os' => $this->getOses(),
            'countries' => $this->getCountries(),
            'store_links' => $this->getStoreLinks()
        ];
    }
}
