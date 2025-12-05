<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class Store extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status'])
            ->logOnlyDirty()
            ->useLogName('store');
    }

    protected $fillable = [
        'users_id',
        'type',
        'name', 
        'slug', 
        'slogan', 
        'logo', 
        'banner',
        'facebook',
        'youtube',
        'tiktok', 
        'twitter',
        'email', 
        'phone', 
        'whatsapp', 
        'website',
        'address',
        'details', 
        'reservation',
        'longitude',
        'latitude',
        'status'
    ];

    protected $casts = [
        'reservation' => 'boolean',
        'created_at'=> 'date: d-m-Y',
        'updated_at'=> 'date: d-m-Y',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function products() {
        return $this->hasMany(Product::class, 'stores_id');
    }

    public function offers() {
        return $this->hasMany(Offer::class, 'stores_id');
    }

    public function availabilities() {
        return $this->hasMany(Availability::class, 'stores_id');
    }

    public function couponUsage(){
        return $this->hasMany(CouponUsage::class);
    }

    protected $appends = ['logourl', 'bannerurl'];

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    public function getBannerUrlAttribute()
    {
        if ($this->banner) {
            return asset('storage/' . $this->banner);
        }
        return null;
    }


    protected static function booted()
    {
        static::deleting(function ($store) {
            $store->products()->each(function ($product) {
                $product->categories()->delete();
                $product->viewers()->delete();
                $product->delete();
            });

            $store->offers()->delete();
            $store->availabilities()->delete();
        });
    }
}
