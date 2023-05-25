<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductContent extends Model
{
    use HasFactory;

    protected $table = 'product_content';
    protected $fillable = ['id', 'type_id', 'title', 'url', 'description'];
    public $timestamps = false;

    public static function allowedTypes()
    {
        return collect([
            1 => 'article',
            2 => 'image',
            3 => 'video'
        ]);
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function getTypeAttribute()
    {
        $allowedTypes = self::allowedTypes();
        if ($allowedTypes->keys()->contains($this->type_id)) {
            return (object)[
                'id' => $this->type_id,
                'name' => $allowedTypes[$this->type_id]
            ];
        } else {
            return null;
        }
    }
}
