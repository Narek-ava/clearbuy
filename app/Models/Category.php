<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';
    protected $fillable = ['id', 'name'];

    public function children()
    {
        return $this->hasMany('App\Models\Category', 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Category', 'parent_id');
    }

    public function attributes()
    {
        return $this->belongsToMany('App\Models\Attribute', 'attribute_to_category', 'category_id', 'attribute_id');
    }

    public function featuredAttributes()
    {
        return $this->belongsToMany('App\Models\Attribute', 'attribute_to_category', 'category_id', 'attribute_id')->where('featured', 1);
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'category_id');
    }

    public function os()
    {
        return $this->belongsToMany('App\Models\OS', 'os_to_category', 'category_id', 'os_id');
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
        return self::select('category.*');
    }

    static protected function ownSortableColumns()
    {
        return collect(['id', 'name']);
    }

    static public function listWithFullPath($excludeId = 0, $categories = null)
    {
        if ($categories === null) {
            $categories = self::doesntHave('parent')->orderBy('name', 'ASC')->get();
        }

        $list = collect([]);
        foreach ($categories as $category) {
            $item = (object)['id' => $category->id, 'name' => $category->name];
            if ($category->id != $excludeId) {
                $list->push($item);
                if ($category->children) {
                    $list = $list->concat(self::listWithFullPath($excludeId, $category->children)->map(function($val) use ($item) {
                        $val->name = $item->name.' > '.$val->name;
                        return $val;
                    }));
                }
            }
        }
        return $list;
    }

    /**
     * @return HasMany
     */
    public function specifications(): HasMany
    {
        return $this->hasMany(AttributeToCategory::class, 'category_id', 'id')
            ->whereHas('attribute', function ($query) {
                $query->where('kind', Attribute::KIND_SPECIFICATION);
            });
    }
}
