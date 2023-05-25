<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    use HasFactory;

    protected $table = 'attribute_group';
    protected $fillable = ['id', 'name', 'sort_order', 'repeatable', 'product_id', 'parent_id'];

    public function attributes()
    {
        return $this->hasMany('App\Models\Attribute', 'attribute_group_id')->orderBy('sort_order', 'ASC');
    }

    public function scopeChildren($query)
	{
		return $query->where('parent_id', $this->id);
	}

    public function scopeProductGroups($query, $product_id)
	{
		return $query->where('parent_id', $this->id)
                     ->where('product_id', $product_id);
	}
}
