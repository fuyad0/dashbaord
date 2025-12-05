<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'stores_id',
        'tags',
        'name',
        'photo',
        'price',
        'offer_type',
        'offer_des',
        'status',
    ];

    protected $casts = [
        'tags' => 'array',
        'created_at' => 'date: d-m-Y',
        'updated_at' => 'date: d-m-Y',
    ];

    protected $hidden = [
        'updated_at',
        'photo', //'status'
    ];

    protected $appends = ['photo_url'];


    public function store()
    {
        return $this->belongsTo(Store::class, 'stores_id');
    }

    public function viewers()
    {
        return $this->hasMany(Viewer::class, 'products_id');
    }

    public function categories()
    {
        return $this->hasMany(ProductCategory::class, 'products_id');
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }

    protected static function booted()
    {
        static::deleting(function ($product) {
            $product->categories()->delete();
            $product->viewers()->delete();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status'])
            ->logOnlyDirty()
            ->useLogName('product');
    }
}
