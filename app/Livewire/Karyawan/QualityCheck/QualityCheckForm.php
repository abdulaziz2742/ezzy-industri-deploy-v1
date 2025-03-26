<?php

namespace App\Livewire\Karyawan\QualityCheck;

use Livewire\Component;
use App\Models\Production;
use App\Models\QualityCheck;
use App\Models\QualityCheckDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use App\Models\Sop;
use App\Models\ProductionSopCheck;
use Livewire\Attributes\On;
use App\Models\OeeRecord;

class QualityCheckForm extends Component
{
    // Basic properties
    public $production;
    public $productionId;
    public $stepId; // Add this property declaration
    public $sampleSize = 1;
    public $notes;
    public $parameters = [];
    public $sop;
    public $measurements = [];
    
    // NG-related properties
    public $hasNG = false;
    public $showNGModal = false;
    public $ngData = [
        'count' => 0,
        'type' => '',
        'notes' => '',
        'step_id' => null
    ];

    // Ganti protected $listeners dengan #[On] attribute untuk Livewire v3
    #[On('showNGForm')]
    public function showNGModal()
    {   
        $this->showNGModal = true;
        $this->dispatchBrowserEvent('show-ng-modal');
    }

    public function cancelNG()
    {
    $this->showNGModal = false;
    $this->dispatch('closeNGModal');
    }
    
    public $qualityCheckHistory = [];
    public $totalChecksNeeded;
    public $currentProgress;
    
    public function mount($productionId = null, $stepId = null)
    {
        $this->productionId = $productionId;
        $this->stepId = $stepId; // Pastikan stepId diinisialisasi di sini
        $this->production = Production::with(['product', 'shift'])->findOrFail($productionId);
        $this->loadSop();
        $this->loadQualityCheckHistory();
    }

    public function checkMeasurement($stepId)
    {
        if (isset($this->measurements[$stepId])) {
            $step = $this->sop->steps->where('id', $stepId)->first();
            
            $measurement = trim($this->measurements[$stepId]);
            if ($measurement === '') {
                return;
            }
            
            // Ganti koma dengan titik untuk validasi numerik
            $numericValue = str_replace(',', '.', $measurement);
            if (!is_numeric($numericValue)) {
                return;
            }
            
            $value = floatval($numericValue);
            $min = floatval(str_replace(',', '.', $step->toleransi_min));
            $max = floatval(str_replace(',', '.', $step->toleransi_max));

            $epsilon = $value < 0.1 ? 0.00001 : 0.001;
            
            if (($value < ($min - $epsilon)) || ($value > ($max + $epsilon))) {
                $this->hasNG = true;
                $this->ngData['step_id'] = $stepId;
                // Tidak perlu menampilkan modal atau alert di sini
                // Hanya tandai bahwa ada nilai NG
            }
        }
    }
    

    public function render()
    {
        return view('livewire.karyawan.quality-check.quality-check-form');
    }

    #[On('openNgModal')] 
    public function openNgModal()
    {
        $this->showNGModal = true;
    }

    public function validateMeasurements()
    {
        Log::info('Starting measurement validation');
        
        // Modifikasi validasi untuk menerima format angka dengan koma
        $this->validate([
            'measurements.*' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Ganti koma dengan titik untuk validasi numerik
                    $numericValue = str_replace(',', '.', $value);
                    if (!is_numeric($numericValue)) {
                        $fail('Nilai harus berupa angka.');
                    }
                },
            ],
        ]);
        
        // Periksa apakah ada nilai di luar toleransi
        $hasNG = false;
        foreach ($this->measurements as $stepId => $measurement) {
            $step = $this->sop->steps->where('id', $stepId)->first();
            if (!$step) continue;
            
            $value = floatval(str_replace(',', '.', $measurement));
            $min = floatval(str_replace(',', '.', $step->toleransi_min));
            $max = floatval(str_replace(',', '.', $step->toleransi_max));
            
            $epsilon = $value < 0.1 ? 0.00001 : 0.001;
            
            if (($value < ($min - $epsilon)) || ($value > ($max + $epsilon))) {
                $hasNG = true;
                $this->ngData['step_id'] = $stepId;
                break;
            }
        }
        
        if ($hasNG) {
            $this->hasNG = true;
            // Hanya kirim event untuk menampilkan konfirmasi
            $this->dispatch('show-ng-modal');
            return;
        }
        
        // Jika tidak ada NG, simpan data
        $this->saveCheck();
    }

