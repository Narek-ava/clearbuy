<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLink extends Model
{
    use HasFactory;

    protected $table = 'product_link';
    protected $fillable = ['agent_id', 'currency_id', 'price_old', 'price_new', 'link', 'description', 'is_primary'];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_id');
    }

    public function agent()
    {
        return $this->belongsTo('App\Models\Agent', 'agent_id');
    }
}
