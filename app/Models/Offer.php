<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory, LogsActivity;

    public $timestamps = false;

    protected $fillable = [
        'stores_id',
        'offer_type',
        'offer_des',
        'status'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'stores_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['offer_type', 'offer_des'])
            ->logOnlyDirty()
            ->useLogName('offer');
    }
}
