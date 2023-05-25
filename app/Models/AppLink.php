<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppLink extends Model
{
    use HasFactory;

    protected $table = 'app_links';
    protected $fillable = ['app_id', 'os_id', 'url', 'price', 'store_id', 'free', 'app_purchase', 'currency_id'];
    public $timestamps = false;

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_id');
    }

    public function store() {
		return $this->belongsTo('App\Models\AppStore', 'store_id');
	}
}
