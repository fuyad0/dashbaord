<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'email',
        'number',
        'system_name',
        'address',
        'copyright_text',
        'logo',
        'favicon',
        'description',
        'facebook',
        'twitter',
        'linkedin',
        'instagram'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getLogoAttribute($value){
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return url(Storage::url($value));
        }
        if (request()->is('api/*') && !empty($value)) {
            return url(Storage::url($value));
        }
        return $value;
    }

    public function getFaviconAttribute($value){
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return url(Storage::url($value));
        }
        if (request()->is('api/*') && !empty($value)) {
            return url(Storage::url($value));
        }
        return $value;
    }

    protected static function booted()
    {
        static::updating(function ($systemSetting) {
            if ($systemSetting->isDirty('favicon')) {
                $oldFavicon = $systemSetting->getOriginal('favicon');
                if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                    Storage::disk('public')->delete($oldFavicon);
                }
            }

            if ($systemSetting->isDirty('logo')) {
                $oldLogo = $systemSetting->getOriginal('logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
            }
        });

    }

}
