<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AttributeToCategory extends Model
{
    use HasFactory;

    protected $table = 'attribute_to_category';

    /**
     * @return HasOne
     */
    public function attribute(): HasOne
    {
        return $this->hasOne(Attribute::class, 'id', 'attribute_id');
    }
}
