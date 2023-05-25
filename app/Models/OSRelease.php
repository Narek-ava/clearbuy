<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OSRelease extends Model
{
    use HasFactory;

    protected $table = 'os_release';
    protected $fillable = ['id', 'version', 'added_features', 'release_date'];

    public function getAddedFeaturesAttribute($data)
    {
        if ($data) {
            return explode("\n", $data);
        } else {
            return [];
        }
    }

    public function setAddedFeaturesAttribute($data)
    {
        if ($data) {
            $this->attributes['added_features'] = implode("\n", $data);
        } else {
            $this->attributes['added_features'] = "";
        }
    }
}
