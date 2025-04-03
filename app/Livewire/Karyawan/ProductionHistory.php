<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Production;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

class ProductionHistory extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $selectedProduction = null;
    public $startDate;
    public $endDate;
    public $selectedShift;
    public $selectedStatus;
    
    public function showDetail($productionId)
    {
        // Update this line to use productionDowntimes instead of downtimes
        $this->selectedProduction = Production::with(['productionDowntimes', 'problems', 'oeeRecord'])
            ->find($productionId);
    }
    
    public function closeDetail()
    {
        $this->selectedProduction = null;
    }
    
    #[Layout('components.layouts.app')]
    public function render()
    {
        $query = Production::where('user_id', Auth::id());
        
        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }
        if ($this->selectedShift) {
            $query->where('shift', $this->selectedShift);
        }
        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }
        
        return view('livewire.karyawan.production-history', [
            'productions' => $query->orderBy('created_at', 'desc')->paginate(10)
        ]);
    }
}
