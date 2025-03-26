<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityCheck extends Model
{
    protected $fillable = [
        'production_id',
        'user_id',
        'check_time',
        'sample_size',
        'status',
        'notes',
        'defect_count',
        'defect_type',
        'defect_notes'
    ];

    protected $casts = [
        'check_time' => 'datetime',
        'sample_size' => 'integer',
        'defect_count' => 'integer'
    ];

    public function details()
    {
        return $this->hasMany(QualityCheckDetail::class);
    }

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}