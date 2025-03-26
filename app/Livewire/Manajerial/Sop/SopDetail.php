<?php

namespace App\Livewire\Manajerial\Sop;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Sop;
use App\Models\SopStep;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Services\CloudinaryService;

class SopDetail extends Component
{
    use WithFileUploads;

    // Basic properties
    public $sop;
    public $judul;
    public $deskripsi;
    public $urutan;
    public $gambar;
    public $editId;
    public $isEditing = false;
    public $showModal = false;
    public $stepToDelete;
    public $cloudinary_url;
    public $cloudinary_id;

    // Quality check properties
    public $nilai_standar;
    public $toleransi_min;
    public $toleransi_max;
    public $measurement_type;
    public $measurement_unit;
    public $interval_value;
    public $interval_unit;

    protected $cloudinaryService;

    public function boot(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    // Measurement types
    public $measurementTypes = [
        'length' => ['mm', 'cm', 'm'],
        'diameter' => ['mm', 'cm'],
        'weight' => ['g', 'kg'],
        'temperature' => ['°C', '°F'],
        'pressure' => ['Bar', 'PSI'],
        'angle' => ['degree'],
        'time' => ['s', 'min', 'hour'],
        'other' => ['unit']
    ];

    public $intervalUnits = [
        'pcs' => 'Pieces',
        'set' => 'Set',
        'box' => 'Box',
        'batch' => 'Batch',
        'hour' => 'Hour',
        'shift' => 'Shift'
    ];

    public function mount($id)
    {
        $this->sop = Sop::with(['steps', 'machine', 'product'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.manajerial.sop.sop-detail', [
            'steps' => $this->sop->steps()->orderBy('urutan')->get()
        ]);
    }

    public function updatedMeasurementType()
    {
        $this->measurement_unit = '';
    }

    protected function rules()
    {
        $rules = [
            'judul' => 'required',
            'deskripsi' => 'required',
            'urutan' => 'required|integer',
        ];

        if ($this->sop->kategori === 'quality') {
            $rules = array_merge($rules, [
                'nilai_standar' => 'required',
                'toleransi_min' => 'required',
                'toleransi_max' => 'required',
                'measurement_unit' => 'required',
                'interval_value' => 'required|integer|min:1',
                'interval_unit' => 'required|in:pcs,set,box,batch,hour,shift',
            ]);
        }

        return $rules;
    }

    public function removeImage()
    {
        if ($this->cloudinary_id) {
            $this->cloudinaryService->delete($this->cloudinary_id);
            $this->cloudinary_url = null;
            $this->cloudinary_id = null;
        }
    }

    public function store()
    {
        $this->validate($this->rules());

        try {
            $data = [
                'judul' => $this->judul,
                'urutan' => $this->urutan,
                'deskripsi' => $this->deskripsi,
                'gambar_path' => $this->cloudinary_url,
                'cloudinary_id' => $this->cloudinary_id
            ];

            if ($this->sop->kategori === 'quality') {
                $data = array_merge($data, [
                    'needs_standard' => true,
                    'nilai_standar' => $this->nilai_standar,
                    'toleransi_min' => $this->toleransi_min,
                    'toleransi_max' => $this->toleransi_max,
                    'measurement_type' => $this->measurement_type,
                    'measurement_unit' => $this->measurement_unit,
                    'interval_value' => $this->interval_value,
                    'interval_unit' => $this->interval_unit
                ]);
            }

            $this->sop->steps()->create($data);

            $this->reset(['judul', 'urutan', 'deskripsi', 
                         'cloudinary_url', 'cloudinary_id',
                         'nilai_standar', 'toleransi_min', 'toleransi_max',
                         'measurement_type', 'measurement_unit', 
                         'interval_value', 'interval_unit']);
            $this->closeModal();
            session()->flash('success', 'Step berhasil ditambahkan');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function openModal()
    {
        $this->reset(['judul', 'deskripsi', 'urutan', 'cloudinary_url', 'cloudinary_id',
                     'nilai_standar', 'toleransi_min', 'toleransi_max', 'measurement_unit',
                     'interval_value', 'interval_unit']);
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->isEditing = false;
    }

    public function edit($id)
    {
        $step = SopStep::find($id);
        $this->editId = $id;
        $this->judul = $step->judul;
        $this->deskripsi = $step->deskripsi;
        $this->urutan = $step->urutan;
        $this->cloudinary_url = $step->gambar_path;
        $this->cloudinary_id = $step->cloudinary_id;
        
        if ($this->sop->kategori === 'quality') {
            $this->nilai_standar = $step->nilai_standar;
            $this->toleransi_min = $step->toleransi_min;
            $this->toleransi_max = $step->toleransi_max;
            $this->measurement_unit = $step->measurement_unit;
            $this->interval_value = $step->interval_value;
            $this->interval_unit = $step->interval_unit;
        }
        
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate($this->rules());

        try {
            $step = $this->sop->steps()->find($this->editId);
            
            $data = [
                'judul' => $this->judul,
                'urutan' => $this->urutan,
                'deskripsi' => $this->deskripsi,
            ];

            if ($this->cloudinary_url) {
                $data['gambar_path'] = $this->cloudinary_url;
                $data['cloudinary_id'] = $this->cloudinary_id;
            }

            if ($this->sop->kategori === 'quality') {
                $data = array_merge($data, [
                    'needs_standard' => true,
                    'nilai_standar' => $this->nilai_standar,
                    'toleransi_min' => $this->toleransi_min,
                    'toleransi_max' => $this->toleransi_max,
                    'measurement_type' => $this->measurement_type,
                    'measurement_unit' => $this->measurement_unit,
                    'interval_value' => $this->interval_value,
                    'interval_unit' => $this->interval_unit
                ]);
            }

            $step->update($data);

            $this->reset(['judul', 'urutan', 'deskripsi', 'editId',
                         'cloudinary_url', 'cloudinary_id',
                         'nilai_standar', 'toleransi_min', 'toleransi_max',
                         'measurement_type', 'measurement_unit', 
                         'interval_value', 'interval_unit']);
            $this->closeModal();
            session()->flash('success', 'Step berhasil diupdate');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->stepToDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('deleteConfirmed')]
    public function delete()
    {
        $step = SopStep::find($this->stepToDelete);
        
        if ($step->cloudinary_id) {
            $this->cloudinaryService->delete($step->cloudinary_id);
        }
        
        $step->delete();
        $this->stepToDelete = null;
        
        $this->dispatch('deleted');
        session()->flash('success', 'Langkah SOP berhasil dihapus');
    }

    public function submitForApproval()
    {
        if ($this->sop->steps->isEmpty()) {
            session()->flash('error', 'Tidak dapat submit SOP tanpa langkah-langkah');
            return;
        }

        $this->sop->update([
            'approval_status' => 'pending',
            'submitted_at' => now(),
            'submitted_by' => Auth::user()
        ]);

        session()->flash('success', 'SOP berhasil diajukan untuk persetujuan');
    }

    public function toggleActive()
    {
        $this->sop->update([
            'is_active' => !$this->sop->is_active
        ]);

        $status = $this->sop->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "SOP berhasil {$status}");
    }
}