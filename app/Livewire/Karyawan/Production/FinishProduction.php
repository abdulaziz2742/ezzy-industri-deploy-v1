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
use App\Models\NGReport;

class FinishProduction extends Component
{
    use OeeAlertTrait;
    
    public $production;
    public $totalProduction = 0;
    public $defectCount = 0;
    public $defectType; 
    public $notes;
    public $totalQualityNG = 0;
    public $totalReject = 0; // Add this property declaration
    
    protected $rules = [
        'totalProduction' => 'required|numeric|min:1',
    ];


    public function mount($productionId)
    {
        $this->production = Production::findOrFail($productionId);
        
        // Hitung total NG dari quality checks
        $this->totalQualityNG = $this->production->qualityChecks()
            ->sum('defect_count');
            
        // Set nilai awal total reject sama dengan total NG
        $this->totalReject = $this->totalQualityNG;
    }

    protected function rules()
    {
        return [
            'totalProduction' => 'required|numeric|min:1',
            'totalReject' => [
                'required',
                'numeric',
                'min:' . $this->totalQualityNG, // Minimal harus sama dengan total NG
            ],
        ];
    }

    protected $messages = [
        'totalProduction.required' => 'Total production is required',
        'totalProduction.numeric' => 'Total production must be a number',
        'totalProduction.min' => 'Total production must be at least 1',
        'totalReject.min' => 'Total reject tidak boleh kurang dari total NG Quality Check (:min)',
    ];

    public $showNGReportModal = false;
    // Hapus product_code dari array ngReport
    public $ngReport = [
        'date' => '',
        'operator_name' => '',
        'employee_id' => '',
        'machine_name' => '',
        'shift' => '',
        'batch_number' => '',
        'product_name' => '',
        // hapus product_code dari sini
        'total_production' => 0,
        'total_ng' => 0,
        'ng_percentage' => '',
        'ng_type' => '',
        'ng_type_other' => '',
        'what' => '',
        'why' => '',
        'where' => '',
        'when' => '',
        'who' => '',
        'how' => '',
        'countermeasure' => '',
        'preventive_action' => '',
        'pic' => '',
        'verified_by' => '',
        'status' => ''
    ];

    public function showNGReportForm()
    {
        $user = Auth::user();
        $machine = Machine::find($this->production->machine_id);
        
        $this->ngReport['date'] = now()->format('Y-m-d');
        $this->ngReport['operator_name'] = $user->name;
        $this->ngReport['employee_id'] = $user->employee_id;
        $this->ngReport['machine_name'] = $machine ? $machine->name : '-';
        $this->ngReport['shift'] = $this->production->shift->id;
        $this->ngReport['batch_number'] = $this->production->batch_number;
        $this->ngReport['product_name'] = $this->production->product;
        // hapus baris product_code
        $this->ngReport['total_production'] = $this->totalProduction;
        $this->ngReport['total_ng'] = $this->totalReject;
        $this->ngReport['ng_percentage'] = number_format(($this->totalReject / $this->totalProduction) * 100, 2) . '%';
        
        $this->showNGReportModal = true;
        $this->dispatch('show-ng-report-modal');
    }

    public function saveNGReport()
    {
        $this->validate([
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
            'ngReport.status' => 'required',
        ]);
    
        // Simpan ke database
        NGReport::create([
            'production_id' => $this->production->id,
            'date' => $this->ngReport['date'],
            'operator_name' => $this->ngReport['operator_name'],
            'employee_id' => $this->ngReport['employee_id'],
            'machine_name' => $this->ngReport['machine_name'],
            'shift' => $this->ngReport['shift'],
            'batch_number' => $this->ngReport['batch_number'],
            'product_name' => $this->ngReport['product_name'],
            // hapus product_code
            'total_production' => $this->ngReport['total_production'],
            'total_ng' => $this->ngReport['total_ng'],
            'ng_percentage' => str_replace('%', '', $this->ngReport['ng_percentage']),
            'ng_type' => $this->ngReport['ng_type'],
            'ng_type_other' => $this->ngReport['ng_type_other'],
            'what' => $this->ngReport['what'],
            'why' => $this->ngReport['why'],
            'where' => $this->ngReport['where'],
            'when' => $this->ngReport['when'],
            'who' => $this->ngReport['who'],
            'how' => $this->ngReport['how'],
            'countermeasure' => $this->ngReport['countermeasure'],
            'preventive_action' => $this->ngReport['preventive_action'],
            'pic' => $this->ngReport['pic'],
            'verified_by' => $this->ngReport['verified_by'],
            'status' => $this->ngReport['status']
        ]);
    
        $this->showNGReportModal = false;
        $this->dispatch('hide-ng-report-modal');
        
        // Lanjut ke proses finish production
        $this->finishProduction();
    }

    public function cancelNGReport()
    {
        $this->showNGReportModal = false;
        $this->dispatch('hide-ng-report-modal');
    }

    public function finish()
    {
        $this->validate();

        if ($this->totalReject > 0) {
            $this->showNGReportForm();
            return;
        }

        $this->finishProduction();
    }

    protected function finishProduction()
    {
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
            // Pastikan perhitungan OEE benar
            $availabilityRate = $operatingTime > 0 ? ($operatingTime / $plannedTime) * 100 : 0;
            $performanceRate = $operatingTime > 0 ? ($this->totalProduction * $this->production->cycle_time / $operatingTime) * 100 : 0;
            $qualityRate = $this->totalProduction > 0 ? (($this->totalProduction - $totalDefects) / $this->totalProduction) * 100 : 0;
            
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
                // Saat mengirim notifikasi
                if ($machine && $oeeScore < $machine->oee_target && $machine->alert_enabled) {
                    if ($machine->alert_email) {
                        Notification::route('mail', $machine->alert_email)
                            ->notify(new OeeAlertNotification(
                                $machine,
                                round($oeeScore, 2), // Round untuk presisi 2 angka
                                $machine->oee_target,
                                $this->production->id
                            ));
                    }
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