<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDetails extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'users_id',
        'username',
        'photo',
        'whatsapp',
        'website',
        'state',
        'zip',
        'address',
        'location',
        'facebook',
        'youtube',
        'tiktok',
        'twitter'
    ];
    protected $appends = ['photo_url'];

    public $timestamps = false;

    public $hidden = ['photo'];


    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }


    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return asset('frontend/default-avatar-profile.jpg');
        }

        // base URL from .env (APP_URL)
        $base = rtrim(env('APP_URL'), '/');

        // ensure storage link used
        return $base . '/storage/' . ltrim($this->photo, '/');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status'])
            ->logOnlyDirty()
            ->useLogName('user Details');
    }
}
