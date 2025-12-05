<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Viewer extends Model
{
    use HasFactory;

    protected $fillable = [
        'products_id',
        'visitors'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'products_id',
    ];

    protected $casts = [
        'created_at' => 'date: d-m-Y',
        'updated_at' => 'date: d-m-Y',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }
}
