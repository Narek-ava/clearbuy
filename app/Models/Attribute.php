<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    public const KIND_SPECIFICATION = 1;
    public const KIND_ADDITIONAL_PRODUCT_INFO = 2;
    public const KIND_RATINGS = 3;

    protected $table = 'attribute';
    protected $fillable = ['id', 'name', 'type', 'kind', 'sort_order', 'attribute_group_id', 'measure_id', 'parent_id'];


    public static function types()
    {
        return collect([
            0 => 'numeric',
            1 => 'string',
            2 => 'boolean',
            3 => 'datetime',
            4 => 'single option',
            5 => 'multiple options',
            6 => 'decimal'
        ]);
    }

    public static function kindsList()
    {
        return collect([
            self::KIND_SPECIFICATION => 'Specifications',
            self::KIND_ADDITIONAL_PRODUCT_INFO => 'Additional Product info'
        ]);
    }

    public function getKindNameAttribute()
    {
        $kindsList = self::kindsList();
        if ($kindsList->keys()->contains($this->kind)) {
            return $kindsList[$this->kind];
        } else {
            return null;
        }
    }

    public function measure()
    {
        return $this->belongsTo('App\Models\Measure', 'measure_id');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\AttributeGroup', 'attribute_group_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'attribute_to_category', 'attribute_id', 'category_id');
    }

    public function options()
    {
        return $this->hasMany('App\Models\AttributeOption', 'attribute_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'attribute_to_product', 'attribute_id', 'product_id');
    }

    public function values()
    {
        return $this->hasMany('App\Models\ProductAttributeValue', 'attribute_id');
    }

    public function valueForProduct($product_id) //all attribute values for product
    {
        if (!$product_id) {
            return null;
        }
        $values = $this->values()->where('product_id', $product_id)->get();
        if ($values->count() > 0) {
            switch ($this->type) {
                case 0:
                    return $values[0]->value_numeric;
                    break;
                case 1:
                    return $values[0]->value_text;
                    break;
                case 2:
                    return $values[0]->value_boolean;
                    break;
                case 3:
                    return $values[0]->value_date;
                    break;
                case 4:
                    return $values[0]->option;
                    break;
                case 5:
                    return $values->map(function($v) {
                        return $v->option;
                    })->filter(function($v) {
                        return $v !== null;
                    });
                    break;
                case 6:
                    return $values[0]->value_numeric;
                    break;
            }
        }
        return null;
    }

    public function getProductValue($product_id) //for conrete product attribute
    {
        $values = $this->values()->where('product_id', $product_id)->get();

        if ($values->count() > 0) {
            switch ($this->type) {
                case 0:
                    return $values[0]->value_numeric;
                    break;
                case 1:
                    return $values[0]->value_text;
                    break;
                case 2:
                    return $values[0]->value_boolean;
                    break;
                case 3:
                    return $values[0]->value_date;
                    break;
                case 4:
                    return $values[0]->option;
                    break;
                case 5:
                    return $values->map(function($v) {
                        return $v->option()->where('id', $v->attribute_option_id)->get();
                    });
                    break;
                case 6:
                    return $values[0]->value_numeric;
                    break;
            }
        }

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
        if ($column == 'measure') {
            return self::select('attribute.*')
                        ->join('measure', 'measure.id', '=', 'attribute.measure_id')
                        ->orderBy('measure.name', $order);
        } else {
            return self::select('attribute.*');
        }
    }

    static protected function ownSortableColumns()
    {
        return collect(['id', 'name', 'type', 'kind', 'sort_order']);
    }

    public function scopeChildren($query)
    {
        return $query->where('parent_id', $this->id);
    }

}
