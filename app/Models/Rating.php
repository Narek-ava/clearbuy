<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';
    protected $fillable = ['id', 'name', 'sort_order'];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'rating_product', 'rating_id', 'product_id');
    }
}
