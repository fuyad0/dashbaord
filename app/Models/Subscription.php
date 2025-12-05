<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'plan_id',
        'type',
        'stripe_id',
        'stripe_price',
        'stripe_status',
        'quantity',
        'trial_ends_at',
        'ends_at',
        'processed'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at'=> 'date: d-m-Y',
        'updated_at'=> 'date: d-m-Y',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'status'])
            ->logOnlyDirty()
            ->useLogName('subscription');
    }


}
