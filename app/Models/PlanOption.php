<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanOption extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'plan_id',
        'name',
        'type',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['offer_type', 'offer_des'])
            ->logOnlyDirty()
            ->useLogName('plan options');
    }
}
