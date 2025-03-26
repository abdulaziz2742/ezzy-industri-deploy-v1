<?php

namespace App\Livewire\Manajerial\Production;

use Livewire\Component;
use App\Models\ProductionProblem;
use Illuminate\Support\Facades\Log;

class ProblemApproval extends Component
{
    public function render()
    {
        $problems = ProductionProblem::with(['production', 'production.machine'])
            ->orderBy('reported_at', 'desc')
            ->get();

        // Add logging to check image URLs
        foreach($problems as $problem) {
            Log::info('Problem image data:', [
                'problem_id' => $problem->id,
                'cloudinary_url' => $problem->cloudinary_url,
                'cloudinary_id' => $problem->cloudinary_id
            ]);
        }

        return view('livewire.manajerial.production.problem-approval', [
            'problems' => $problems
        ]);
    }

    public function approve($problemId)
    {
        $problem = ProductionProblem::find($problemId);
        if ($problem) {
            $problem->status = 'approved';
            $problem->approved_at = now();
            $problem->save();
            
            $this->dispatch('problem-count-updated');
        }
    }

    public function reject($problemId)
    {
        $problem = ProductionProblem::find($problemId);
        if ($problem) {
            $problem->status = 'rejected';
            $problem->resolved_at = now();
            $problem->save();
            
            $problem->production->update(['status' => 'running']);
            $this->dispatch('problem-count-updated');
        }
    }
}