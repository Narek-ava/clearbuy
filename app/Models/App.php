<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class App extends Model
{
    use HasFactory;

    protected $table = 'app';
    protected $fillable = ['id', 'name', 'price', 'change_log_url', 'type_id', 'brand_id', 'logo', 'video_url', 'description'];

    public static function types()
    {
        return collect([
            0 => 'App',
            1 => 'Game'
        ]);
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id');
    }

    public function countries()
    {
        return $this->belongsToMany('App\Models\Country', 'app_to_country', 'app_id', 'country_id');
    }

    public function os()
    {
        return $this->belongsToMany('App\Models\OS', 'app_to_os', 'app_id', 'os_id');
    }

    public function links()
    {
        return $this->hasMany('App\Models\AppLink', 'app_id');
    }

    public function images()
    {
        return $this->hasMany('App\Models\AppImage', 'app_id')->orderBy('order', 'ASC');
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

    public function getImages()
    {
        if(!empty($this->images)){
            return collect($this->images)->map(function($image){

                if ($image->path != "" && Storage::disk('do_image_spaces')->exists($image->path)) {
                    return Storage::disk('do_image_spaces')->temporaryUrl(
                        $image->path,
                        now()->addMinutes(10)
                    );
                }

             });
        }

        return [];
    }

    public function getOses()  //for API
    {
        $ar_os = [];
        $os = $this->os()->get();

        if($os->isNotEmpty()) {
            foreach($os as $s) {
                $ar_os[$s->id] = [
                    'name' => $s->name,
                    'image' => $s->image
                ];
            }
        }
        return $ar_os;
    }

    public function getCountries()  //for API
    {
        $ar_countries = [];
        $ctrs = $this->countries()->get();

        if($ctrs->isNotEmpty()) {
            foreach($ctrs as $c) {
                $ar_countries[$c->id] = [
                    'name' => $c->name,
                ];
            }
        }
        return $ar_countries;
    }

    public function getStoreLinks()  //for API
    {
        $ar_links = [];
        $links = $this->links()->get();

        if($links->isNotEmpty()) {
            foreach($links as $link) {
                $ar_links[$link->app_id] = [
                    'store_name' => (string) $link->store->name,
                    'store_url' => (string) $link->url,
                    'icon' => $link->store->icon ,
                    'price' => (float) $link->price,
                    'currency' => (string) $link->currency->symbol,
                    'free' => (bool)$link->free,
                    'app_purchase' => (bool)$link->app_purchase,
                ];
            }
        }
        return $ar_links;
    }
}
