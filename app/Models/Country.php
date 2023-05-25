<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'country';
    protected $fillable = ['id', 'name'];

    public function brands()
    {
        return $this->hasMany('App\Models\Brand', 'country_id');
    }

    public function currencies()
    {
        return $this->hasMany('App\Models\Currency', 'country_id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'country_id');
    }

    public function targetProducts()
    {
        return $this->belongsToMany('App\Models\Product', 'product_to_country', 'country_id', 'product_id');
    }
}
