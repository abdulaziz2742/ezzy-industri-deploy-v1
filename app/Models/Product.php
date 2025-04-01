<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'unit',
        'target_per_hour',
        'target_per_shift',
        'target_per_day',
        'cycle_time',  // Add this
        'product_code'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'target_per_hour' => 'integer',
        'target_per_shift' => 'integer',
        'target_per_day' => 'integer',
        'cycle_time' => 'decimal:2'  // Add this
    ];

    public function sops()
    {
        return $this->hasMany(Sop::class);
    }
}