/**
 * Menyimpan data NG (Not Good/defect)
 */
public function saveNGData()
{
    Log::info('Saving NG data', $this->ngData);
    
    $this->validate([
        'ngData.count' => 'required|numeric|min:1',
        'ngData.type' => 'required|string',
        'ngData.notes' => 'required|string',
    ]);

    try {
        DB::beginTransaction();

        // Buat quality check baru untuk setiap pemeriksaan
        $qualityCheck = QualityCheck::create([
            'production_id' => $this->productionId,
            'user_id' => Auth::id(),
            'check_time' => now(),
            'sample_size' => $this->sampleSize,
            'status' => 'ng',
            'defect_count' => $this->ngData['count'],
            'defect_type' => $this->ngData['type'],
            'defect_notes' => $this->ngData['notes']
        ]);

        // Simpan detail pengukuran
        foreach ($this->measurements as $stepId => $value) {
            $step = $this->sop->steps->where('id', $stepId)->first();
            if ($step) {
                QualityCheckDetail::create([
                    'quality_check_id' => $qualityCheck->id,
                    'parameter' => $step->judul,
                    'standard_value' => $step->nilai_standar,
                    'measured_value' => $value,
                    'tolerance_min' => $step->toleransi_min,
                    'tolerance_max' => $step->toleransi_max,
                    'status' => $stepId == $this->ngData['step_id'] ? 'ng' : 'ok'
                ]);
            }
        }

        DB::commit();
        
        $this->showNGModal = false;
        $this->dispatch('closeNGModal');
        $this->redirect(route('production.status'));

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error saving NG data: " . $e->getMessage());
        session()->flash('error', 'Terjadi kesalahan saat menyimpan data NG');
    }
}


   
    public function loadSop()
    {
        $qualitySop = Sop::where('product_id', $this->production->product_id)
                         ->where('kategori', 'quality')
                         ->where('is_active', true)
                         ->where('approval_status', 'approved')
                         ->with(['steps' => function($query) {
                             $query->orderBy('urutan', 'asc');
                         }])
                         ->first();

        if ($qualitySop && $qualitySop->steps) {
            $this->sop = $qualitySop;
            $this->parameters = $qualitySop->steps
                ->map(function($step) {
                    return [
                        'name' => $step->judul,
                        'description' => $step->deskripsi,
                        'value' => null,
                        'standard' => $step->nilai_standar,
                        'min' => $step->toleransi_min,
                        'max' => $step->toleransi_max,
                        'unit' => $step->measurement_unit,
                        'type' => $step->measurement_type,
                        'cloudinary_url' => $step->cloudinary_url,
                        'cloudinary_id' => $step->cloudinary_id
                    ];
                })->toArray();
        }
    }



    public function loadQualityCheckHistory()
    {
        // Get the collection first
        $qualityChecks = QualityCheck::where('production_id', $this->productionId)
            ->with(['details' => function($query) {
                $query->orderBy('id', 'asc');
            }, 'user'])
            ->orderBy('check_time', 'desc')
            ->get();
            
        // Calculate progress before converting to array
        if ($this->sop) {
            $interval = $this->sop->interval ?? 10;
            $target = $this->sop->target ?? 80;
            $this->totalChecksNeeded = ceil($target / $interval);
            $this->currentProgress = $qualityChecks->count();
        }

        // Log the details while it's still a collection
        Log::info('Quality Check History loaded', [
            'production_id' => $this->productionId,
            'history_count' => $qualityChecks->count(),
            'details_count' => $qualityChecks->flatMap->details->count()
        ]);

        // Convert to array after using collection methods
        $this->qualityCheckHistory = $qualityChecks->toArray();
    }
    // Add this to refresh history after saving
    public function saveCheck()
    {
        try {
            DB::beginTransaction();
    
            // Reset hasNG flag dan cek ulang status berdasarkan measurements
            $this->hasNG = false;
            $hasNGMeasurements = false;
    
            // Cek setiap measurement
            foreach ($this->measurements as $stepId => $value) {
                $step = $this->sop->steps->where('id', $stepId)->first();
                if ($step) {
                    $measuredValue = $this->convertToDecimal($value);
                    $minValue = $this->convertToDecimal($step->toleransi_min);
                    $maxValue = $this->convertToDecimal($step->toleransi_max);
                    
                    $epsilon = $measuredValue < 0.1 ? 0.00001 : 0.001;
                    if ($measuredValue < ($minValue - $epsilon) || $measuredValue > ($maxValue + $epsilon)) {
                        $hasNGMeasurements = true;
                        break;
                    }
                }
            }
    
            // HAPUS bagian ini yang mencari existing check
            // $existingCheck = QualityCheck::where('production_id', $this->productionId)->first();
            
            // LANGSUNG buat quality check baru untuk setiap pemeriksaan
            $qualityCheck = QualityCheck::create([
                'production_id' => $this->productionId,
                'user_id' => Auth::id(),
                'check_time' => now(),
                'sample_size' => $this->sampleSize,
                'status' => $hasNGMeasurements ? 'ng' : 'ok',
                'notes' => $this->notes,
                'defect_count' => $hasNGMeasurements ? ($this->ngData['count'] ?? 0) : 0,
                'defect_type' => $hasNGMeasurements ? ($this->ngData['type'] ?? '') : null,
                'defect_notes' => $hasNGMeasurements ? ($this->ngData['notes'] ?? '') : null
            ]);
    
            // Simpan detail measurements
            foreach ($this->measurements as $stepId => $value) {
                $step = $this->sop->steps->where('id', $stepId)->first();
                
                if ($step) {
                    $measuredValue = $this->convertToDecimal($value);
                    $minValue = $this->convertToDecimal($step->toleransi_min);
                    $maxValue = $this->convertToDecimal($step->toleransi_max);
                    $standardValue = $this->convertToDecimal($step->nilai_standar);
                    
                    $epsilon = $measuredValue < 0.1 ? 0.00001 : 0.001;
                    $status = ($measuredValue >= ($minValue - $epsilon) && $measuredValue <= ($maxValue + $epsilon)) ? 'ok' : 'ng';
    
                    QualityCheckDetail::create([
                        'quality_check_id' => $qualityCheck->id,
                        'parameter' => $step->judul,
                        'standard_value' => $standardValue,
                        'measured_value' => $measuredValue,
                        'tolerance_min' => $minValue,
                        'tolerance_max' => $maxValue,
                        'status' => $status
                    ]);
                }
            }
    
            DB::commit();
            
            // Update OEE Record
            try {
                $oeeRecord = OeeRecord::where('production_id', $this->production->id)->first();
                if (!$oeeRecord) {
                    $oeeRecord = new OeeRecord();
                }
                $oeeRecord->updateFromProduction($this->production);
            } catch (\Exception $e) {
                Log::error('Error updating OEE record: ' . $e->getMessage());
            }
            
            session()->flash('success', 'Data pemeriksaan kualitas berhasil disimpan');
            return redirect()->route('production.status');
    
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Quality Check Error: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Tambahkan fungsi helper untuk konversi nilai desimal dengan presisi yang tepat
    private function convertToDecimal($value)
    {
        // Hapus semua spasi
        $value = trim($value);
        
        // Ganti koma dengan titik untuk format desimal
        $value = str_replace(',', '.', $value);
        
        // Pastikan nilai adalah numerik
        if (is_numeric($value)) {
            // Kembalikan nilai sebagai string untuk mempertahankan presisi
            return $value;
        }
        
        return '0'; // Default jika tidak valid
    }
    }