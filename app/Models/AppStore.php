<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppStore extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'brand_id', 'url', 'icon'];

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id');
    }

}
