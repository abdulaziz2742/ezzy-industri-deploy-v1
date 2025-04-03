<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use App\Models\Production;
use App\Models\ProductionDowntime;
use App\Models\ProductionProblem;
use App\Models\OeeRecord;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    public $todayProduction;
    public $todayDefects;
    public $activeProduction;
    public $totalDowntime;
    public $recentDowntimes;
    public $recentProblems;
    
    // Tambah property baru
    public $selectedPeriod = 'today';
    public $startDate;
    public $endDate;
    public $oeeData;
    public $productionTarget;
    public $productionRealization;
    public $performanceData;

    public function mount()
    {
        $this->setPeriod($this->selectedPeriod);
        $this->loadDashboardData();
    }

    public function setPeriod($period)
    {
        $this->selectedPeriod = $period;
        
        $fixedDate = Carbon::create(2025, 4, 3);
        
        switch($period) {
            case 'today':
                $this->startDate = $fixedDate->copy()->startOfDay();
                $this->endDate = $fixedDate->copy()->endOfDay(); // Ubah ke end of day
                break;
            case 'week':
                $this->startDate = $fixedDate->copy()->startOfWeek();
                $this->endDate = $fixedDate->copy()->endOfWeek();
                break;
            case 'month':
                $this->startDate = $fixedDate->copy()->startOfMonth();
                $this->endDate = $fixedDate->copy()->endOfMonth();
                break;
        }
    }   

    public function loadDashboardData()
    {
        $this->loadBasicData();
        $this->loadOeeData();
        $this->loadTargetRealization();
        $this->loadPerformanceData();
    }

    protected function loadBasicData()
    {
        // Get today's production data
        $this->todayProduction = Production::where('user_id', Auth::id())
            ->whereDate('created_at', $this->startDate)
            ->sum('total_production');

        $this->todayDefects = Production::where('user_id', Auth::id())
            ->whereDate('created_at', $this->startDate)
            ->sum('defect_count');

        // Get active production
        $this->activeProduction = Production::where('user_id', Auth::id())
            ->whereIn('status', ['running', 'paused', 'problem'])
            ->first();

        // Calculate total downtime
        $this->totalDowntime = ProductionDowntime::whereHas('production', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->whereBetween('start_time', [$this->startDate, $this->endDate])
        ->sum('duration_minutes');

        // Get recent downtimes
        $this->recentDowntimes = ProductionDowntime::whereHas('production', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->latest()
        ->take(5)
        ->get();

        // Get recent problems
        $this->recentProblems = ProductionProblem::whereHas('production', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->latest()
        ->take(5)
        ->get();
    }

    protected function loadOeeData()
    {
        $this->oeeData = OeeRecord::whereHas('production', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->latest()
            ->first();
    }

    protected function loadTargetRealization()
    {
        // Get latest production
        $production = Production::where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($production) {
            // Set target based on period
            switch($this->selectedPeriod) {
                case 'today':
                    $this->productionTarget = $production->target_per_shift;
                    break;
                case 'week':
                    $this->productionTarget = $production->target_per_shift * 7;
                    break;
                case 'month':
                    $this->productionTarget = $production->target_per_shift * 30;
                    break;
            }

            // Debug: tampilkan range tanggal yang digunakan
            Log::info('Date Range:', [
                'start' => $this->startDate,
                'end' => $this->endDate,
                'user_id' => Auth::id(),
                'product' => $production->product
            ]);

            // Get total production for the period
            $query = Production::where('user_id', Auth::id())
                ->where('product', $production->product)
                ->whereBetween('created_at', [$this->startDate, $this->endDate]);
            
            // Debug: tampilkan query yang dijalankan
            Log::info('SQL:', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);

            $this->productionRealization = $query->sum('total_production');
        } else {
            $this->productionTarget = 0;
            $this->productionRealization = 0;
        }
    }

    protected function loadPerformanceData()
    {
        $this->performanceData = OeeRecord::whereHas('production', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->select('date', 'oee_score', 'availability_rate', 'performance_rate', 'quality_rate')
            ->orderBy('date', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.karyawan.dashboard');
    }
}