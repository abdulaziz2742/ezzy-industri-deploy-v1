<div>
    <div class="pagetitle">
        <h1>Maintenance Tasks Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Maintenance Tasks</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Maintenance Tasks List</h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal">
                                <i class="bi bi-plus-circle me-1"></i> Add New Task
                            </button>
                        </div>

                        <!-- Search -->
                        <div class="mb-3">
                            <input wire:model.live="search" type="text" class="form-control" placeholder="Search tasks...">
                        </div>

                        <!-- Tasks Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Machine</th>
                                        <th>Type</th>
                                        <th>Task Name</th>
                                        <th>Frequency</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                    <tr>
                                        <td>{{ $task->machine->name }}</td>
                                        <td>{{ strtoupper($task->maintenance_type) }}</td>
                                        <td>{{ $task->task_name }}</td>
                                        <td>{{ ucfirst($task->frequency) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $task->is_active ? 'success' : 'danger' }}">
                                                {{ $task->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button wire:click="edit({{ $task->id }})" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button wire:click="delete({{ $task->id }})" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $tasks->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Task Modal -->
    <div wire:ignore.self class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditing ? 'Edit' : 'Add' }} Maintenance Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Machine</label>
                                <select wire:model="selectedMachine" class="form-select">
                                    <option value="">Select Machine</option>
                                    @foreach($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedMachine') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Maintenance Type</label>
                                <select wire:model="maintenanceType" class="form-select">
                                    <option value="am">AM</option>
                                    <option value="pm">PM</option>
                                </select>
                                @error('maintenanceType') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Task Name</label>
                            <input type="text" wire:model="taskName" class="form-control">
                            @error('taskName') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea wire:model="description" class="form-control" rows="3"></textarea>
                            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Standard Value</label>
                            <input type="text" wire:model="standardValue" class="form-control">
                            @error('standardValue') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Frequency</label>
                                <select wire:model="frequency" class="form-select">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                                @error('frequency') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Preferred Time</label>
                                <input type="time" wire:model="preferredTime" class="form-control">
                                @error('preferredTime') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Shifts</label>
                            <div class="row">
                                @foreach($shifts as $shift)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                wire:model="shiftIds" 
                                                value="{{ $shift->id }}"
                                                id="shift{{ $shift->id }}">
                                            <label class="form-check-label" for="shift{{ $shift->id }}">
                                                {{ $shift->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('shiftIds') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="requiresPhoto" id="requiresPhoto">
                                <label class="form-check-label" for="requiresPhoto">
                                    Requires Photo Evidence
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="isActive" id="isActive">
                                <label class="form-check-label" for="isActive">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">
                                {{ $isEditing ? 'Update' : 'Create' }} Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>