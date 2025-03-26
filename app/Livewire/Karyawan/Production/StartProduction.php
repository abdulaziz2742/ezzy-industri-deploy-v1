<?php

namespace App\Livewire\Karyawan\Production;

use App\Models\Production;
use App\Models\Machine;
use App\Models\Shift;
use App\Models\MaintenanceTask;
use App\Models\Sop;
use App\Models\OeeRecord;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;  // Tambahkan ini di bagian atas
use Illuminate\Support\Facades\Log;

class StartProduction extends Component
{
    public $selectedMachine;
    public $selectedShift;
    public $product;
    public $machineSop;
    public $showChecksheet = false;  // Add this line
    public $product_id;  // Ganti dari $product


    public function updatedSelectedMachine($value)
    {
        if ($value) {
            $this->machineSop = Sop::where('machine_id', $value)
                                  ->where('is_active', true)
                                  ->with('steps')
                                  ->latest()
                                  ->first();
        } else {
            $this->machineSop = null;
        }
    }

    public function mount()
    {
        // Check active production first
        $activeProduction = Production::where('user_id', Auth::id())
            ->whereIn('status', ['running', 'problem', 'waiting_approval', 'paused'])
            ->first();

        if ($activeProduction) {
            return $this->redirect(route('production.status'), navigate: true);
        }

        // Get current shift
        $currentShift = Shift::getCurrentShift();
        if (!$currentShift) {
            session()->flash('error', 'Tidak ada shift yang aktif saat ini');
            return;
        }

        // Store shift info in session
        session([
            'current_shift' => [
                'id' => $currentShift->id,
                'name' => $currentShift->name
            ]
        ]);
    }

    protected $rules = [
        'selectedMachine' => 'required',
        'selectedShift' => 'required',
        'product_id' => 'required|exists:products,id'
    ];

    public $qualitySop;

    public function updatedProductId($value)
    {
        if ($value) {
            $this->qualitySop = Sop::where('product_id', $value)
                                  ->where('kategori', 'quality')
                                  ->where('is_active', true)
                                  ->with('steps')
                                  ->latest()
                                  ->first();
        } else {
            $this->qualitySop = null;
        }
    }

    public function startProduction()
    {
        $this->validate();
    
        try {
            Log::info("Validating production data");
            
            // Add duplicate check
            $existingProduction = Production::where('user_id', Auth::id())
                ->where('machine_id', $this->selectedMachine)
                ->where('shift_id', $this->selectedShift)
                ->whereDate('start_time', now())
                ->whereIn('status', ['running', 'problem', 'waiting_approval', 'paused'])
                ->first();
    
            if ($existingProduction) {
                throw new \Exception('Sudah ada produksi aktif untuk mesin dan shift ini');
            }
    
            $machine = Machine::find($this->selectedMachine);
            $product = Product::find($this->product_id);
            $shift = Shift::find($this->selectedShift);
            
            if (!$machine || !$product || !$shift) {
                throw new \Exception('Data tidak lengkap');
            }

            // Store production data in session
            session([
                'pending_production' => [
                    'machine_id' => $this->selectedMachine,
                    'machine_name' => $machine->name,
                    'shift_id' => $this->selectedShift,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'target_per_shift' => $product->target_per_shift ?? 0,
                    'cycle_time' => $product->cycle_time ?? 0,
                    'planned_production_time' => $shift->planned_operation_time,
                    'quality_sop_id' => $this->qualitySop ? $this->qualitySop->id : null
                ]
            ]);
    
            return $this->redirect(
                route('production.checksheet', [
                    'machineId' => $this->selectedMachine,
                    'shiftId' => $this->selectedShift
                ])
            );

        } catch (\Exception $e) {
            Log::error("Error validating production: " . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        return view('livewire.karyawan.production.start-production', [
            'machines' => Machine::where('status', 'active')->get(),
            'shifts' => Shift::all(),
            'products' => Product::orderBy('name')->get()  // Perbaiki ini
        ]);
    }
}