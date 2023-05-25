<?php namespace App\Traits;

trait HasAgent {

    public function agent() {
        return $this->belongsTo('App\Models\Agent', 'agent_id');
    }
}
