<?php namespace App\Traits;

trait HasProduct {

    public function product() {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}
