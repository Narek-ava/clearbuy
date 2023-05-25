<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Traits\NullableFields;

class Brand extends Model
{
    use HasFactory, NullableFields;

    protected $table = 'brand';
    protected $fillable = ['id', 'name', 'image', 'website', 'bio', 'country_id'];

    public function setCountryIdAttribute($value)
    {
        $this->attributes['country_id'] = $this->nullIfEmpty($value);
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'brand_id');
    }

    public function contacts()
    {
        return $this->hasMany('App\Models\BrandContact', 'brand_id');
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
            return self::select('brand.*')
                        ->join('country', 'country.id', '=', 'brand.country_id')
                        ->orderBy('country.name', $order);
        } else {
            return self::select('brand.*');
        }
    }

    static protected function ownSortableColumns()
    {
        return collect(['id', 'name']);
    }

    public function getLogoUrl($value)
    {
        if (is_null($value)) {
            return null;
        }

        if(Str::contains($value, 'http')) {
            return $value;
        }

        try{
            return Storage::disk('do_image_spaces')->temporaryUrl($value, now()->addMinutes(10));
        }catch(\Exception $e){}
    }
}
