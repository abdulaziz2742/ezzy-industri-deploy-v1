<?php

namespace App\Livewire\Karyawan\Production;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Production;
use App\Models\ProductionProblem;
use App\Models\OeeRecord;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class ReportProblem extends Component
{
    use WithFileUploads;

    public $productionId;
    public $problemType;
    public $notes;
    public $cloudinary_url;
    public $cloudinary_id;
    
    public function mount($productionId = null)
    {
        $this->productionId = $productionId;
    }

    #[On('openProblemModal')] 
    public function openProblemModal()
    {
        $this->dispatch('show-problem-modal');
    }


    public function save()
    {
        $this->validate([
            'problemType' => 'required',
            'notes' => 'required',
        ]);
    
        Log::info('Saving problem report with image', [
            'cloudinary_url' => $this->cloudinary_url,
            'cloudinary_id' => $this->cloudinary_id
        ]);
    
        ProductionProblem::create([
            'production_id' => $this->productionId,
            'problem_type' => $this->problemType,
            'notes' => $this->notes,
            'cloudinary_url' => $this->cloudinary_url,
            'cloudinary_id' => $this->cloudinary_id,
            'status' => 'pending',
            'reported_at' => now()
        ]);

        // Update production status
        $production = Production::find($this->productionId);
        $production->update(['status' => 'problem']);

        // Update OEE Record secara real-time
        try {
            Log::info('Updating OEE record after problem reported', [
                'production_id' => $this->productionId,
                'problem_type' => $this->problemType
            ]);
            
            $oeeRecord = OeeRecord::where('production_id', $this->productionId)->first();
            if ($oeeRecord) {
                // Passing objek Production, bukan ID
                $oeeRecord->updateFromProduction($production);
            }
        } catch (\Exception $e) {
            Log::error('Error updating OEE record after problem reported: ' . $e->getMessage(), [
                'production_id' => $this->productionId
            ]);
        }

        $this->dispatch('closeModal'); // Ganti ke event yang sama dengan script JS
        $this->reset(['problemType', 'notes', 'cloudinary_url', 'cloudinary_id']);
    
        $this->dispatch('refresh-production-status');
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Problem berhasil dilaporkan dan menunggu approval'
        ]);
    }

    public function render()
    {
        return view('livewire.karyawan.production.report-problem');
    }
    
    // Tambahkan method untuk update properties
public function updated($property, $value)
{
    Log::info("Property updated", [
        'property' => $property,
        'value' => $value
    ]);
}
}

