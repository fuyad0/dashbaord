<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'code',
        'type',
        'name',
        'email',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $hidden = [
        'usage_limit',
        'updated_at',
    ];

    public function isValid(): bool
    {
        $now = Carbon::now();
        return $this->is_active &&
            ($this->created_at == null || $this->created_at <= $now) &&
            ($this->expires_at == null || $this->expires_at >= $now) &&
            ($this->usage_limit == null || $this->used_count < $this->usage_limit);
    }

    public function couponUsage(){
        return $this->hasMany(CouponUsage::class);
    }

    protected $casts = [
        'created_at' => 'datetime: d-m-Y h:m',
        'expires_at' => 'datetime: d-m-Y h:m',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'code', 'is_active'])
            ->logOnlyDirty()
            ->useLogName('coupon');
    }
}
