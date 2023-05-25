<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tag';
    protected $fillable = ['id', 'name'];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'tag_to_product', 'tag_id', 'product_id');
    }
}
