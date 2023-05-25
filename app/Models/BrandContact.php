<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandContact extends Model
{
    use HasFactory;

    protected $table = 'brand_contact';
    protected $fillable = ['id', 'name', 'email', 'phone', 'role'];
    public $timestamps = false;

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id');
    }
}
