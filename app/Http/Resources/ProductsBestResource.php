<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\ProductController;

class ProductsBestResource extends JsonResource
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
           'id'     =>  (int) $this->id,
           'name'   =>  (string) $this->name,
           'rating' =>  $this->public_rating,
           'images' =>  $this->getImagePath() ?? [],
           'store_url'  =>  !empty($this->product_url) ? (string) $this->product_url : null,
           'review_url' =>  !empty($this->review_url) ? (string) $this->review_url : null,
           'buyers_guide_url' =>  !empty($this->buyers_guide_url) ? (string) $this->buyers_guide_url : null,
           'edit_url'   =>  action([ProductController::class, 'form'], ['id' => $this->id, 'backUrl' => '/admin/products'])
       ];
   }
}
