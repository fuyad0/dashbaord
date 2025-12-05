<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, Billable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'number',
        'address',
        'dob',
        'password',
        'role',
        'is_online',
        'is_agree'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_online' => 'boolean',
            'dob' => 'date',
            'is_agree'=> 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
        "reset_code",
        "reset_code_expires_at",
    ];

    protected $appends = [
        'subscription_status',
        'name',
        'avatar'
    ];

    public function userDetails()
    {
        return $this->hasOne(UserDetails::class, 'users_id', 'id');
    }

    public function store()
    {
        return $this->hasMany(Store::class, 'users_id');
    }

    public function subscribed(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    public function emaillog(){
        return $this->hasMany(EmailLog::class);
    }

    public function couponUsage(){
        return $this->hasMany(CouponUsage::class);
    }

     public function review(){
        return $this->hasMany(Review::class, 'users_id');
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    // accessor for avatar (photo from user_details)
    public function getAvatarAttribute()
    {
        $default = env('APP_URL') . '/frontend/default-avatar-profile.jpg';

        // Check if relation exists and has a photo
        $photo = $this->userDetails?->photo;

        if (empty($photo)) {
            return $default;
        }

        // If photo is already a full URL, return as-is
        if (filter_var($photo, FILTER_VALIDATE_URL)) {
            return $photo;
        }

        // Otherwise, return full URL using Storage facade
        return env('APP_URL') . Storage::url($photo);
    }



    public function getDobAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }

    public function getSubscriptionStatusAttribute()
    {
        $check = $this->subscribed()
            ->where('stripe_status', 'active')
            ->where('ends_at', '>', now())
            ->select('id', 'user_id', 'plan_id', 'stripe_status', 'ends_at')
            ->first();

        return $check;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'status'])
            ->logOnlyDirty()
            ->useLogName('user');
    }


}
