<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductCategory extends Model
{
    use LogsActivity;
    public $timestamps = false;
    protected $fillable = [
        'products_id',
        'category'
    ];

    protected $hidden = [
        'products_id',
    ];
    public function store()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product_id', 'category'])
            ->logOnlyDirty()
            ->useLogName('product category');
    }
}
