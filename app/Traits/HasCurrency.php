<?php namespace App\Traits;

trait HasCurrency {

    public function currency() {
        return $this->belongsTo('App\Models\Currency', 'currency_id');
    }
}
