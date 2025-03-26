<?php

namespace App\Livewire\Karyawan\Production;

use Livewire\Component;
use App\Models\Production;
use Livewire\Attributes\Polling;
use Illuminate\Support\Facades\Log; 
use App\Models\ProductionSopCheck;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductionDowntime;
use App\Models\Sop;
use App\Models\SopStep;
use App\Models\QualityCheck;
use App\Models\QualityCheckDetail;




class ProductionStatus extends Component
{
    // Add these properties
    public $downtimeReason;
    public $problemDescription;

    public $activeProduction;
    public $qualityChecks;
    public $checkProgress = 0;
    public $completedChecks = 0;
    public $totalChecksNeeded = 0;
    public $intervalCheck = 0;

    public function calculateQualityProgress()
    {
        if ($this->activeProduction) {
            $masterSop = Sop::where('product_id', $this->activeProduction->product_id)
                          ->where('kategori', 'quality')
                          ->where('is_active', true)
                          ->first();
    
            if ($masterSop) {
                // Ambil step pertama yang memiliki interval
                $qualityStep = SopStep::where('sop_id', $masterSop->id)
                    ->whereNotNull('interval_value')
                    ->first();
    
                Log::info('Quality Step Detail', [
                    'step_found' => $qualityStep ? true : false,
                    'step_id' => $qualityStep ? $qualityStep->id : null,
                    'interval' => $qualityStep ? $qualityStep->interval_value : null
                ]);
    
                if ($qualityStep) {
                    $this->intervalCheck = $qualityStep->interval_value;
                    $targetPerShift = $this->activeProduction->target_per_shift;
                    $this->totalChecksNeeded = ceil($targetPerShift / $this->intervalCheck);
                    
                    // Hitung berdasarkan quality_check_id yang unik
                    $this->completedChecks = QualityCheck::where('production_id', $this->activeProduction->id)
                        ->count();
    
                    Log::info('Progress Calculation', [
                        'target' => $targetPerShift,
                        'interval' => $this->intervalCheck,
                        'total_needed' => $this->totalChecksNeeded,
                        'completed_checks' => $this->completedChecks
                    ]);
    
                    $this->checkProgress = $this->totalChecksNeeded > 0 
                        ? min(100, round(($this->completedChecks / $this->totalChecksNeeded) * 100))
                        : 0;
                }
            }
        }
    }

    public $reason;
    public $notes;
    public $activeDowntime;
    
    public function render()
    {
        $this->activeProduction = Production::where('user_id', Auth::id())
            ->whereIn('status', ['running', 'problem', 'waiting_approval', 'paused'])
            ->first();
    
        if ($this->activeProduction) {
            $this->calculateQualityProgress();
            
            // Update the query to properly load the relationships
            $this->qualityChecks = QualityCheck::with(['details', 'user'])
                ->where('production_id', $this->activeProduction->id)
                ->orderBy('check_time', 'desc')
                ->get();
        }
    
        return view('livewire.karyawan.production.production-status', [
            'activeProduction' => $this->activeProduction,
            'qualityChecks' => $this->qualityChecks ?? collect([])
        ]);
    }


    protected $listeners = [
        'refresh-production-status' => '$refresh',
        'problemResolved' => '$refresh'
    ];

    public function mount()
    {
        $this->activeProduction = Production::where('status', '!=', 'finished')
            ->with(['product', 'machine'])
            ->latest()
            ->first();

        if ($this->activeProduction) {
            $this->activeDowntime = ProductionDowntime::where('production_id', $this->activeProduction->id)
                ->whereNull('end_time')
                ->first();
            $this->calculateQualityProgress();
        }
    }

    public function pauseProduction()
    {
        $this->dispatch('openDowntimeModal');
    }

    public function saveDowntime()
    {
        $this->validate([
            'reason' => 'required'
        ]);

        $downtime = ProductionDowntime::create([
            'production_id' => $this->activeProduction->id,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'start_time' => now()
        ]);

        $this->activeProduction->update(['status' => 'paused']);
        $this->activeDowntime = $downtime;
        
        $this->dispatch('closeModal');
        $this->reset(['reason', 'notes']);
    }

    public function resumeProduction()
    {
        if ($this->activeDowntime) {
            $this->activeDowntime->update([
                'end_time' => now(),
                'duration_minutes' => now()->diffInMinutes($this->activeDowntime->start_time)
            ]);
        }

        $this->activeProduction->update(['status' => 'running']);
        $this->activeDowntime = null;
    }

    public function resolveProblem()
    {
        $this->activeProduction->update(['status' => 'running']);
        
        $problem = $this->activeProduction->problems()->latest()->first();
        if ($problem) {
            $problem->update([
                'status' => 'resolved',
                'resolved_at' => now()
            ]);
        }

        $this->activeProduction->refresh();
        $this->dispatch('refresh-production-status');
    }

    public function recordDowntime()
    {
        // ... your existing code ...
        
        $this->downtimeReason = ''; // Reset after successful submission
    }
    
    public function reportProblem()
    {
        // ... your existing code ...
        
        $this->problemDescription = ''; // Reset after successful submission
    }
}