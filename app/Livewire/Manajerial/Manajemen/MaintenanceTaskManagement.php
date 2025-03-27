<?php

namespace App\Livewire\Manajerial\Manajemen;

use Livewire\Component;
use App\Models\Machine;
use App\Models\Shift;
use App\Models\MaintenanceTask;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class MaintenanceTaskManagement extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $selectedMachine;
    public $maintenanceType = 'am';
    public $taskName;
    public $description;
    public $standardValue;
    public $requiresPhoto = false;
    public $frequency = 'daily';
    public $shiftIds = [];
    public $preferredTime;
    public $isActive = true;
    public $isEditing = false;
    public $editingId;

    protected $rules = [
        'selectedMachine' => 'required',
        'maintenanceType' => 'required|in:am,pm',
        'taskName' => 'required|string|max:255',
        'description' => 'nullable|string',
        'standardValue' => 'nullable|string',
        'requiresPhoto' => 'boolean',
        'frequency' => 'required|in:daily,weekly,monthly',
        'shiftIds' => 'required|array|min:1',
        'preferredTime' => 'nullable|date_format:H:i',
        'isActive' => 'boolean'
    ];

    public function create()
    {
        $this->validate();

        $shiftIds = array_map('intval', array_values($this->shiftIds));

        MaintenanceTask::create([
            'machine_id' => $this->selectedMachine,
            'maintenance_type' => $this->maintenanceType,
            'task_name' => $this->taskName,
            'description' => $this->description,
            'standard_value' => $this->standardValue,
            'requires_photo' => $this->requiresPhoto,
            'frequency' => $this->frequency,
            'shift_ids' => json_encode($shiftIds),
            'preferred_time' => $this->preferredTime,
            'is_active' => $this->isActive
        ]);

        $this->resetForm();
        session()->flash('message', 'Task maintenance berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $task = MaintenanceTask::findOrFail($id);
        $this->editingId = $id;
        $this->selectedMachine = $task->machine_id;
        $this->maintenanceType = $task->maintenance_type;
        $this->taskName = $task->task_name;
        $this->description = $task->description;
        $this->standardValue = $task->standard_value;
        $this->requiresPhoto = $task->requires_photo;
        $this->frequency = $task->frequency;
        $this->shiftIds = json_decode($task->shift_ids) ?? []; 
        $this->preferredTime = $task->preferred_time;
        $this->isActive = $task->is_active;
        $this->isEditing = true;
    }

    public function update()
    {
        $this->validate();

        $shiftIds = array_map('intval', array_values($this->shiftIds));

        $task = MaintenanceTask::findOrFail($this->editingId);
        $task->update([
            'machine_id' => $this->selectedMachine,
            'maintenance_type' => $this->maintenanceType,
            'task_name' => $this->taskName,
            'description' => $this->description,
            'standard_value' => $this->standardValue,
            'requires_photo' => $this->requiresPhoto,
            'frequency' => $this->frequency,
            'shift_ids' => json_encode($shiftIds),
            'preferred_time' => $this->preferredTime,
            'is_active' => $this->isActive
        ]);

        $this->resetForm();
        session()->flash('message', 'Task maintenance berhasil diupdate!');
    }

    public function delete($id)
    {
        MaintenanceTask::findOrFail($id)->delete();
        session()->flash('message', 'Task maintenance berhasil dihapus!');
    }

    public function resetForm()
    {
        $this->selectedMachine = '';
        $this->maintenanceType = 'am';
        $this->taskName = '';
        $this->description = '';
        $this->standardValue = '';
        $this->requiresPhoto = false;
        $this->frequency = 'daily';
        $this->shiftIds = [];
        $this->preferredTime = '';
        $this->isActive = true;
        $this->isEditing = false;
        $this->editingId = null;
    }

    public function render()
    {
        $tasks = MaintenanceTask::query()
            ->when($this->search, function($query) {
                $query->where('task_name', 'like', '%' . $this->search . '%');
            })
            ->with('machine')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        return view('livewire.manajerial.manajemen.maintenance-task-management', [  // Changed from Manajerial to manajerial
            'tasks' => $tasks,
            'machines' => Machine::all(),
            'shifts' => Shift::all(),
        ]);
    }
}