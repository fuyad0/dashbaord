<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Availability extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'stores_id',
        'day',
        'time_start',
        'time_end'
    ];

    protected $casts = [
        'time_start' => 'datetime:H:i',
        'time_end' => 'datetime:H:i',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'stores_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['day', 'time_start', 'time_end', 'stores_id'])
            ->logOnlyDirty()
            ->useLogName('availablility');
    }
}
