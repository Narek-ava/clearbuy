<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\ProductController;

class ProductsVariantsResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */

    public function toArray($request)
    {
        $data = [];

        if (!is_null($this->productFirstImage())) {
            $data[] = ['id' => $this->id, 'name' => 'Default'];
        }

        $products_variants = $this->variants($this->id)->has('images')->get();

        foreach($products_variants as $vrt) {
            $data[] = [
                'id'      => (int) $vrt->id,
                'name'    => (string) $vrt->variant->name,
            ];
        }

        return [
            'id'        =>  $this->id,
            'variants'  =>  $data,
            'edit_url'  =>  action([ProductController::class, 'form'], ['id' => $this->id, 'backUrl' => '/admin/products'])
        ];
    }
}
