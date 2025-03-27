<div>
    <div class="pagetitle">
        <h1>Manajemen Task Maintenance</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Task Maintenance</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Form Column -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $isEditing ? 'Edit Task' : 'Tambah Task Baru' }}</h5>
                        <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}">
                            <div class="mb-3">
                                <label class="form-label">Mesin</label>
                                <select class="form-select" wire:model="selectedMachine" required>
                                    <option value="">Pilih Mesin...</option>
                                    @foreach($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedMachine') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tipe Maintenance</label>
                                <select class="form-select" wire:model="maintenanceType" required>
                                    <option value="am">Autonomous Maintenance</option>
                                    <option value="pm">Preventive Maintenance</option>
                                </select>
                                @error('maintenanceType') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Task</label>
                                <input type="text" class="form-control" wire:model="taskName" required>
                                @error('taskName') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" wire:model="description" rows="3"></textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nilai Standar</label>
                                <input type="text" class="form-control" wire:model="standardValue">
                                @error('standardValue') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="requiresPhoto" id="requiresPhoto">
                                    <label class="form-check-label" for="requiresPhoto">
                                        Membutuhkan Foto
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Frekuensi</label>
                                <select class="form-select" wire:model="frequency" required>
                                    <option value="daily">Harian</option>
                                    <option value="weekly">Mingguan</option>
                                    <option value="monthly">Bulanan</option>
                                </select>
                                @error('frequency') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Shift</label>
                                @foreach($shifts as $shift)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               wire:model="shiftIds" 
                                               value="{{ $shift->id }}" 
                                               id="shift{{ $shift->id }}">
                                        <label class="form-check-label" for="shift{{ $shift->id }}">
                                            {{ $shift->name }}
                                        </label>
                                    </div>
                                @endforeach
                                @error('shiftIds') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Waktu Yang Direkomendasikan</label>
                                <input type="time" class="form-control" wire:model="preferredTime">
                                @error('preferredTime') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="isActive" id="isActive">
                                    <label class="form-check-label" for="isActive">
                                        Task Aktif
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    {{ $isEditing ? 'Update Task' : 'Tambah Task' }}
                                </button>
                                @if($isEditing)
                                    <button type="button" class="btn btn-secondary mt-2" wire:click="resetForm">
                                        Batal Edit
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table Column -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar Task Maintenance</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mesin</th>
                                        <th>Tipe</th>
                                        <th>Task</th>
                                        <th>Frekuensi</th>
                                        <th>Shift</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tasks as $task)
                                        <tr>
                                            <td>{{ $task->machine->name }}</td>
                                            <td>{{ strtoupper($task->maintenance_type) }}</td>
                                            <td>
                                                {{ $task->task_name }}
                                                @if($task->description)
                                                    <small class="d-block text-muted">{{ $task->description }}</small>
                                                @endif
                                            </td>
                                            <td>{{ ucfirst($task->frequency) }}</td>
                                            <td>
                                                @foreach($shifts as $shift)
                                                    @if(in_array($shift->id, json_decode($task->shift_ids) ?? []))
                                                        <span class="badge bg-info">{{ $shift->name }}</span>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="badge {{ $task->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $task->is_active ? 'Aktif' : 'Non-aktif' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" wire:click="edit({{ $task->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" 
                                                        wire:click="delete({{ $task->id }})"
                                                        onclick="return confirm('Yakin ingin menghapus task ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada task maintenance</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($tasks->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $tasks->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>