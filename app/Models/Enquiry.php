<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Enquiry extends Model
{
    use LogsActivity;
    protected $fillable = [
        "name",
        "email",
        "phone",
        "membership_id",
        "redemption_code",
        "subject",
        "reason",
        "description",
        "answer"
    ];

    protected $casts = [
        'created_at'=> 'date: d-m-Y',
        'updated_at'=> 'date: d-m-Y',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'answer', 'email'])
            ->logOnlyDirty()
            ->useLogName('enquiry');
    }
}
