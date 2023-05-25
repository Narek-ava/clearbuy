<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasProduct;
use App\Traits\HasCurrency;
use App\Traits\HasAgent;
use Carbon\Carbon;

class ProductDealPrice extends Model
{
	use HasFactory, HasProduct, HasCurrency, HasAgent;

	protected $table = 'deal_prices';
	protected $fillable = [
		'agent_id',
		'product_id',
		'currency_id',
		'price',
		'original_price',
		'expiry_notification',
		'url',
		'is_free',
		'coupon_code',
		'expiry_date',
		'is_hot',
		'recommended',
		'retailer_custom_text',
		'created_at',
	];
  
	// protected $casts = [
	//     'expiry_date' => 'datetime:Y-m-d H:i:s',
	// ];

	static public function orderByColumn($column, $order = 'ASC')
	{
		$order = strtoupper($order) == 'ASC' ? 'ASC' : 'DESC';
		if (self::ownSortableColumns()->contains($column)) {
			return self::orderBy($column, $order);
		} else {
			return self::orderByRelation($column, $order);
		}
	}

	static protected function orderByRelation($column, $order)
	{
		if ($column == 'product') {
			return self::select('deal_prices.*')
				->join('product', 'product.id', '=', 'deal_prices.product_id')
				->orderBy('product.name', $order);
		} elseif ($column == 'agent') {
			return self::select('deal_prices.*')
				->join('agent', 'agent.id', '=', 'deal_prices.agent_id')
				->orderBy('agent.name', $order);
		}  else {
			return self::select('deal_prices.*');
		}
	}

	static protected function ownSortableColumns()
	{
		return collect(['id', 'price', 'created_at', 'expiry_date']);
	}

	public function getCreatedAtAttribute($date) {
		return (new \Carbon\Carbon($date))->format("m/d/Y");
	}

	public function getCreationDateAttribute() {
		return (new \Carbon\Carbon($this->getRawOriginal('created_at')))->format("Y-m-d\TH:i");
	}

	public function setCreatedAtAttribute($date) {
		$this->attributes['created_at'] = (new \Carbon\Carbon($date))->format("Y-m-d H:i:s");
	}

	public function getExpiryDateAttribute($date) {
		return (new \Carbon\Carbon($date))->format("Y-m-d\TH:i:s");
	}

	public function setExpiryDateAttribute($date) {
		$this->attributes['expiry_date'] = (new \Carbon\Carbon($date))->format("Y-m-d H:i:s");
	}

	public function getCarbonDate(string $date) {
		return new \Carbon\Carbon($date);
	}

	public function scopeNotExpiry($query) {

		return $query->whereNull('expiry_date')
                     ->orWhere('expiry_date', '>', now());
	}

	public function scopeWhereAgents($query, $agents) {

		if(!empty($agents)) return $query->whereIn('agent_id', $agents);
	}

	public function scopeByRecomended($query, $sort) {

		return $query->orderBy('recommended', $sort);
	}

	public function scopeOrderByPrice($query) {

		return $query->orderBy('price');
	}

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}

	public function getExpiryDateTimestampAttribute()
	{
		return Carbon::parse($this->expiry_date)->timestamp;
	}

	public function getCreatedAtTimestampAttribute()
	{
		return Carbon::parse($this->getRawOriginal('created_at'))->timestamp;
	}
}
