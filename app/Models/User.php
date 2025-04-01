<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Models\ChecksheetEntry;
use App\Models\MaintenanceTask;
use App\Models\Production;
use App\Models\QualityCheck;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'department_id',
        'shift_id',  // Add this
        'employee_id', // Add this
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class);
    }

    public function checksheetEntries()
    {
        return $this->hasMany(ChecksheetEntry::class);
    }
    // Quality Check Metrics
    // Rename method from getQualityCheckMetrics to getQualityMetrics
    public function getQualityMetrics($dateFrom = null, $dateTo = null)
    {
        $query = $this->qualityChecks();
        
        if ($dateFrom && $dateTo) {
            $startDate = $dateFrom . ' 00:00:00';
            $endDate = $dateTo . ' 23:59:59';
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Get productions in the date range
        $productions = $this->productions()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Calculate required checks based on production targets
        $requiredChecks = 0;
        foreach ($productions as $production) {
            $targetSets = $production->target_per_shift;
            $checkInterval = 10; // Interval pengecekan setiap 10 set
            $requiredChecks += ceil($targetSets / $checkInterval);
        }

        $completed = $query->count();
        $complianceRate = $requiredChecks > 0 ? min(100, ($completed / $requiredChecks) * 100) : 0;

        return [
            'completed' => $completed,
            'required' => $requiredChecks,
            'compliance_rate' => round($complianceRate, 1)
        ];
    }

    public function getMaintenanceMetrics($dateFrom, $dateTo)
    {
        // Get all active AM tasks for the current day
        $amTasks = MaintenanceTask::where('maintenance_type', 'am')
            ->where('is_active', true)
            ->whereDate('created_at', '<=', $dateTo)
            ->count();
        
        // Get all active PM tasks for the current day
        $pmTasks = MaintenanceTask::where('maintenance_type', 'pm')
            ->where('is_active', true)
            ->whereDate('created_at', '<=', $dateTo)
            ->count();
    
        // Get completed tasks for this user in date range
        $completedAM = $this->checksheetEntries()
            ->whereHas('task', function($query) {
                $query->where('maintenance_type', 'am');
            })
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
    
        $completedPM = $this->checksheetEntries()
            ->whereHas('task', function($query) {
                $query->where('maintenance_type', 'pm');
            })
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
    
        // Calculate compliance rate
        $totalRequired = $amTasks + $pmTasks;
        $totalCompleted = $completedAM + $completedPM;
        $complianceRate = $totalRequired > 0 ? round(($totalCompleted / $totalRequired) * 100, 1) : 0;
    
        return [
            'completed_am' => $completedAM,
            'completed_pm' => $completedPM,
            'required_am' => $amTasks,
            'required_pm' => $pmTasks,
            'required_total' => $totalRequired,
            'compliance_rate' => $complianceRate,
            'rate' => $complianceRate
        ];
    }
        // Production Metrics
    public function getProductionMetrics($dateFrom = null, $dateTo = null)
    {
        $query = $this->productions();
        
        if ($dateFrom && $dateTo) {
            // Tambahkan waktu ke tanggal
            $startDate = $dateFrom . ' 00:00:00';
            $endDate = $dateTo . ' 23:59:59';
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $productions = $query->get();
        $totalRuns = $productions->count();
        
        // Calculate achievement rate
        $achievedTargets = $productions->filter(function($production) {
            return $production->status === 'completed' && 
                   $production->total_production >= $production->target_per_shift;
        })->count();
            
        $achievementRate = $totalRuns > 0 ? ($achievedTargets / $totalRuns) * 100 : 0;

        // Calculate problems
        $problemCount = $productions->flatMap(function ($production) {
            return $production->problems;
        })->count();

        return [
            'total_runs' => $totalRuns,
            'achievement_rate' => round($achievementRate, 1),
            'problem_frequency' => $problemCount
        ];
    }

    // Performance Rate Calculation
    public function getPerformanceRateAttribute()
    {
        $qcRate = $this->qc_metrics['compliance_rate'] ?? 0;
        $maintenanceRate = $this->maintenance_metrics['compliance_rate'] ?? 0;
        $productionRate = $this->production_metrics['achievement_rate'] ?? 0;
        
        return round(($qcRate + $maintenanceRate + $productionRate) / 3, 1);
    }
        // Helper methods for detailed calculations
        private function calculateProductionAchievement($productions)
        {
            if ($productions->isEmpty()) {
                return 0;
            }
    
            $totalAchievement = 0;
            foreach ($productions as $production) {
                if ($production->target_per_shift > 0) {
                    $achievement = ($production->total_production / $production->target_per_shift) * 100;
                    $totalAchievement += min(100, $achievement);
                }
            }
    
            return round($totalAchievement / $productions->count(), 1);
        }
    
        private function calculateDowntimeRate($productions)
        {
            if ($productions->isEmpty()) {
                return 0;
            }
    
            $totalDowntime = $productions->sum('downtime_duration');
            $totalRuntime = $productions->sum('runtime_duration');
    
            return $totalRuntime > 0 ? round(($totalDowntime / $totalRuntime) * 100, 1) : 0;
        }
    
        private function calculateProblemRate($productions)
        {
            if ($productions->isEmpty()) {
                return 0;
            }
    
            $totalProblems = $productions->sum(function ($production) {
                return $production->problems()->count();
            });
    
            return round($totalProblems / $productions->count(), 1);
        }
    // Additional helper methods
    public function amChecksheets()
    {
        return $this->checksheetEntries()
            ->whereHas('task', function($query) {
                $query->where('maintenance_type', 'am');
            });
    }

    public function pmChecksheets()
    {
        return $this->checksheetEntries()
            ->whereHas('task', function($query) {
                $query->where('maintenance_type', 'pm');
            });
    }

    public function getActiveProductionsAttribute()
    {
        return $this->productions()
            ->where('status', 'active')
            ->orWhere('status', 'pause')
            ->get();
    }

    public function getCompletedProductionsAttribute()
    {
        return $this->productions()
            ->where('status', 'completed')
            ->get();
    }

    public function getProductionProblemsAttribute()
    {
        return $this->productions()
            ->with('problems')
            ->whereHas('problems')
            ->get();
    }

    public function getRejects($dateFrom = null, $dateTo = null)
    {
        $query = $this->qualityChecks();
        
        if ($dateFrom && $dateTo) {
            $startDate = $dateFrom . ' 00:00:00';
            $endDate = $dateTo . ' 23:59:59';
            $query->whereBetween('check_time', [$startDate, $endDate]);
        }

        return $query->where('status', 'ng')
                    ->with(['details' => function($query) {
                        $query->where('status', 'ng');
                    }])
                    ->get();
    }

    // Tambahkan relationship untuk QualityCheckDetail
    public function qualityCheckDetails()
    {
        return $this->hasManyThrough(
            QualityCheckDetail::class,
            QualityCheck::class,
            'user_id',
            'quality_check_id'
        );
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
} // Single closing bracket for the User class
