<?php

namespace App\Livewire\Manajerial;

use App\Models\Machine;
use App\Models\OeeRecord;
use Carbon\Carbon;
use Livewire\Component;

class OeeDetail extends Component
{
    public $machine;
    public $selectedPeriod = 'daily';
    public $averageAvailability;
    public $averagePerformance;
    public $averageQuality;
    public $oeeScore;
    public $lastUpdated;
    public $refreshInterval = 300000; // 5 menit dalam milidetik

    public function mount($machineId)
    {
        $this->machine = Machine::findOrFail($machineId);
        $this->loadData();
    }

    public function loadData()
    {
        $query = OeeRecord::where('machine_id', $this->machine->id);

        switch ($this->selectedPeriod) {
            case 'daily':
                $query->whereDate('date', Carbon::today());
                break;
            case 'weekly':
                $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('date', Carbon::now()->month)
                      ->whereYear('date', Carbon::now()->year);
                break;
        }

        $records = $query->get();

        // Hitung rata-rata
        $this->averageAvailability = $records->avg('availability_rate') ?? 0;
        $this->averagePerformance = $records->avg('performance_rate') ?? 0;
        $this->averageQuality = $records->avg('quality_rate') ?? 0;
        $this->oeeScore = ($this->averageAvailability * $this->averagePerformance * $this->averageQuality) / 10000;
        $this->lastUpdated = now()->format('H:i:s');
    }

    public function getChartData()
    {
        $records = OeeRecord::where('machine_id', $this->machine->id)
            ->when($this->selectedPeriod === 'daily', function($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this->selectedPeriod === 'weekly', function($query) {
                return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            })
            ->when($this->selectedPeriod === 'monthly', function($query) {
                return $query->whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year);
            })
            ->orderBy('created_at')
            ->get();
    
        return [
            'labels' => $records->pluck('created_at')->map(fn($date) => $date->format('H:i')),
            'availability' => $records->pluck('availability_rate'),
            'performance' => $records->pluck('performance_rate'),
            'quality' => $records->pluck('quality_rate'),
            'oee' => $records->pluck('oee_score')
        ];
    }

    public function render()
    {
        $chartData = $this->getChartData();
        $this->dispatch('updateChartData', $chartData);
        
        return view('livewire.manajerial.oee-detail', [
            'chartData' => $chartData,
            'machine' => $this->machine,
            'selectedPeriod' => $this->selectedPeriod,
            'averageAvailability' => $this->averageAvailability,
            'averagePerformance' => $this->averagePerformance,
            'averageQuality' => $this->averageQuality,
            'oeeScore' => $this->oeeScore,
            'lastUpdated' => $this->lastUpdated,
        ]);
    }
}