<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityCheckDetail extends Model
{
    protected $fillable = [
        'quality_check_id',
        'parameter',
        'standard_value',
        'measured_value',
        'tolerance_min',
        'tolerance_max',
        'status'
    ];

    protected $casts = [
        'standard_value' => 'decimal:6',
        'measured_value' => 'decimal:6',
        'tolerance_min' => 'decimal:6',
        'tolerance_max' => 'decimal:6'
    ];

    public function qualityCheck()
    {
        return $this->belongsTo(QualityCheck::class);
    }
}