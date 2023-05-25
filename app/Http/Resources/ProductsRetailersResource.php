<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\ProductController;

class ProductsRetailersResource extends JsonResource
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

        switch($request->type) {

            case 'deal':
                $retailers = $this->dealAgents()->get();
                break;

            case 'product':
                $retailers = $this->agents()->get();
                break;

            default:
                $retailers = $this->dealAgents()->get();
                $retailers = $retailers->merge($this->agents()->get());
        }

        $data = $retailers->map(function($retailer) {
            return [
                'id' => (int) $retailer->id,
                'name' => (string) $retailer->name
            ];
        })->unique('id')->toArray();

        return [
            'id'        => $this->id,
            'agents'    => $data,
            'edit_url'  => action([ProductController::class, 'form'], ['id' => $this->id, 'backUrl' => '/admin/products'])
        ];
    }
}
