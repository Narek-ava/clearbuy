<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OS extends Model
{
    use HasFactory;

    protected $table = 'os';
    protected $fillable = ['id', 'name', 'description', 'is_kernel'];

    public function children()
    {
        return $this->hasMany('App\Models\OS', 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\OS', 'parent_id');
    }

    public function parentOSRelease()
    {
        return $this->belongsTo('App\Models\OSRelease', 'parent_os_release_id');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id');
    }

    public function licenseType()
    {
        return $this->belongsTo('App\Models\LicenseType', 'license_type_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'os_to_category', 'os_id', 'category_id');
    }

    public function releases()
    {
        return $this->hasMany('App\Models\OSRelease', 'os_id')->orderBy('release_date', 'ASC');
    }

    public function getVersionAttribute()
    {
        $lastRelease = $this->releases->last();
        if ($lastRelease) {
            return $lastRelease->version;
        }
        return null;
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

    static public function orderByColumn($column, $order = 'ASC')
    {
        $order = strtoupper($order) == 'ASC' ? 'ASC' : 'DESC';
        if (self::ownSortableColumns()->contains($column)) {
            return self::orderBy($column, $order);
        } else {
            return self::orderByRelation($column, $order);
        }
    }

    static protected function orderByRelation($column, $order)
    {
        if ($column == 'license') {
            return self::select('os.*')
                        ->join('license_type', 'license_type.id', '=', 'os.license_type_id')
                        ->orderBy('license_type.name', $order);
        } elseif ($column == 'brand') {
            return self::select('os.*')
                        ->join('brand', 'brand.id', '=', 'os.brand_id')
                        ->orderBy('brand.name', $order);
        } else {
            return self::select('os.*');
        }
    }

    static protected function ownSortableColumns()
    {
        return collect(['id', 'name', 'is_kernel']);
    }
}
