<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OeeRecord extends Model
{
    protected $fillable = [
        'production_id',
        'machine_id',
        'shift_id',
        'date',
        'planned_production_time',
        'operating_time',
        'downtime_problems',
        'downtime_maintenance',
        'total_downtime',
        'total_output',
        'good_output',
        'defect_count',
        'ideal_cycle_time',
        'availability_rate',
        'performance_rate',
        'quality_rate',
        'oee_score'
    ];

    // Relationships
    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // Add this method
    public function updateFromProduction(Production $production)
    {
        $this->production_id = $production->id;
        $this->machine_id = $production->machine_id;
        $this->shift_id = $production->shift_id;
        $this->date = $production->start_time->toDateString();
        
        // Calculate operating time (in minutes)
        $operatingTime = $production->start_time->diffInMinutes($production->end_time ?? now());
        $this->operating_time = $operatingTime;
        
        // Get downtime data
        $problemDowntime = $production->problems()->sum('duration') ?? 0;
        
        // Ganti dari:
        // $maintenanceDowntime = $production->downtimes()->where('type', 'maintenance')->sum('duration_minutes') ?? 0;
        
        // Menjadi:
        $maintenanceDowntime = $production->downtimes()
            ->where('reason', 'like', '%maintenance%')
            ->sum('duration_minutes') ?? 0;
        
        $this->downtime_problems = $problemDowntime;
        $this->downtime_maintenance = $maintenanceDowntime;
        $this->total_downtime = $problemDowntime + $maintenanceDowntime;
        
        // Get quality data
        $this->total_output = $production->total_output ?? 0;
        $this->good_output = $production->good_output ?? 0;
        $this->defect_count = $production->defect_count ?? 0;
        
        // Calculate OEE components
        $this->availability_rate = $this->calculateAvailability();
        $this->performance_rate = $this->calculatePerformance();
        $this->quality_rate = $this->calculateQuality();
        
        // Calculate final OEE score
        $this->oee_score = ($this->availability_rate * $this->performance_rate * $this->quality_rate) * 100;
        
        $this->save();
        
        return $this;
    }

    private function calculateAvailability()
    {
        $plannedTime = $this->planned_production_time ?? 480; // Default 8 hours
        $actualOperatingTime = $plannedTime - $this->total_downtime;
        return $actualOperatingTime > 0 ? $actualOperatingTime / $plannedTime : 0;
    }

    private function calculatePerformance()
    {
        if (!$this->total_output || !$this->ideal_cycle_time || !$this->operating_time) {
            return 0;
        }
        return ($this->total_output * $this->ideal_cycle_time) / ($this->operating_time * 60);
    }

    private function calculateQuality()
    {
        return $this->total_output > 0 ? ($this->good_output / $this->total_output) : 0;
    }
}