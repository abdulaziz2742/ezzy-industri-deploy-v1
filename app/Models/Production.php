<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\QualityCheck;
use Illuminate\Support\Facades\Log;


class Production extends Model
{
    protected $fillable = [
        'user_id',
        'machine_id',
        'machine',
        'product_id',
        'product',
        'shift_id',
        'start_time',
        'end_time',
        'status',
        'batch_number', // Add this
        'total_production',  // Pastikan field ini ada
        'defect_count',
        'defect_type',
        'notes',
        'target_per_shift',
        'planned_production_time',
        'cycle_time'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'cycle_time' => 'decimal:2',
        'planned_production_time' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function problems()
    {
        return $this->hasMany(ProductionProblem::class);
    }
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
    public function checks()
    {
        return $this->hasMany(ProductionCheck::class);
    }

    public function sopChecks()
    {
        return $this->hasMany(ProductionSopCheck::class);
    }

    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class);
    }

    // Add this relationship method
    public function productionDowntimes()
    {
        return $this->hasMany(ProductionDowntime::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function checksheetEntries()
    {
        return $this->hasMany(ChecksheetEntry::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getProductionDetails()
    {
        Log::info('Production Details:', [
            'id' => $this->id,
            'planned_time' => $this->planned_production_time,
            'total_output' => $this->total_output,
            'defect_count' => $this->defect_count,
            'ng_records' => $this->qualityChecks()->where('is_ng', true)->count(),
            'problems' => $this->problems()->get(['id', 'duration']),
            // Fix: Change downtimes() to productionDowntimes() to match the relationship name
            'maintenance' => $this->productionDowntimes()->get(['id', 'duration_minutes'])
        ]);
    }
    public function oeeRecord()
    {
        return $this->hasOne(OeeRecord::class);
    }

    public static function generateBatchNumber($shift, $product)
    {
        $date = now()->format('ymd');
        $productCode = $product->code;
        
        // Get last sequence number for today
        $lastProduction = self::whereDate('created_at', today())
            ->where('shift_id', $shift->id)
            ->where('product_id', $product->id)
            ->orderBy('id', 'desc')
            ->first();
            
        $sequence = $lastProduction ? 
            (int)substr($lastProduction->batch_number, -3) + 1 : 
            1;
            
        return sprintf("%s-%d-%s-%03d", 
            $date,
            $shift->id,
            $productCode,
            $sequence
        );
    }
}