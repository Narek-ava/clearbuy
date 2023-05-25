<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
	use HasFactory;

	protected $table = 'product';
	protected $fillable = [
		'id',
		"name",
		"sku",
		"asin",
		"model",
		"model_family",
		"price_msrp",
		"currency_msrp",
		"price_current",
		"currency_current",
		"size_length",
		"size_width",
		"size_height",
		"weight",
		"date_publish",
		"is_promote",
		"excerpt",
		"summary_main",
		"tagline",
		"reasons_to_buy",
		"full_overview",
		"seo_keywords",
		"category_id",
		"brand_id",
		"country_id",
		"released_with_os_id",
		"review_url",
		"urgency",
		"product_url",
		"pros",
		"cons",
		"rating"
	];

	protected $dates = ['created_at', 'updated_at', 'urgency'];

	public function category()
	{
		return $this->belongsTo('App\Models\Category', 'category_id');
	}

	public function brand()
	{
		return $this->belongsTo('App\Models\Brand', 'brand_id');
	}

	public function country()
	{
		return $this->belongsTo('App\Models\Country', 'country_id');
	}

	public function msrpCurrency()
	{
		return $this->belongsTo('App\Models\Currency', 'currency_msrp');
	}

	public function currentCurrency()
	{
		return $this->belongsTo('App\Models\Currency', 'currency_current');
	}

	public function releasedWithOS()
	{
		return $this->belongsTo('App\Models\OS', 'released_with_os_id');
	}

	public function targetCountries()
	{
		return $this->belongsToMany('App\Models\Country', 'product_to_country', 'product_id', 'country_id');
	}

	public function updatableToOS()
	{
		return $this->belongsToMany('App\Models\OS', 'product_to_os', 'product_id', 'os_id');
	}

	public function similarProducts()
	{
		return $this->belongsToMany('App\Models\Product', 'similar_products', 'product_id', 'similar_id');
	}

	public function websites()
	{
		return $this->belongsToMany('App\Models\Website', 'product_to_website', 'product_id', 'website_id');
	}

	public function links()
	{
		return $this->hasMany('App\Models\ProductLink', 'product_id');
	}

	public function deal()
	{
		return $this->hasMany('App\Models\ProductDealPrice', 'product_id');
	}

	public function hotDealWithPriority()
	{
		return $this->hasOne('App\Models\ProductDealPrice', 'product_id')
			->where('is_hot', true)
			->where('expiry_date', '>', now())
			->orderBy('created_at', 'desc')
			->orderBy('recommended', 'desc')
			->orderBy('price');
	}

	public function dealWithPriority()
	{
		return $this->hasOne('App\Models\ProductDealPrice', 'product_id')
			->where('expiry_date', '>', now())
			->orderBy('created_at', 'desc')
			->orderBy('recommended', 'desc')
			->orderBy('price');
	}

	public function prices()
	{
		return $this->hasMany('App\Models\ProductPrice', 'product_id');
	}

	public function contents()
	{
		return $this->hasMany('App\Models\ProductContent', 'product_id');
	}

	public function images()
	{
		return $this->hasMany('App\Models\ProductImage', 'product_id')->orderBy('order', 'ASC');
	}

	public function getImageAttribute()
	{
		if ($this->images->count() > 0) {
			return $this->images[0];
		}
		return null;
	}

	public function getImagePath()
	{
		if ($this->images()->exists()) {
			$ar_image = $this->images->pluck('path');

			foreach($ar_image as $key => $item) {
				if (Storage::disk('do_image_spaces')->exists($item)) {
					$ar_image[$key] = Storage::disk('do_image_spaces')->Url($item);
				}
			}
			return $ar_image;
		}
		return null;
	}

	public function productFirstImage()
	{
		if (empty($this->images->first())) {
			return null;
		}

		$image = trim($this->images->first()->path);

		if (filter_var(trim($image), FILTER_VALIDATE_URL)) {
			return $image;
		}

		if (Storage::disk('do_image_spaces')->exists($image)) {
			return Storage::disk('do_image_spaces')->Url($image);
		}

		return null;
	}

	public function agents()
	{
		return $this->belongsToMany('App\Models\Agent', 'product_prices', 'product_id', 'agent_id');
	}

	public function dealAgents()
	{
		return $this->belongsToMany('App\Models\Agent', 'deal_prices', 'product_id', 'agent_id');
	}

	public function priceChanges()
	{
		return $this->hasMany('App\Models\ProductPriceChange', 'product_id')->orderBy('created_at', 'ASC');
	}

	public function attributes()
	{
		return $this->belongsToMany('App\Models\Attribute', 'attribute_to_product', 'product_id', 'attribute_id');
	}

	public function variant()
	{
		return $this->belongsTo('App\Models\ProductVariant', 'variant_id');
	}

	public function scopeVariants($query, $parent_id)
	{
		return $query->where('parent_id', $parent_id);
	}

	public function attributeValue(int $attribute_id)
	{
		$attribute = $this->attributes()->where('attribute_id', $attribute_id)->first();
		if ($attribute) {
			return $attribute->valueForProduct($this->id);
		}
		return null;
	}

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
		if ($column == 'country') {
			return self::select('product.*')
				->join('country', 'country.id', '=', 'product.country_id')
				->orderBy('country.name', $order);
		} elseif ($column == 'category') {
			return self::select('product.*')
				->join('category', 'category.id', '=', 'product.category_id')
				->orderBy('category.name', $order);
		} elseif ($column == 'brand') {
			return self::select('product.*')
				->join('brand', 'brand.id', '=', 'product.brand_id')
				->orderBy('brand.name', $order);
		} else {
			return self::select('product.*');
		}
	}

	static protected function ownSortableColumns()
	{
		return collect(['id', 'name', 'sku', 'model', 'model_family', 'created_at', 'date_publish', 'price_msrp', 'price_current', 'is_promote']);
	}



	public function getBadgePathAttribute()
	{
		$badges = $this->badges;
		if ($badges->isEmpty()) {
			return null;
		}

		$badge = $badges[0];
		if (is_null($badge->image)) {
			return null;
		}

		return $badge->getLogoUrl($badge->image);
	}

	public function getPublicRatingAttribute()
	{
		return $this->rating ? $this->rating : null;
	}

	public function badges()
  {
      return $this->belongsToMany('App\Models\Badge', 'badge_to_product', 'product_id', 'badge_id');
  }

	public function tags()
	{
			return $this->belongsToMany('App\Models\Tag', 'tag_to_product', 'product_id', 'tag_id');
	}

	public function getIsPendingAttribute()
	{
	    return $this->deal->count() == 0 && $this->prices->count() == 0 ? true : false;
  }

	public function getDatePublishTimestampAttribute()
	{
	    return Carbon::parse($this->date_publish)->timestamp;
	}

	public function ratings()
	{
		return $this->belongsToMany('App\Models\Rating', 'rating_product', 'product_id', 'rating_id')
			->withPivot('admin_rating', 'user_rating', 'votes');
		}

    /**
     * @return HasMany
     */
    public function specifications(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id', 'id')
            ->whereHas('attribute', function ($query) {
                $query->where('kind', Attribute::KIND_SPECIFICATION);
            });
    }
}
