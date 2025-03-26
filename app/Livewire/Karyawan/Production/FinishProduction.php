<?php

namespace App\Livewire\Karyawan\Production;

use Livewire\Component;
use App\Models\Production;
use App\Models\OeeRecord;
use App\Models\Machine;
use App\Traits\OeeAlertTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Shift;
use App\Notifications\OeeAlertNotification;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Notification;

class FinishProduction extends Component
{
    use OeeAlertTrait;
    
    public $production;
    public $totalProduction = 0;
    public $defectCount = 0;
    public $defectType; 
    public $notes;

    protected $rules = [
        'totalProduction' => 'required|numeric|min:1',
    ];

    protected $messages = [
        'totalProduction.required' => 'Total production is required',
        'totalProduction.numeric' => 'Total production must be a number',
        'totalProduction.min' => 'Total production must be at least 1',
    ];

    public function mount($productionId)
    {
        // Load production data
        $this->production = Production::findOrFail($productionId);
    }

    public function finish()
    {
        $this->validate();

        DB::transaction(function ($db) {
            // Change from downtimes() to productionDowntimes()
            $maintenanceDowntime = $this->production->productionDowntimes()
                ->where('reason', 'like', '%maintenance%')
                ->sum('duration_minutes') ?? 0;
            
            $totalDowntime = $this->production->productionDowntimes()->sum('duration_minutes');
            $plannedTime = $this->production->planned_production_time;
            $operatingTime = $plannedTime - $totalDowntime;
            
            $totalDefects = $this->production->qualityChecks()->sum('defect_count');
            $goodOutput = $this->totalProduction - $totalDefects;

            // Update production first
            $this->production->update([
                'total_production' => $this->totalProduction, // Pastikan field ini sesuai dengan fillable
                'defect_count' => $totalDefects,
                'status' => 'finished',
                'end_time' => now()
            ]);

            // Update OEE record
            $oeeRecord = OeeRecord::where('production_id', $this->production->id)->first();
            
            // Hitung rate
            $availabilityRate = ($operatingTime / $plannedTime) * 100;
            $performanceRate = ($this->totalProduction * $this->production->cycle_time / $operatingTime) * 100;
            $qualityRate = ($this->totalProduction > 0) ? (($this->totalProduction - $totalDefects) / $this->totalProduction) * 100 : 0;
            
            // Hitung OEE Score
            $oeeScore = ($availabilityRate * $performanceRate * $qualityRate) / 10000;

            $oeeRecord->update([
                'operating_time' => $operatingTime,
                'total_downtime' => $totalDowntime,
                'total_output' => $this->totalProduction,
                'good_output' => $goodOutput,
                'defect_count' => $totalDefects,
                'availability_rate' => $availabilityRate,
                'performance_rate' => $performanceRate,
                'quality_rate' => $qualityRate,
                'oee_score' => $oeeScore
            ]);

            // Check if OEE is below target and send notifications
            // Get machine object properly
            $machine = Machine::find($this->production->machine_id);
            if ($machine && $oeeScore < $machine->oee_target && $machine->alert_enabled) {
                Log::info('OEE below target, checking machine alerts', [
                    'machine' => $machine->name,
                    'oee_score' => $oeeScore,
                    'target' => $machine->oee_target,
                    'alert_phone' => $machine->alert_phone
                ]);

                // Send WhatsApp if phone is configured
                if ($machine->alert_phone) {
                    $whatsapp = new WhatsAppService();
                    $whatsapp->sendOeeAlert(
                        $machine->alert_phone,
                        $machine,
                        $oeeScore,
                        $machine->oee_target,
                        $this->production
                    );
                }

                // Send email if email is configured
                if ($machine->alert_email) {
                    Notification::route('mail', $machine->alert_email)
                        ->notify(new OeeAlertNotification(
                            $machine,
                            $oeeScore,
                            $machine->oee_target,
                            $this->production->id
                        ));
                }
            }
        });

        $this->dispatch('finish-success');
    }

    public function redirectToStart()
    {
        return redirect()->route('production.start');
    }

    public function render()
    {
        return view('livewire.karyawan.production.finish-production');
    }
}