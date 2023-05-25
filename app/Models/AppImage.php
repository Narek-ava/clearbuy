<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppImage extends Model
{
    use HasFactory;

    protected $table = 'app_image';
    protected $fillable = ['path', 'order'];
    public $timestamps = false;

    public function getUrlAttribute()
    {
        if ($this->path === null) {
            return null;
        }

        if (!Storage::disk('images')->exists($this->path)) {
            return null;
        }

        return Storage::disk('images')->url($this->path);
    }
}
