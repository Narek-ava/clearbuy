<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Website extends Model
{
    use HasFactory;

    protected $table = 'website';
    protected $fillable = ['id', 'name', 'logo', 'url', 'description'];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'product_to_website', 'website_id', 'product_id');
    }

    public function getLogoAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        if(Str::contains($value, 'http')) {
            return $value;
        }

        return Storage::disk('do_image_spaces')->temporaryUrl($value, now()->addMinutes(10));
    }
}
