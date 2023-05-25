<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    use HasFactory;

    protected $table = 'people';
    protected $fillable = ['id', 'name', 'surname'];

    public function films()
    {
        return $this->belongsToMany('App\Models\Film', 'people_film', 'people_id', 'film_id');
    }

    public function getFullNameAttribute()
    {
        return $this->name.' '.$this->surname;
    }
}
