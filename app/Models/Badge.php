<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Badge extends Model
{
    use HasFactory;

    protected $table = 'badge';
    protected $fillable = ['id', 'name', 'year', 'image'];

    public function getLogoUrl($value)
    {
        if (is_null($value)) {
            return null;
        }

        if(Str::contains($value, 'http')) {
            return $value;
        }

        try{
            return Storage::disk('do_image_spaces')->temporaryUrl($value, now()->addMinutes(10));
        }catch(\Exception $e){}
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'badge_to_product', 'badge_id', 'product_id');
    }

}
