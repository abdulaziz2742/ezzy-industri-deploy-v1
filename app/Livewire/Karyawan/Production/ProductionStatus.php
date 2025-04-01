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
use App\Models\NGReport;



class ProductionStatus extends Component
{
    // Move these properties to class level
    public $ngReport = [];
    public $productionId;
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
        $this->downtimeReason = ''; // Reset after successful submission
    }

    // Move these methods outside of recordDowntime
    public function openNGReportModal()
    {
        $production = Production::with('product')->find($this->activeProduction->id);
        
        $this->ngReport = [
            'date' => now()->toDateString(),
            'operator_name' => Auth::user()->name,
            'employee_id' => Auth::user()->employee_id,
            'machine_name' => $production->machine,
            'shift' => $production->shift_id,
            'batch_number' => $production->batch_number,
            'product_name' => $production->product,
            // hapus product_code dari sini
            'total_production' => $production->total_output ?? 0,
            'total_ng' => 0,
            'ng_percentage' => '0%'
        ];

        $this->dispatch('open-modal', 'ngReportModal');
    }

    public function saveNGReport()
    {
        try {
            // Debug data yang diterima
            Log::info('NG Report Data before validation:', [
                'ngReport' => $this->ngReport,
                'activeProduction' => $this->activeProduction->id
            ]);

            $this->validate([
                'ngReport.total_ng' => 'required|numeric|min:0',
                'ngReport.ng_type' => 'required',
                'ngReport.what' => 'required',
                'ngReport.why' => 'required',
                'ngReport.where' => 'required',
                'ngReport.when' => 'required',
                'ngReport.who' => 'required',
                'ngReport.how' => 'required',
                'ngReport.countermeasure' => 'required',
                'ngReport.preventive_action' => 'required',
                'ngReport.pic' => 'required',
            ]);

            Log::info('Validation passed');

            // Calculate NG percentage
            if ($this->ngReport['total_production'] > 0) {
                $this->ngReport['ng_percentage'] = round(($this->ngReport['total_ng'] / $this->ngReport['total_production']) * 100, 2);
            }

            // Debug data sebelum create
            Log::info('Creating NG Report with data:', [
                'production_id' => $this->activeProduction->id,
                'ng_data' => $this->ngReport
            ]);

            // Create NG Report
            $ngReport = NGReport::create([
                'production_id' => $this->activeProduction->id,
                'date' => $this->ngReport['date'],
                'operator_name' => $this->ngReport['operator_name'],
                'employee_id' => $this->ngReport['employee_id'],
                'machine_name' => $this->ngReport['machine_name'],
                'shift' => $this->ngReport['shift'],
                'batch_number' => $this->ngReport['batch_number'],
                'product_name' => $this->ngReport['product_name'],
                // hapus product_code dari sini
                'total_production' => $this->ngReport['total_production'],
                'total_ng' => $this->ngReport['total_ng'],
                'ng_percentage' => $this->ngReport['ng_percentage'],
                'ng_type' => $this->ngReport['ng_type'],
                'ng_type_other' => $this->ngReport['ng_type_other'] ?? null,
                'what' => $this->ngReport['what'],
                'why' => $this->ngReport['why'],
                'where' => $this->ngReport['where'],
                'when' => $this->ngReport['when'],
                'who' => $this->ngReport['who'],
                'how' => $this->ngReport['how'],
                'countermeasure' => $this->ngReport['countermeasure'],
                'preventive_action' => $this->ngReport['preventive_action'],
                'pic' => $this->ngReport['pic'],
                'status' => 'pending'
            ]);

            Log::info('NG Report created successfully', ['report_id' => $ngReport->id]);

            $this->dispatch('close-modal', 'ngReportModal');
            $this->dispatch('show-toast', ['type' => 'success', 'message' => 'Laporan NG berhasil disimpan']);
            $this->reset('ngReport');

        } catch (\Exception $e) {
            Log::error('Error saving NG Report: ' . $e->getMessage());
            $this->dispatch('show-toast', ['type' => 'error', 'message' => 'Gagal menyimpan laporan: ' . $e->getMessage()]);
        }
    }

    public function reportProblem()
    {
        $this->problemDescription = ''; // Reset after successful submission
    }
}