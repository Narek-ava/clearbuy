<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_image';
    protected $fillable = ['path', 'order', 'source_url', 'source_name'];
    public $timestamps = false;

    public function getUrlAttribute()
    {
        if ($this->path === null) {
            return null;
        }

        if (!Storage::disk('images')->exists($this->path)) {
            return null;
        }

        return Storage::disk('images')->url($this->path);
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}
