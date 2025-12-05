<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $fillable = [
        'user_id',
        'coupon_id',
        'store_id',
        'discount_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
