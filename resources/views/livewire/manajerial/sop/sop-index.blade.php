<div>
    <div class="pagetitle">
        <h1>Master SOP</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Master SOP</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $isEditing ? 'Edit SOP' : 'Tambah SOP Baru' }}</h5>
                        <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}">
                            <div class="mb-3">
                                <label class="form-label">No. SOP</label>
                                <input type="text" class="form-control" wire:model="no_sop" placeholder="Contoh: SOP-001">
                                @error('no_sop') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama SOP</label>
                                <input type="text" class="form-control" wire:model="nama">
                                @error('nama') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" wire:model.live="kategori">
                                    <option value="">Pilih Kategori</option>
                                    <option value="produksi">Produksi</option>
                                    <option value="quality">Quality Control</option>
                                    <option value="safety">Safety</option>
                                </select>
                                @error('kategori') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            @if($kategori == 'quality')
                            <div class="mb-3">
                                <label class="form-label">Produk</label>
                                <select class="form-select" wire:model="product_id">
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                @error('product_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            @if($kategori == 'produksi' || $kategori == 'safety')
                            <div class="mb-3">
                                <label class="form-label">Mesin</label>
                                <select class="form-select" wire:model="machine_id">
                                    <option value="">Pilih Mesin</option>
                                    @foreach($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                    @endforeach
                                </select>
                                @error('machine_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" wire:model="deskripsi" rows="3"></textarea>
                                @error('deskripsi') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Versi</label>
                                <input type="text" class="form-control" wire:model="versi" value="1.0" placeholder="Contoh: 1.0, 1.1, 2.0">
                                @error('versi') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ $isEditing ? 'Update' : 'Simpan' }}
                            </button>
                            @if($isEditing)
                                <button type="button" class="btn btn-secondary" wire:click="$set('isEditing', false)">
                                    Batal
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar SOP</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. SOP</th>
                                        <th>Nama SOP</th>
                                        <th>Kategori</th>
                                        <th>Dibuat Pada</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Status</th>
                                        <th>Disetujui Oleh</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sops as $sop)
                                    <tr>
                                        <td>{{ $sop->no_sop }}</td>
                                        <td>{{ $sop->nama }}</td>
                                        <td>{{ ucfirst($sop->kategori) }}</td>
                                        <td>{{ $sop->created_date ? $sop->created_date->format('d/m/Y H:i') : '-' }}</td>
                                        <td>{{ $sop->creator->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $sop->approval_status === 'approved' ? 'success' : 
                                                ($sop->approval_status === 'pending' ? 'warning' : 
                                                ($sop->approval_status === 'rejected' ? 'danger' : 'secondary')) 
                                            }}">
                                                {{ ucfirst($sop->approval_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $sop->approver->name ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('manajerial.sop.detail', $sop->id) }}" 
                                                   class="btn btn-sm btn-info" wire:navigate>
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($sop->approval_status === 'draft')
                                                <button class="btn btn-sm btn-warning" wire:click="edit({{ $sop->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" 
                                                        wire:click="confirmDelete({{ $sop->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Belum ada data SOP</td>
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
</div>