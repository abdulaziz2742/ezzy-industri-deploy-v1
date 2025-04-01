<?php

namespace App\Livewire\Components;

use App\Models\MaintenanceTask;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;  
use App\Models\Production;
use App\Models\Machine;
use App\Models\ProductionCheck;     // Add this
use App\Models\ProductionSopCheck;  // Add this
use App\Models\ChecksheetEntry;
use App\Models\Sop;
use Illuminate\Support\Facades\Redirect;
use App\Models\Shift;
use App\Models\OeeRecord;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class ChecksheetTable extends Component
{
    use WithFileUploads;
    public $tasks;
    public $machineId;
    public $shiftId;
    public $machineSop;
    public $sopResults = [];
    public $checkResults = [];  // Add this
    public $notes = [];        // Add this
    public $photos = [];       // Add this
    public $previewUrl = [];
    
    
    public function mount($machineId, $shiftId)
    {
        $this->machineId = $machineId;
        $this->shiftId = $shiftId;
        
        // Get tasks based on frequency
        $this->tasks = $this->getTasksByFrequency();
        
        // Load SOP for this machine
        $this->machineSop = Sop::where('machine_id', $machineId)
                              ->where('is_active', true)
                              ->with(['steps' => function($query) {
                                  $query->where('is_checkpoint', true)
                                       ->orderBy('urutan');
                              }])
                              ->latest()
                              ->first();
    }

    private function getTasksByFrequency()
    {
        $tasks = MaintenanceTask::where('machine_id', $this->machineId)
            ->where('is_active', true)
            ->get();
    
        return $tasks->filter(function($task) {
            $lastCheck = ChecksheetEntry::where('task_id', $task->id)
                ->latest()
                ->first();
    
            $shouldShow = true;
            
            if ($lastCheck) {
                $lastCheckDate = $lastCheck->created_at;
                $today = now();
                
                switch ($task->frequency) {
                    case 'weekly':
                        $shouldShow = $lastCheckDate->diffInWeeks($today) >= 1;
                        Log::info("Weekly Task {$task->task_name}: Last check was on {$lastCheckDate}. Should show: " . ($shouldShow ? 'Yes' : 'No'));
                        break;
                        
                    case 'monthly':
                        $shouldShow = $lastCheckDate->diffInMonths($today) >= 1;
                        Log::info("Monthly Task {$task->task_name}: Last check was on {$lastCheckDate}. Should show: " . ($shouldShow ? 'Yes' : 'No'));
                        break;
                        
                    case 'daily':
                        $shouldShow = $lastCheckDate->diffInDays($today) >= 1;
                        Log::info("Daily Task {$task->task_name}: Last check was on {$lastCheckDate}. Should show: " . ($shouldShow ? 'Yes' : 'No'));
                        break;
                }
            } else {
                Log::info("Task {$task->task_name}: No previous check found. Showing task.");
            }
            
            return $shouldShow;
        });
    }

    public function render()
    {
        $sopCheckpoints = collect([]); 
    
        if ($this->machineSop) {
            $sopCheckpoints = $this->machineSop->steps ?? collect([]);
        }
    
        return view('livewire.components.checksheet-table', [
            'tasks' => $this->tasks,
            'maintenanceTasks' => $this->tasks, // keep this for backward compatibility
            'sopCheckpoints' => $sopCheckpoints
        ]);
    }

    // Hapus method updatedPhotos karena kita menggunakan Alpine.js untuk upload

    public function startProduction()
    {
        try {
            Log::info('Starting checksheet validation');
            
            // Get pending production from session
            $pendingProduction = session('pending_production');
            if (!$pendingProduction) {
                throw new \Exception('Data produksi tidak ditemukan');
            }

            DB::beginTransaction();

            // Generate batch number
            $shift = Shift::find($pendingProduction['shift_id']);
            $product = Product::find($pendingProduction['product_id']);
            $batchNumber = Production::generateBatchNumber($shift, $product);

            // Create the production record
            $production = Production::create([
                'user_id' => Auth::id(),
                'machine_id' => $pendingProduction['machine_id'],
                'machine' => $pendingProduction['machine_name'],
                'product_id' => $pendingProduction['product_id'],
                'product' => $pendingProduction['product_name'],
                'product_code' => $product->code,  // Gunakan code, bukan product_code
                'shift_id' => $pendingProduction['shift_id'],
                'status' => 'running',
                'start_time' => now(),
                'planned_production_time' => $pendingProduction['planned_production_time'],
                'cycle_time' => $pendingProduction['cycle_time'],
                'target_per_shift' => $pendingProduction['target_per_shift'],
                'ideal_cycle_time' => $pendingProduction['cycle_time'],
                'batch_number' => $batchNumber
            ]);

            // Create initial OEE record
            OeeRecord::create([
                'production_id' => $production->id,
                'machine_id' => $pendingProduction['machine_id'],
                'shift_id' => $pendingProduction['shift_id'],
                'date' => now(),
                'planned_production_time' => $pendingProduction['planned_production_time'],
                'operating_time' => 0,
                'total_downtime' => 0,
                'total_output' => 0,
                'good_output' => 0,
                'ideal_cycle_time' => $pendingProduction['cycle_time'],
                'availability_rate' => 0,
                'performance_rate' => 0,
                'quality_rate' => 0,
                'oee_score' => 0
            ]);
    
            // Save checksheet entries
            foreach ($this->checkResults as $taskId => $result) {
                $task = MaintenanceTask::find($taskId);
                
                // Get shift start time for next check
                $shiftIds = json_decode($task->shift_ids);
                $shift = Shift::find($shiftIds[0]);
                $nextCheckTime = now()->addDay()->format('Y-m-d') . ' ' . $shift->start_time;
                $nextCheckDue = \Carbon\Carbon::parse($nextCheckTime)->subHour();
    
                $checksheetEntry = ChecksheetEntry::create([
                    'production_id' => $production->id,
                    'task_id' => $taskId,
                    'machine_id' => $this->machineId,
                    'shift_id' => $this->shiftId,
                    'user_id' => Auth::id(),
                    'result' => $result,
                    'notes' => $this->notes[$taskId] ?? null,
                    'cloudinary_url' => $photoData['url'] ?? null,
                    'cloudinary_id' => $photoData['public_id'] ?? null,
                ]);
    
                Log::info("Checksheet Entry Created", [
                    'task_name' => $task->task_name,
                    'production_id' => $production->id,
                    'result' => $result
                ]);
            }
        
            DB::commit();
            
            return redirect()->route('production.status');
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in production start: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memulai produksi');
            return null;
        }
    }

    public function getProgressPercentage()
    {
        if (!$this->tasks || $this->tasks->count() === 0) {
            return 0;
        }
        return min(100, round((count($this->checkResults) / $this->tasks->count()) * 100));
    }

    public function getProgressText()
    {
        return count($this->checkResults) . '/' . ($this->tasks?->count() ?? 0);
    }
    public function getProgressProperty()
    {
        if (!$this->tasks || $this->tasks->count() === 0) {
            return 0;
        }
        
        return round((count($this->checkResults) / $this->tasks->count()) * 100);
    }

    public function back()
    {
        return redirect()->route('production.start');
    }
}