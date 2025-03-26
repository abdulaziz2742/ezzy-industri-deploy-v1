<div>
    <div class="pagetitle">
        <h1>Manajemen Mesin</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Mesin</li>
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
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Daftar Mesin</h5>
                            <button class="btn btn-primary" wire:click="createMachine">
                                <i class="bi bi-plus-lg"></i> Tambah Mesin
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Tipe</th>
                                        <th>Lokasi</th>
                                        <th>Target OEE</th>
                                        <th>Alert</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($machines as $machine)
                                        <tr>
                                            <td>{{ $machine->code }}</td>
                                            <td>{{ $machine->name }}</td>
                                            <td>{{ $machine->type }}</td>
                                            <td>{{ $machine->location }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ number_format($machine->oee_target, 2) }}%
                                                </span>
                                            </td>
                                            <td>
                                                @if($machine->alert_enabled)
                                                    <span class="badge bg-success" 
                                                          title="Email: {{ $machine->alert_email }}{{ $machine->alert_phone ? ', WA: '.$machine->alert_phone : '' }}">
                                                        <i class="bi bi-bell-fill"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-bell-slash"></i> Disabled
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $machine->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ $machine->status === 'active' ? 'Aktif' : 'Non-aktif' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" wire:click="editMachine({{ $machine->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $machine->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Belum ada data mesin</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editMode ? 'Edit Mesin' : 'Tambah Mesin Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="saveMachine">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Mesin</label>
                            <input type="text" class="form-control" wire:model="form.code" required>
                            @error('form.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Mesin</label>
                            <input type="text" class="form-control" wire:model="form.name" required>
                            @error('form.name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe</label>
                            <select class="form-select" wire:model="form.type" required>
                                <option value="">Pilih Tipe...</option>
                                <option value="Mesin">Mesin</option>
                                <option value="Aset Pendukung">Aset Pendukung</option>
                            </select>
                            @error('form.type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <input type="text" class="form-control" wire:model="form.location">
                            @error('form.location') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" wire:model="form.description" rows="3"></textarea>
                            @error('form.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model="form.status">
                                <option value="active">Aktif</option>
                                <option value="inactive">Non-aktif</option>
                            </select>
                            @error('form.status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <!-- Di dalam form modal -->
                        <div class="mb-3">
                            <label class="form-label">Target OEE (%)</label>
                            <input type="number" 
                                   class="form-control" 
                                   wire:model="form.oee_target" 
                                   step="0.01" 
                                   min="0" 
                                   max="100">
                            @error('form.oee_target') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       wire:model="form.alert_enabled" 
                                       id="alertEnabled">
                                <label class="form-check-label" for="alertEnabled">
                                    Aktifkan Alert OEE
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3" x-show="$wire.form.alert_enabled">
                            <label class="form-label">Email Alert</label>
                            <input type="email" 
                                   class="form-control" 
                                   wire:model="form.alert_email" 
                                   placeholder="supervisor@example.com">
                            @error('form.alert_email') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                        </div>
                        
                        <!-- Tambahkan field WhatsApp Alert -->
                        <div class="mb-3" x-show="$wire.form.alert_enabled">
                            <label class="form-label">WhatsApp Alert</label>
                            <input type="text" 
                                   class="form-control" 
                                   wire:model="form.alert_phone" 
                                   placeholder="628123456789">
                            <div class="form-text">Format: 628xxxxxxxxxx (tanpa tanda + atau spasi)</div>
                            @error('form.alert_phone') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus mesin ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Batal</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteMachine">Hapus</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>