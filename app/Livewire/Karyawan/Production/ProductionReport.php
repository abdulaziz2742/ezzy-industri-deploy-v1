<?php

namespace App\Livewire\Production;

use Livewire\Component;
use App\Models\Production;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log; 


class ProductionReport extends Component
{
    public $production;
    public $productionId;

    public function mount($productionId)
    {
        $this->productionId = $productionId;
        $this->production = Production::with([
            'machine',
            'checksheetEntries.task',
            'problems',
            'productionDowntimes'  // pastikan relasi ini ada
        ])->findOrFail($productionId);
    }

    public function downloadPdf()   
    {
        $pdf = Pdf::loadView('livewire.production.production-report-pdf', [
            'production' => $this->production
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'production-report-' . $this->productionId . '.pdf');
    }

    public function render()
    {
        return view('livewire.production.production-report');
    }
}