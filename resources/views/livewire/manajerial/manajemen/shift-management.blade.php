<div>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Manajemen Shift</h1>
            <button class="btn btn-primary" wire:click="createShift">Tambah Shift</button>
        </div>

        <!-- Shift List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Shift</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($shifts as $shift)
<tr>
    <td>{{ $shift->name }}</td>
    <td>{{ $shift->start_time }}</td>
    <td>{{ $shift->end_time }}</td>
    <td>
    <span class="badge bg-{{ $shift->status === 'active' ? 'success' : 'danger' }}">
        {{ $shift->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
    </span>
    </td>
    <td>
        <button class="btn btn-sm btn-warning" wire:click="editShift({{ $shift->id }})">Edit</button>
        <button class="btn btn-sm btn-danger" wire:click="deleteShift({{ $shift->id }})">Delete</button>
    </td>
</tr>
@endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editMode ? 'Edit Shift' : 'Tambah Shift' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveShift">
                        <div class="mb-3">
                            <label class="form-label">Nama Shift</label>
                            <input type="text" class="form-control" wire:model="form.name">
                            @error('form.name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" wire:model="form.start_time">
                            @error('form.start_time') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control" wire:model="form.end_time">
                            @error('form.end_time') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model="form.status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>