<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class Agent extends Model
{
    use HasFactory;

    protected $table = 'agent';
    protected $fillable = ['id', 'name', 'image', 'surname', 'website', 'is_retailer', 'type_id', 'countries'];
    protected $casts = [
        'countries' => 'array'
    ];

    public static function types()
    {
        return collect([
            0 => 'legal entity',
            1 => 'individual'
        ]);
    }

    public function links()
    {
        return $this->hasMany('App\Models\ProductLink', 'agent_id');
    }

    public function deal()
    {
        return $this->hasMany('App\Models\ProductDealPrice', 'agent_id');
    }

    public function prices()
    {
        return $this->hasMany('App\Models\ProductPrice', 'agent_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'product_link', 'agent_id', 'product_id');
    }

    public function getTypeAttribute()
    {
        return (object)[
            'id' => (int)$this->type_id,
            'name' => self::types()[(int)$this->type_id]
        ];
    }

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

    public function getFullNameAttribute()
    {
        return $this->name.' '.$this->surname;
    }
}
