<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Film extends Model
{
    use HasFactory;

    public static function types()
    {
        return collect([
            0 => 'movie',
            1 => 'series',
        ]);
    }

    protected $table = 'film';
    protected $fillable = ['id'];

    public function director()
    {
        return $this->belongsTo('App\Models\People', 'director_id');
    }

    public function writer()
    {
        return $this->belongsTo('App\Models\People', 'writer_id');
    }

    public function producer()
    {
        return $this->belongsTo('App\Models\People', 'producer_id');
    }

    public function production_company()
    {
        return $this->belongsTo('App\Models\People', 'production_company_id');
    }

    public function age_rating()
    {
        return $this->belongsTo('App\Models\AgeRating', 'age_rating_id');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\FilmReview', 'film_id');
    }

    public function actors()
    {
        return $this->belongsToMany('App\Models\People', 'people_film', 'film_id', 'people_id');
    }

    public function genres()
    {
        return $this->belongsToMany('App\Models\FilmGenre', 'film_to_genre', 'film_id', 'genre_id');
    }

    public function websites()
    {
        return $this->belongsToMany('App\Models\Website', 'film_to_website', 'film_id', 'website_id')
                    ->withPivot('link');
    }

    public function recomendations()
    {
        return $this->belongsToMany('App\Models\Film', 'film_recomendation', 'film_parent_id', 'film_child_id');
    }

    public function getImageUrlAttribute()
    {
        if ($this->image === null) {
            return null;
        }

        if (!Storage::disk('images')->exists($this->image)) {
            return null;
        }

        return Storage::disk('images')->url($this->image);
    }

    public function getTypeAttribute()
    {
        return (object)[
            'id' => (int)$this->type_id,
            'name' => self::types()[(int)$this->type_id]
        ];
    }

    public function getReleaseDateAttribute($date)
    {
        return (new \DateTime($date))->format("Y-m-d");
    }
}
