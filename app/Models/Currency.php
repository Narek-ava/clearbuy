<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currency';
    protected $fillable = ['id', 'name', 'symbol', 'country_ids'];

    

    public function getCountryIdsAttribute($value)
    {
        return explode(',', $value);
    }

    public function countryNames()
    { 
        $countries =  Country::select('name')->whereIn('id', $this->country_ids)->get();
        $countries = $countries->map(function($country) { if($country->name)return $country->name; })->toArray();
        return implode(', ', $countries);
    }

    public function country()
    { 
        return Country::whereIn('id', $this->country_ids)->get();
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
            return self::select('currency.*')
                        ->join('country', 'country.id', '=', 'currency.country_id')
                        ->orderBy('country.name', $order);
        } else {
            return self::select('currency.*');
        }
    }

    static protected function ownSortableColumns()
    {
        return collect(['id', 'name']);
    }
}
