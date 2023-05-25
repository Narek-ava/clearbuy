<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasProduct;
use App\Traits\HasCurrency;
use App\Traits\HasAgent;

class ProductPrice extends Model
{
	use HasFactory, HasProduct, HasCurrency, HasAgent;

	protected $table = 'product_prices';
	protected $fillable = ['agent_id', 'product_id', 'currency_id', 'current_msrp', 'original_msrp', 'url', 'recommended'];

	public function scopeWhereAgents($query, $agents) {

		if(!empty($agents)) return $query->whereIn('agent_id', $agents);
	}

	public function scopeByRecomended($query, $sort) {

		return $query->orderBy('recommended', $sort);
	}

	public function scopeOrderByPrice($query) {

		return $query->orderBy('current_msrp');
	}

}
