<?php

namespace App\Livewire\Manajerial;

use App\Models\Machine;
use App\Models\OeeRecord;
use App\Models\Shift;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class OeeDashboard extends Component
{
    public $startDate;
    public $endDate;
    public $selectedShift = '';
    public $machines = [];
    public $shifts = [];
    public $refreshInterval = 300000; // 5 minutes in milliseconds

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->machines = Machine::all();
        $this->shifts = Shift::all();
    }

    public function render()
    {
        $oeeRecords = $this->loadOeeData();
        
        return view('livewire.manajerial.oee-dashboard', [
            'oeeRecords' => $oeeRecords
        ]);
    }

    private function loadOeeData()
    {
        $query = OeeRecord::with(['machine', 'shift'])
            ->whereBetween('date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);

        if ($this->selectedShift) {
            $query->where('shift_id', $this->selectedShift);
        }

        return $query->get();
    }
}