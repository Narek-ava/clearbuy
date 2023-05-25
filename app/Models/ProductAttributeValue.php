<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $table = 'attribute_to_product';
    protected $fillable = ['attribute_id'];

    public function option()
    {
        return $this->belongsTo('App\Models\AttributeOption', 'attribute_option_id');
    }

    /**
     * @return HasOne
     */
    public function attribute(): HasOne
    {
        return $this->hasOne(Attribute::class, 'id', 'attribute_id');
    }
}
