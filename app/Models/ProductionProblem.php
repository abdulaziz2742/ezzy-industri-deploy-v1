<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ProductionProblem extends Model
{
    protected $fillable = [
        'production_id',
        'problem_type',
        'notes',
        'image_path',
        'cloudinary_url',   // Add this
        'cloudinary_id',    // Add this
        'status',
        'reported_at',
        'approved_at',
        'resolved_at'
    ];
    protected $dates = [
        'reported_at',
        'approved_at',
        'resolved_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'approved_at' => 'datetime',
        'resolved_at' => 'datetime'
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    protected static function booted()
    {
        static::updating(function ($problem) {
            if ($problem->isDirty('resolved_at') && $problem->resolved_at) {
                $start = Carbon::parse($problem->reported_at);
                $end = Carbon::parse($problem->resolved_at);
                $problem->duration = $start->diffInMinutes($end);
            }
        });
    }
}