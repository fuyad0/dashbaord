<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'price',
        'type',
        'duration',
        'stripe_product_id',
        'stripe_price_id',
        'interval',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    

    public static function published()
    {
        return self::query()->where('status', 'Active');
    }

    public function planOptions()
    {
        return $this->hasMany(PlanOption::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }


    public function payment(){
        return $this->hasMany(Subscription::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status'])
            ->logOnlyDirty()
            ->useLogName('plan');
    }
}
