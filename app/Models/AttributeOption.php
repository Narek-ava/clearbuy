<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    use HasFactory;

    protected $table = 'attribute_option';
    protected $fillable = ['name', 'attribute_id'];

    public function attribute()
    {
        return $this->belongsTo('App\Models\Attribute', 'attribute_id');
    }
}
