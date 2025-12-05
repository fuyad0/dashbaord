<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Review extends Model
{ 
    use LogsActivity;
    protected $fillable = [
        "users_id",
        "ratings",
        "comment",
        "company",
        "status"
    ];

    protected $casts = [
        'created_at'=> 'date: d-m-Y',
        'updated_at'=> 'date: d-m-Y',
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['ratings', 'comment'])
            ->logOnlyDirty()
            ->useLogName('review');
    }
}
